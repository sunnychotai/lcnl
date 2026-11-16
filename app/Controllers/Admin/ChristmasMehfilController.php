<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MehfilBookingModel;
use App\Services\MehfilBookingService;
use Config\ChristmasMehfil;

/**
 * Back-office for Christmas Mehfil bookings: match bank transfers, chase and
 * cancel unpaid bookings, and export the list. ADMIN and EVENTS roles.
 *
 * Nothing is cancelled automatically. Bookings unpaid after 48 hours are
 * flagged as overdue for an admin to cancel.
 */
class ChristmasMehfilController extends BaseController
{
    private const BASE = 'admin/christmas-mehfil';

    protected ChristmasMehfil $config;
    protected MehfilBookingModel $bookings;
    protected MehfilBookingService $service;

    public function __construct()
    {
        $this->config   = config('ChristmasMehfil');
        $this->bookings = new MehfilBookingModel();
        $this->service  = new MehfilBookingService();
    }

    public function index()
    {
        $all = $this->bookings->orderBy('created_at', 'DESC')->findAll();

        // Flag repeat emails so double submissions are visible, not blocked:
        // a family may genuinely book twice.
        $seen = [];
        foreach (array_reverse(array_keys($all)) as $i) {
            $r   = &$all[$i];
            $key = strtolower(trim($r['email']));

            $r['is_duplicate'] = $r['status'] !== 'cancelled' && isset($seen[$key]);
            $r['is_overdue']   = $this->config->isOverdue($r);
            $r['people']       = $this->config->people((int) $r['individual_tickets'], (int) $r['tables']);

            if ($r['status'] !== 'cancelled') {
                $seen[$key] = true;
            }
            unset($r);
        }

        $active  = array_filter($all, static fn(array $r): bool => $r['status'] !== 'cancelled');
        $paid    = array_filter($active, static fn(array $r): bool => (bool) $r['payment_received']);
        $overdue = array_filter($active, static fn(array $r): bool => $r['is_overdue']);

        $filter = (string) $this->request->getGet('status');
        $rows   = match ($filter) {
            'awaiting'  => array_filter($active, static fn(array $r): bool => ! $r['payment_received']),
            'overdue'   => $overdue,
            'paid'      => $paid,
            'cancelled' => array_filter($all, static fn(array $r): bool => $r['status'] === 'cancelled'),
            default     => $all,
        };

        return view('admin/mehfil/index', [
            'title'    => 'Christmas Mehfil Bookings',
            'config'   => $this->config,
            'bookings' => $rows,
            'filter'   => in_array($filter, ['awaiting', 'overdue', 'paid', 'cancelled'], true) ? $filter : 'all',
            'stats'    => [
                'bookings'   => count($active),
                'people'     => array_sum(array_column($active, 'people')),
                'paidPeople' => array_sum(array_column($paid, 'people')),
                'individual' => array_sum(array_map('intval', array_column($active, 'individual_tickets'))),
                'tables'     => array_sum(array_map('intval', array_column($active, 'tables'))),
                'members'    => count(array_filter($active, static fn(array $r): bool => (bool) $r['is_member'])),
                'paid'       => count($paid),
                'awaiting'   => count($active) - count($paid),
                'overdue'    => count($overdue),
                'received'   => array_sum(array_column($paid, 'amount')),
                'expected'   => array_sum(array_column($active, 'amount')),
            ],
        ]);
    }

    public function edit(int $id)
    {
        $booking = $this->bookings->find($id);

        if (! $booking) {
            return redirect()->to(self::BASE)->with('error', 'Booking not found.');
        }

        return view('admin/mehfil/edit', [
            'title'   => 'Edit booking ' . $booking['booking_ref'],
            'config'  => $this->config,
            'booking' => $booking,
        ]);
    }

    /**
     * Admins can change the tickets and keep notes. The total is worked out
     * again at the prices fixed when the booking was made.
     */
    public function update(int $id)
    {
        $booking = $this->bookings->find($id);

        if (! $booking) {
            return redirect()->to(self::BASE)->with('error', 'Booking not found.');
        }

        $rules = [
            'individual_tickets' => 'required|is_natural|less_than_equal_to[99]',
            'tables'             => 'required|is_natural|less_than_equal_to[20]',
            'admin_notes'        => 'permit_empty|trim|max_length[5000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $individual = (int) $this->request->getPost('individual_tickets');
        $tables     = (int) $this->request->getPost('tables');

        if ($individual + $tables === 0) {
            return redirect()->back()->withInput()
                ->with('errors', ['A booking needs at least one ticket or table. To remove it, cancel it instead.']);
        }

        $amount = $individual * (float) $booking['individual_price'] + $tables * (float) $booking['table_price'];

        $this->bookings->update($id, [
            'individual_tickets' => $individual,
            'tables'             => $tables,
            'amount'             => $amount,
            'admin_notes'        => $this->request->getPost('admin_notes') ?: null,
        ]);

        $msg = 'Booking ' . $booking['booking_ref'] . ' updated.';
        if ((float) $booking['amount'] !== $amount) {
            $msg .= ' The total is now £' . number_format($amount) . ' (was £' . number_format((float) $booking['amount']) . ').';
        }

        return redirect()->to(self::BASE)->with('success', $msg);
    }

    /**
     * Toggle whether the transfer has been matched on the LCNL statement. The
     * first time a booking is marked paid, the booker is told it is confirmed.
     */
    public function togglePayment(int $id)
    {
        $booking = $this->bookings->find($id);

        if (! $booking || $booking['status'] === 'cancelled') {
            return redirect()->to(self::BASE)->with('error', 'Booking not found or cancelled.');
        }

        $name = $booking['first_name'] . ' ' . $booking['last_name'];

        if (empty($booking['payment_received'])) {
            $confirmed = $this->service->markPaid($booking, session()->get('admin_name') ?? 'Admin');

            return redirect()->to(self::BASE)
                ->with('success', $name . ' marked as paid.' . ($confirmed ? ' Confirmation email queued.' : ''));
        }

        $this->bookings->update($id, [
            'payment_received'    => 0,
            'payment_received_at' => null,
            'payment_marked_by'   => null,
            'status'              => 'booked',
        ]);

        return redirect()->to(self::BASE)->with('success', $name . ' marked as unpaid.');
    }

    public function remind(int $id)
    {
        $booking = $this->bookings->find($id);

        if (! $booking || $booking['status'] !== 'booked') {
            return redirect()->to(self::BASE)->with('error', 'Only unpaid bookings can be sent a reminder.');
        }

        $this->service->email($booking, 'mehfil_payment_reminder', 'Christmas Mehfil – payment reminder');

        $this->bookings->update($id, [
            'reminder_sent_at' => date('Y-m-d H:i:s'),
            'reminder_count'   => (int) $booking['reminder_count'] + 1,
        ]);

        return redirect()->to(self::BASE)
            ->with('success', 'Reminder queued for ' . $booking['first_name'] . ' ' . $booking['last_name'] . '.');
    }

    /** Frees the seats. Emails the booker unless the admin unticked "email the booker". */
    public function cancel(int $id)
    {
        $booking = $this->bookings->find($id);

        if (! $booking || $booking['status'] === 'cancelled') {
            return redirect()->to(self::BASE)->with('error', 'Booking not found or already cancelled.');
        }

        $this->bookings->update($id, ['status' => 'cancelled', 'cancelled_at' => date('Y-m-d H:i:s')]);

        $notify = (bool) $this->request->getPost('notify');
        if ($notify) {
            $this->service->email($booking, 'mehfil_booking_cancelled', 'Christmas Mehfil – your booking has been cancelled');
        }

        return redirect()->to(self::BASE)
            ->with('success', 'Booking ' . $booking['booking_ref'] . ' cancelled.' . ($notify ? ' Cancellation email queued.' : ''));
    }

    /** Reinstated bookings start unpaid; payment is re-marked explicitly. */
    public function reinstate(int $id)
    {
        $booking = $this->bookings->find($id);

        if (! $booking || $booking['status'] !== 'cancelled') {
            return redirect()->to(self::BASE)->with('error', 'Booking not found or not cancelled.');
        }

        $this->bookings->update($id, [
            'status'              => 'booked',
            'cancelled_at'        => null,
            'payment_received'    => 0,
            'payment_received_at' => null,
            'payment_marked_by'   => null,
        ]);

        return redirect()->to(self::BASE)->with('success', 'Booking ' . $booking['booking_ref'] . ' reinstated.');
    }

    /** Every booking, oldest first. */
    public function export()
    {
        $rows = $this->bookings->orderBy('created_at', 'ASC')->findAll();

        return $this->csv('christmas_mehfil_' . date('Ymd') . '.csv', [
            'Reference', 'First name', 'Surname', 'Email', 'Mobile', 'Postcode', 'LCNL member',
            'Individual tickets', 'Tables', 'People', 'Price band', 'Individual price', 'Table price', 'Amount',
            'Payment received', 'Received at', 'Marked by', 'Status', 'Pay by', 'Reminders sent',
            'Admin notes', 'Booked at',
        ], array_map(fn(array $r): array => [
            $r['booking_ref'], $r['first_name'], $r['last_name'], $r['email'], $r['phone'], $r['postcode'],
            $r['is_member'] ? 'Yes' : 'No',
            $r['individual_tickets'], $r['tables'], $this->config->people((int) $r['individual_tickets'], (int) $r['tables']),
            $r['price_band'] === 'early' ? 'Early bird' : 'Standard', $r['individual_price'], $r['table_price'], $r['amount'],
            $r['payment_received'] ? 'Yes' : 'No', $r['payment_received_at'], $r['payment_marked_by'], $r['status'],
            $this->config->payBy($r['created_at'])->format('Y-m-d H:i'), $r['reminder_count'],
            $r['admin_notes'], $r['created_at'],
        ], $rows));
    }

    /** At most a few hundred rows, so build the file in memory and return it. */
    private function csv(string $filename, array $header, array $rows)
    {
        $out = fopen('php://temp', 'w+');
        fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

        fputcsv($out, $header);
        foreach ($rows as $row) {
            // Neutralise spreadsheet formulas in booker-supplied text.
            fputcsv($out, array_map(static fn($v) => is_string($v) && preg_match('/^[=+\-@]/', $v) ? "'" . $v : $v, $row));
        }

        rewind($out);
        $body = stream_get_contents($out);
        fclose($out);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->setBody($body);
    }
}
