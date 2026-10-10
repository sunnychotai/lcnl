<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EmailQueueModel;
use App\Models\LegacyPledgeModel;
use Config\Legacy50;

/**
 * Back-office for the Legacy 50 pledge campaign. ADMIN only: pledges carry
 * home addresses and Gift Aid declarations.
 */
class Legacy50Controller extends BaseController
{
    private const BASE = 'admin/legacy50';

    protected Legacy50 $config;
    protected LegacyPledgeModel $pledges;
    protected EmailQueueModel $emails;

    public function __construct()
    {
        $this->config  = config('Legacy50');
        $this->pledges = new LegacyPledgeModel();
        $this->emails  = new EmailQueueModel();
    }

    public function index()
    {
        $all = $this->pledges->orderBy('created_at', 'DESC')->findAll();

        // Flag repeat emails so double submissions are visible, not blocked.
        $seen = [];
        foreach (array_reverse(array_keys($all)) as $i) {
            $key = strtolower(trim($all[$i]['email']));
            $all[$i]['is_duplicate'] = $all[$i]['status'] !== 'cancelled' && isset($seen[$key]);
            if ($all[$i]['status'] !== 'cancelled') {
                $seen[$key] = true;
            }
        }

        $active = array_filter($all, static fn(array $p): bool => $p['status'] !== 'cancelled');
        $paid   = array_filter($active, static fn(array $p): bool => (bool) $p['payment_received']);

        $filter = (string) $this->request->getGet('status');
        $rows   = match ($filter) {
            'awaiting'  => array_filter($active, static fn(array $p): bool => ! $p['payment_received']),
            'paid'      => $paid,
            'cancelled' => array_filter($all, static fn(array $p): bool => $p['status'] === 'cancelled'),
            default     => $all,
        };

        $received     = array_sum(array_column($paid, 'amount'));
        $giftAidTotal = array_sum(array_column(array_filter($paid, static fn(array $p): bool => (bool) $p['gift_aid']), 'amount'));

        return view('admin/legacy50/index', [
            'title'   => 'Legacy 50 Pledges',
            'config'  => $this->config,
            'pledges' => $rows,
            'filter'  => in_array($filter, ['awaiting', 'paid', 'cancelled'], true) ? $filter : 'all',
            'stats'   => [
                'pledges'         => count($active),
                'paid'            => count($paid),
                'awaiting'        => count($active) - count($paid),
                'received'        => $received,
                'withMatch'       => $received + count($paid) * $this->config->match,
                'giftAid'         => count(array_filter($active, static fn(array $p): bool => (bool) $p['gift_aid'])),
                'giftAidEstimate' => $giftAidTotal * 0.25,
            ],
        ]);
    }

    public function edit(int $id)
    {
        $pledge = $this->pledges->find($id);

        if (! $pledge) {
            return redirect()->to(self::BASE)->with('error', 'Pledge not found.');
        }

        return view('admin/legacy50/edit', [
            'title'  => 'Edit pledge ' . $pledge['pledge_ref'],
            'config' => $this->config,
            'pledge' => $pledge,
        ]);
    }

    /** Admins can tidy the plaque wording and keep notes; nothing else is editable. */
    public function update(int $id)
    {
        $pledge = $this->pledges->find($id);

        if (! $pledge) {
            return redirect()->to(self::BASE)->with('error', 'Pledge not found.');
        }

        $rules = [
            'plaque_name' => 'required|trim|max_length[' . $this->config->plaqueMaxLength . ']',
            'admin_notes' => 'permit_empty|trim|max_length[5000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->pledges->update($id, [
            'plaque_name' => trim(preg_replace('/\s+/u', ' ', $this->request->getPost('plaque_name'))),
            'admin_notes' => $this->request->getPost('admin_notes') ?: null,
        ]);

        return redirect()->to(self::BASE)->with('success', 'Pledge ' . $pledge['pledge_ref'] . ' updated.');
    }

    /**
     * Toggle whether the transfer has been matched on the LCF statement. The
     * first time a pledge is marked paid, the donor gets a thank-you email.
     */
    public function togglePayment(int $id)
    {
        $pledge = $this->pledges->find($id);

        if (! $pledge || $pledge['status'] === 'cancelled') {
            return redirect()->to(self::BASE)->with('error', 'Pledge not found or cancelled.');
        }

        $nowPaid = empty($pledge['payment_received']);
        $data    = [
            'payment_received'    => $nowPaid ? 1 : 0,
            'payment_received_at' => $nowPaid ? date('Y-m-d H:i:s') : null,
            'payment_marked_by'   => $nowPaid ? (session()->get('admin_name') ?? 'Admin') : null,
            'status'              => $nowPaid ? 'paid' : 'pledged',
        ];

        $thanked = false;
        if ($nowPaid && empty($pledge['thank_you_sent_at'])) {
            $this->queue($pledge, 'legacy50_payment_received', 'Thank you – your donation has arrived');
            $data['thank_you_sent_at'] = date('Y-m-d H:i:s');
            $thanked = true;
        }

        $this->pledges->update($id, $data);

        $name = $pledge['first_name'] . ' ' . $pledge['last_name'];

        return redirect()->to(self::BASE)->with('success', $nowPaid
            ? $name . ' marked as paid.' . ($thanked ? ' Thank-you email queued.' : '')
            : $name . ' marked as unpaid.');
    }

    public function remind(int $id)
    {
        $pledge = $this->pledges->find($id);

        if (! $pledge || $pledge['status'] !== 'pledged') {
            return redirect()->to(self::BASE)->with('error', 'Only unpaid pledges can be sent a reminder.');
        }

        $this->queue($pledge, 'legacy50_payment_reminder', 'Your Legacy 50 pledge – payment details');

        $this->pledges->update($id, [
            'reminder_sent_at' => date('Y-m-d H:i:s'),
            'reminder_count'   => (int) $pledge['reminder_count'] + 1,
        ]);

        return redirect()->to(self::BASE)
            ->with('success', 'Reminder queued for ' . $pledge['first_name'] . ' ' . $pledge['last_name'] . '.');
    }

    public function cancel(int $id)
    {
        return $this->setStatus($id, 'cancelled', 'cancelled');
    }

    public function reinstate(int $id)
    {
        return $this->setStatus($id, 'pledged', 'reinstated');
    }

    /* =====================================================================
       Exports
    ===================================================================== */

    public function export()
    {
        $rows = $this->pledges->orderBy('created_at', 'ASC')->findAll();

        return $this->csv('legacy50_pledges_' . date('Ymd') . '.csv', [
            'Pledge ref', 'Title', 'First name', 'Last name', 'Email', 'Mobile',
            'Plaque name', 'Plaque name (as submitted)', 'Payment reference', 'Said paid at pledge',
            'Amount', 'Payment received', 'Received at', 'Marked by', 'Status',
            'Gift Aid', 'Declared at', 'Declaration version',
            'House name/number', 'Street', 'Town', 'Postcode',
            'Reminders sent', 'Comments', 'Admin notes', 'Pledged at',
        ], array_map(static fn(array $p): array => [
            $p['pledge_ref'], $p['title'], $p['first_name'], $p['last_name'], $p['email'], $p['phone'],
            $p['plaque_name'], $p['plaque_name_submitted'], $p['payment_reference'],
            $p['payment_timing'] === 'paid' ? 'Yes' : 'No',
            $p['amount'], $p['payment_received'] ? 'Yes' : 'No', $p['payment_received_at'], $p['payment_marked_by'], $p['status'],
            $p['gift_aid'] ? 'Yes' : 'No', $p['gift_aid_declared_at'], $p['gift_aid_version'],
            $p['house_name_number'], $p['street'], $p['town'], $p['postcode'],
            $p['reminder_count'], $p['comments'], $p['admin_notes'], $p['created_at'],
        ], $rows));
    }

    /** Paid donors only, alphabetical — the list for the engraver. */
    public function exportPlaque()
    {
        $rows = $this->pledges->where('status', 'paid')->findAll();
        usort($rows, static fn(array $a, array $b): int => strcasecmp($a['plaque_name'], $b['plaque_name']));

        return $this->csv('legacy50_plaque_' . date('Ymd') . '.csv',
            ['Plaque name', 'Pledge ref', 'Donor'],
            array_map(static fn(array $p): array => [
                $p['plaque_name'], $p['pledge_ref'], $p['first_name'] . ' ' . $p['last_name'],
            ], $rows)
        );
    }

    /**
     * Paid Gift Aid donations in the column order of HMRC's Charities Online
     * schedule, trimmed to its field limits, ready to paste in.
     */
    public function exportGiftAid()
    {
        $rows = $this->pledges->where('status', 'paid')->where('gift_aid', 1)
            ->orderBy('payment_received_at', 'ASC')->findAll();

        return $this->csv('legacy50_gift_aid_' . date('Ymd') . '.csv', [
            'Title', 'First name or initial', 'Last name', 'House name or number', 'Postcode',
            'Aggregated donations', 'Sponsored event', 'Donation date (DD/MM/YY)', 'Amount',
        ], array_map(static fn(array $p): array => [
            mb_substr((string) $p['title'], 0, 4),
            mb_substr($p['first_name'], 0, 35),
            mb_substr($p['last_name'], 0, 35),
            mb_substr((string) $p['house_name_number'], 0, 40),
            $p['postcode'],
            '',
            '',
            date('d/m/y', strtotime($p['payment_received_at'])),
            number_format((float) $p['amount'], 2, '.', ''),
        ], $rows));
    }

    /* =====================================================================
       Helpers
    ===================================================================== */

    private function setStatus(int $id, string $status, string $verb)
    {
        $pledge = $this->pledges->find($id);

        if (! $pledge) {
            return redirect()->to(self::BASE)->with('error', 'Pledge not found.');
        }

        $data = ['status' => $status];
        if ($status === 'pledged') {
            // Reinstated pledges start unpaid; payment is re-marked explicitly.
            $data += ['payment_received' => 0, 'payment_received_at' => null, 'payment_marked_by' => null];
        }

        $this->pledges->update($id, $data);

        return redirect()->to(self::BASE)
            ->with('success', 'Pledge ' . $pledge['pledge_ref'] . ' ' . $verb . '.');
    }

    private function queue(array $pledge, string $view, string $subject): void
    {
        $html = view('emails/' . $view, ['pledge' => $pledge, 'config' => $this->config]);

        $this->emails->enqueue([
            'to_email'   => $pledge['email'],
            'to_name'    => $pledge['first_name'] . ' ' . $pledge['last_name'],
            'subject'    => $subject,
            'type'       => $view,
            'related_id' => $pledge['id'],
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
            // Neutralise spreadsheet formulas in donor-supplied text.
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
