<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ChopdaRegistrationModel;
use App\Models\EmailQueueModel;
use Config\ChopdaPujan;

/**
 * Back-office for Chopda Pujan registrations: match payments, chase unpaid
 * registrations and export the list for the day. ADMIN and EVENTS roles.
 */
class ChopdaPujanController extends BaseController
{
    private const BASE = 'admin/chopda-pujan';

    protected ChopdaPujan $config;
    protected ChopdaRegistrationModel $registrations;
    protected EmailQueueModel $emails;

    public function __construct()
    {
        $this->config        = config('ChopdaPujan');
        $this->registrations = new ChopdaRegistrationModel();
        $this->emails        = new EmailQueueModel();
    }

    public function index()
    {
        $all = $this->registrations->orderBy('created_at', 'DESC')->findAll();

        // Flag repeat emails so double submissions are visible, not blocked:
        // a family may genuinely book two poojas.
        $seen = [];
        foreach (array_reverse(array_keys($all)) as $i) {
            $key = strtolower(trim($all[$i]['email']));
            $all[$i]['is_duplicate'] = $all[$i]['status'] !== 'cancelled' && isset($seen[$key]);
            if ($all[$i]['status'] !== 'cancelled') {
                $seen[$key] = true;
            }
        }

        $active = array_filter($all, static fn(array $r): bool => $r['status'] !== 'cancelled');
        $paid   = array_filter($active, static fn(array $r): bool => (bool) $r['payment_received']);

        $filter = (string) $this->request->getGet('status');
        $rows   = match ($filter) {
            'awaiting'  => array_filter($active, static fn(array $r): bool => ! $r['payment_received']),
            'paid'      => $paid,
            'cancelled' => array_filter($all, static fn(array $r): bool => $r['status'] === 'cancelled'),
            default     => $all,
        };

        return view('admin/chopda/index', [
            'title'         => 'Chopda Pujan Registrations',
            'config'        => $this->config,
            'registrations' => $rows,
            'filter'        => in_array($filter, ['awaiting', 'paid', 'cancelled'], true) ? $filter : 'all',
            'stats'         => [
                'poojas'    => count($active),
                'paid'      => count($paid),
                'awaiting'  => count($active) - count($paid),
                'attendees' => array_sum(array_map('intval', array_column($active, 'num_attendees'))),
                'received'  => array_sum(array_column($paid, 'amount')),
                'expected'  => array_sum(array_column($active, 'amount')),
            ],
        ]);
    }

    public function edit(int $id)
    {
        $registration = $this->registrations->find($id);

        if (! $registration) {
            return redirect()->to(self::BASE)->with('error', 'Registration not found.');
        }

        return view('admin/chopda/edit', [
            'title'        => 'Edit registration ' . $registration['registration_ref'],
            'config'       => $this->config,
            'registration' => $registration,
        ]);
    }

    /** Admins can correct the attendee count and keep notes; nothing else is editable. */
    public function update(int $id)
    {
        $registration = $this->registrations->find($id);

        if (! $registration) {
            return redirect()->to(self::BASE)->with('error', 'Registration not found.');
        }

        $rules = [
            'num_attendees' => 'required|is_natural_no_zero|less_than_equal_to[99]',
            'admin_notes'   => 'permit_empty|trim|max_length[5000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->registrations->update($id, [
            'num_attendees' => (int) $this->request->getPost('num_attendees'),
            'admin_notes'   => $this->request->getPost('admin_notes') ?: null,
        ]);

        return redirect()->to(self::BASE)
            ->with('success', 'Registration ' . $registration['registration_ref'] . ' updated.');
    }

    /**
     * Toggle whether the transfer has been matched on the LCNL statement. The
     * first time a registration is marked paid, the registrant is told their
     * place is confirmed.
     */
    public function togglePayment(int $id)
    {
        $registration = $this->registrations->find($id);

        if (! $registration || $registration['status'] === 'cancelled') {
            return redirect()->to(self::BASE)->with('error', 'Registration not found or cancelled.');
        }

        $nowPaid = empty($registration['payment_received']);
        $data    = [
            'payment_received'    => $nowPaid ? 1 : 0,
            'payment_received_at' => $nowPaid ? date('Y-m-d H:i:s') : null,
            'payment_marked_by'   => $nowPaid ? (session()->get('admin_name') ?? 'Admin') : null,
            'status'              => $nowPaid ? 'paid' : 'registered',
        ];

        $confirmed = false;
        if ($nowPaid && empty($registration['confirmed_sent_at'])) {
            $this->queue($registration, 'chopda_payment_received', 'Chopda Pujan – your place is confirmed');
            $data['confirmed_sent_at'] = date('Y-m-d H:i:s');
            $confirmed = true;
        }

        $this->registrations->update($id, $data);

        $name = $registration['first_name'] . ' ' . $registration['last_name'];

        return redirect()->to(self::BASE)->with('success', $nowPaid
            ? $name . ' marked as paid.' . ($confirmed ? ' Confirmation email queued.' : '')
            : $name . ' marked as unpaid.');
    }

    public function remind(int $id)
    {
        $registration = $this->registrations->find($id);

        if (! $registration || $registration['status'] !== 'registered') {
            return redirect()->to(self::BASE)->with('error', 'Only unpaid registrations can be sent a reminder.');
        }

        $this->queue($registration, 'chopda_payment_reminder', 'Chopda Pujan – payment reminder');

        $this->registrations->update($id, [
            'reminder_sent_at' => date('Y-m-d H:i:s'),
            'reminder_count'   => (int) $registration['reminder_count'] + 1,
        ]);

        return redirect()->to(self::BASE)
            ->with('success', 'Reminder queued for ' . $registration['first_name'] . ' ' . $registration['last_name'] . '.');
    }

    public function cancel(int $id)
    {
        return $this->setStatus($id, 'cancelled', 'cancelled');
    }

    public function reinstate(int $id)
    {
        return $this->setStatus($id, 'registered', 'reinstated');
    }

    /** Every registration, oldest first — doubles as the sign-in sheet for the day. */
    public function export()
    {
        $rows = $this->registrations->orderBy('created_at', 'ASC')->findAll();

        return $this->csv('chopda_pujan_' . date('Ymd') . '.csv', [
            'Ref', 'First name', 'Last name', 'Email', 'Mobile', 'Attending',
            'Payment reference', 'Said paid at registration', 'Amount', 'Payment received',
            'Received at', 'Marked by', 'Status', 'Reminders sent', 'Comments', 'Admin notes', 'Registered at',
        ], array_map(static fn(array $r): array => [
            $r['registration_ref'], $r['first_name'], $r['last_name'], $r['email'], $r['phone'], $r['num_attendees'],
            $r['payment_reference'], $r['payment_timing'] === 'paid' ? 'Yes' : 'No', $r['amount'],
            $r['payment_received'] ? 'Yes' : 'No', $r['payment_received_at'], $r['payment_marked_by'], $r['status'],
            $r['reminder_count'], $r['comments'], $r['admin_notes'], $r['created_at'],
        ], $rows));
    }

    /* =====================================================================
       Helpers
    ===================================================================== */

    private function setStatus(int $id, string $status, string $verb)
    {
        $registration = $this->registrations->find($id);

        if (! $registration) {
            return redirect()->to(self::BASE)->with('error', 'Registration not found.');
        }

        $data = ['status' => $status];
        if ($status === 'registered') {
            // Reinstated registrations start unpaid; payment is re-marked explicitly.
            $data += ['payment_received' => 0, 'payment_received_at' => null, 'payment_marked_by' => null];
        }

        $this->registrations->update($id, $data);

        return redirect()->to(self::BASE)
            ->with('success', 'Registration ' . $registration['registration_ref'] . ' ' . $verb . '.');
    }

    private function queue(array $registration, string $view, string $subject): void
    {
        $html = view('emails/' . $view, ['registration' => $registration, 'config' => $this->config]);

        $this->emails->enqueue([
            'to_email'   => $registration['email'],
            'to_name'    => $registration['first_name'] . ' ' . $registration['last_name'],
            'subject'    => $subject,
            'type'       => $view,
            'related_id' => $registration['id'],
            'body_html'  => $html,
            'body_text'  => html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'priority'   => 1,
        ]);
    }

    /** At most a few hundred rows, so build the file in memory and return it. */
    private function csv(string $filename, array $header, array $rows)
    {
        $out = fopen('php://temp', 'w+');
        fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

        fputcsv($out, $header);
        foreach ($rows as $row) {
            // Neutralise spreadsheet formulas in registrant-supplied text.
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
