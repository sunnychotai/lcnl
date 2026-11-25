<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\StripeService;
use App\Models\ConnexionsAttendeeModel;
use App\Models\ConnexionsBookingModel;
use App\Services\ConnexionsBookingService;
use Config\Connexions;

/**
 * Back-office for Connexions bookings: the bookings and their attendees, a
 * printable door list, correcting attendee details and resending the
 * confirmation emails. ADMIN and EVENTS roles.
 *
 * Payment is by card, so bookings normally mark themselves paid. "In
 * checkout" bookings are holding places while the booker pays; "abandoned"
 * ones were never paid and hold nothing.
 */
class ConnexionsController extends BaseController
{
    private const BASE = 'admin/connexions';

    protected Connexions $config;
    protected ConnexionsBookingModel $bookings;
    protected ConnexionsAttendeeModel $attendees;
    protected ConnexionsBookingService $service;

    public function __construct()
    {
        $this->config    = config('Connexions');
        $this->bookings  = new ConnexionsBookingModel();
        $this->attendees = new ConnexionsAttendeeModel();
        $this->service   = new ConnexionsBookingService();
    }

    public function index()
    {
        $all       = $this->annotated();
        $paid      = array_filter($all, static fn(array $b): bool => $b['state'] === 'paid');
        $holding   = array_filter($all, static fn(array $b): bool => $b['state'] === 'checkout');
        $abandoned = array_filter($all, static fn(array $b): bool => $b['state'] === 'abandoned');
        $cancelled = array_filter($all, static fn(array $b): bool => $b['state'] === 'cancelled');

        $filter = (string) $this->request->getGet('status');
        $rows   = match ($filter) {
            'paid'      => $paid,
            'checkout'  => $holding,
            'abandoned' => $abandoned,
            'cancelled' => $cancelled,
            default     => $all,
        };

        return view('admin/connexions/index', [
            'title'    => 'Connexions Bookings',
            'config'   => $this->config,
            'bookings' => $rows,
            'filter'   => in_array($filter, ['paid', 'checkout', 'abandoned', 'cancelled'], true) ? $filter : 'all',
            'stats'    => [
                'girls'     => array_sum(array_map('intval', array_column($paid, 'girl_tickets'))),
                'boys'      => array_sum(array_map('intval', array_column($paid, 'boy_tickets'))),
                'paid'      => count($paid),
                'received'  => array_sum(array_column($paid, 'amount')),
                'members'   => count(array_filter($paid, static fn(array $b): bool => (bool) $b['is_member'])),
                'checkout'  => count($holding),
                'abandoned' => count($abandoned),
                'cancelled' => count($cancelled),
                'repeats'   => count(array_filter($all, static fn(array $b): bool => $b['has_repeat'])),
                'left'      => $this->service->placesLeft(),
            ],
        ]);
    }

    /** Everyone on a paid booking, by surname: print it for the door. */
    public function attendees()
    {
        $paid = array_filter($this->annotated(), static fn(array $b): bool => $b['state'] === 'paid');
        $rows = [];

        foreach ($paid as $b) {
            foreach ($b['attendees'] as $a) {
                $rows[] = $a + ['booking' => $b];
            }
        }

        usort($rows, static fn(array $x, array $y): int => strcasecmp($x['last_name'] . ' ' . $x['first_name'], $y['last_name'] . ' ' . $y['first_name']));

        return view('admin/connexions/attendees', [
            'title'  => 'Connexions door list',
            'config' => $this->config,
            'rows'   => $rows,
            'girls'  => count(array_filter($rows, static fn(array $r): bool => $r['gender'] === 'girl')),
            'boys'   => count(array_filter($rows, static fn(array $r): bool => $r['gender'] === 'boy')),
        ]);
    }

    public function edit(int $id)
    {
        $booking = $this->bookings->find($id);

        if (! $booking) {
            return redirect()->to(self::BASE)->with('error', 'Booking not found.');
        }

        return view('admin/connexions/edit', [
            'title'     => 'Edit booking ' . $booking['booking_ref'],
            'config'    => $this->config,
            'booking'   => $booking,
            'attendees' => $this->attendees->forBooking($id),
        ]);
    }

    /**
     * Correct the booker's and attendees' details, and keep notes. Tickets and
     * the amount paid can't be changed here.
     */
    public function update(int $id)
    {
        $booking = $this->bookings->find($id);

        if (! $booking) {
            return redirect()->to(self::BASE)->with('error', 'Booking not found.');
        }

        $attendees = $this->attendees->forBooking($id);
        $phone     = 'required|min_length[7]|max_length[30]|regex_match[/^[0-9 +()\-]+$/]';

        $rules = [
            'first_name'  => ['label' => 'Booker first name', 'rules' => 'required|max_length[100]'],
            'last_name'   => ['label' => 'Booker surname', 'rules' => 'required|max_length[100]'],
            'email'       => ['label' => 'Booker email', 'rules' => 'required|valid_email|max_length[255]'],
            'phone'       => ['label' => 'Booker mobile', 'rules' => $phone],
            'admin_notes' => ['label' => 'Notes', 'rules' => 'permit_empty|max_length[5000]'],
        ];

        foreach ($attendees as $a) {
            $who = $this->config->genders[$a['gender']]['one'] . ' ' . $a['position'];
            $key = 'attendees.' . $a['id'] . '.';

            $rules[$key . 'first_name'] = ['label' => $who . ' first name', 'rules' => 'required|max_length[100]'];
            $rules[$key . 'last_name']  = ['label' => $who . ' surname', 'rules' => 'required|max_length[100]'];
            $rules[$key . 'email']      = ['label' => $who . ' email', 'rules' => 'required|valid_email|max_length[255]'];
            $rules[$key . 'phone']      = ['label' => $who . ' mobile', 'rules' => $phone];
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post  = $this->request->getPost();
        $clean = static fn($v): string => trim(preg_replace('/\s+/', ' ', (string) $v));

        $this->bookings->update($id, [
            'first_name'  => $clean($post['first_name']),
            'last_name'   => $clean($post['last_name']),
            'email'       => $clean($post['email']),
            'phone'       => $clean($post['phone']),
            'admin_notes' => trim((string) ($post['admin_notes'] ?? '')) ?: null,
        ]);

        foreach ($attendees as $a) {
            $in = $post['attendees'][$a['id']] ?? [];

            $this->attendees->update($a['id'], [
                'first_name' => $clean($in['first_name'] ?? ''),
                'last_name'  => $clean($in['last_name'] ?? ''),
                'email'      => $clean($in['email'] ?? ''),
                'phone'      => $clean($in['phone'] ?? ''),
            ]);
        }

        $msg = 'Booking ' . $booking['booking_ref'] . ' updated.';
        if (! empty($booking['payment_received'])) {
            $msg .= ' Use “Resend emails” if the booker and attendees should get the corrected confirmation.';
        }

        return redirect()->to(self::BASE)->with('success', $msg);
    }

    /**
     * For a payment Stripe took that didn't reach us (the webhook failed), or
     * one taken some other way. Sends the confirmations the first time.
     */
    public function markPaid(int $id)
    {
        $booking = $this->bookings->find($id);

        if (! $booking || $booking['status'] !== 'pending') {
            return redirect()->to(self::BASE)->with('error', 'Only unpaid bookings can be marked as paid.');
        }

        $sent = $this->service->markPaid($booking, session()->get('admin_name') ?? 'Admin');

        return redirect()->to(self::BASE)->with('success', 'Booking ' . $booking['booking_ref'] . ' marked as paid.'
            . ($sent ? ' Confirmation emails queued for the booker and attendees.' : ''));
    }

    /** Queue the booker's and every attendee's confirmation again, e.g. after correcting details. */
    public function resend(int $id)
    {
        $booking = $this->bookings->find($id);

        if (! $booking || $booking['status'] !== 'paid') {
            return redirect()->to(self::BASE)->with('error', 'Only paid bookings have confirmations to resend.');
        }

        $count = $this->service->sendConfirmations($booking);

        return redirect()->to(self::BASE)->with('success', $count . ' confirmation email' . ($count === 1 ? '' : 's')
            . ' queued for ' . $booking['booking_ref'] . '.');
    }

    /** Frees the places. A card payment is not refunded here: do that in Stripe if it is owed. */
    public function cancel(int $id)
    {
        $booking = $this->bookings->find($id);

        if (! $booking || $booking['status'] === 'cancelled') {
            return redirect()->to(self::BASE)->with('error', 'Booking not found or already cancelled.');
        }

        $this->bookings->update($id, ['status' => 'cancelled', 'cancelled_at' => date('Y-m-d H:i:s')]);

        return redirect()->to(self::BASE)->with('success', 'Booking ' . $booking['booking_ref'] . ' cancelled.'
            . (! empty($booking['payment_received']) ? ' It was paid by card: no refund has been made.' : ''));
    }

    /** Back to paid if it was paid, otherwise unpaid (the booker can pay again from their link). */
    public function reinstate(int $id)
    {
        $booking = $this->bookings->find($id);

        if (! $booking || $booking['status'] !== 'cancelled') {
            return redirect()->to(self::BASE)->with('error', 'Booking not found or not cancelled.');
        }

        $paid = ! empty($booking['payment_received']);
        $this->bookings->update($id, ['status' => $paid ? 'paid' : 'pending', 'cancelled_at' => null]);

        $msg  = 'Booking ' . $booking['booking_ref'] . ' reinstated.';
        $left = $this->service->placesLeft();
        if (($left['girl'] === 0 && (int) $booking['girl_tickets'] > 0) || ($left['boy'] === 0 && (int) $booking['boy_tickets'] > 0)) {
            $msg .= ' Note: places are now full (or over) for this booking’s tickets.';
        }

        return redirect()->to(self::BASE)->with('success', $msg);
    }

    /** One row per attendee, every booking, oldest first. */
    public function export()
    {
        $rows = [];

        foreach (array_reverse($this->annotated()) as $b) {
            foreach ($b['attendees'] as $a) {
                $rows[] = [
                    $b['booking_ref'], $b['state'], $this->config->genders[$a['gender']]['one'] . ' ' . $a['position'],
                    $a['first_name'], $a['last_name'], $a['email'], $a['phone'], $a['is_repeat'] ? 'Yes' : '',
                    $b['first_name'] . ' ' . $b['last_name'], $b['email'], $b['phone'], $b['is_member'] ? 'Yes' : 'No',
                    $b['girl_tickets'], $b['boy_tickets'], $b['amount'],
                    $b['payment_received_at'], $b['payment_marked_by'], $b['stripe_payment_intent_id'],
                    $b['admin_notes'], $b['created_at'],
                ];
            }
        }

        return $this->csv('connexions_' . date('Ymd') . '.csv', [
            'Reference', 'Status', 'Ticket', 'First name', 'Surname', 'Email', 'Mobile', 'Repeat email',
            'Booked by', 'Booker email', 'Booker mobile', 'Booker LCNL member',
            'Girl tickets', 'Boy tickets', 'Amount', 'Paid at', 'Marked by', 'Stripe payment',
            'Admin notes', 'Booked at',
        ], $rows);
    }

    /* =====================================================================
       Helpers
    ===================================================================== */

    /**
     * Every booking, newest first, with its attendees, its state (paid,
     * checkout, abandoned, cancelled) and repeat flags: an attendee email
     * that appears more than once across live bookings (paid or in checkout)
     * is flagged but allowed.
     */
    private function annotated(): array
    {
        $all       = $this->bookings->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->findAll();
        $attendees = $this->attendees->forBookings(array_map('intval', array_column($all, 'id')));
        $now       = date('Y-m-d H:i:s');

        foreach ($all as &$b) {
            $b['attendees'] = $attendees[(int) $b['id']] ?? [];
            $b['state']     = match (true) {
                $b['status'] === 'cancelled' => 'cancelled',
                $b['status'] === 'paid'      => 'paid',
                ! empty($b['hold_until']) && $b['hold_until'] > $now => 'checkout',
                default                      => 'abandoned',
            };
        }
        unset($b);

        $seen = [];
        foreach ($all as $b) {
            if (in_array($b['state'], ['paid', 'checkout'], true)) {
                foreach ($b['attendees'] as $a) {
                    $key        = strtolower(trim($a['email']));
                    $seen[$key] = ($seen[$key] ?? 0) + 1;
                }
            }
        }

        foreach ($all as &$b) {
            $live            = in_array($b['state'], ['paid', 'checkout'], true);
            $b['has_repeat'] = false;

            foreach ($b['attendees'] as &$a) {
                $a['is_repeat'] = $live && ($seen[strtolower(trim($a['email']))] ?? 0) > 1;
                $b['has_repeat'] = $b['has_repeat'] || $a['is_repeat'];
            }
            unset($a);

            $b['stripe_url'] = ! empty($b['stripe_payment_intent_id'])
                ? StripeService::dashboardPaymentUrl($b['stripe_payment_intent_id'])
                : null;
        }
        unset($b);

        return $all;
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
