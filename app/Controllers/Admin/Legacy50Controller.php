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

    /**
     * Correct anything the donor submitted. Payment and status keep their own
     * buttons, the amount is fixed, and the donor-submitted plaque name is kept.
     *
     * Turning Gift Aid on records a declaration the donor made to an admin
     * (by phone, email or in person), so the admin must confirm that it was made.
     * Every save appends a dated line to the admin notes naming who changed
     * what, which gives a record HMRC can see for any Gift Aid change.
     */
    public function update(int $id)
    {
        $pledge = $this->pledges->find($id);

        if (! $pledge) {
            return redirect()->to(self::BASE)->with('error', 'Pledge not found.');
        }

        $post    = $this->request->getPost();
        $giftAid = ! empty($post['gift_aid']);
        $turnOn  = $giftAid && ! $pledge['gift_aid'];

        $rules = [
            'title'             => 'permit_empty|in_list[' . implode(',', $this->config->titles) . ']',
            'first_name'        => 'required|trim|max_length[100]',
            'last_name'         => 'required|trim|max_length[100]',
            'email'             => 'required|trim|valid_email|max_length[255]',
            'phone'             => 'required|trim|min_length[7]|max_length[30]',
            'plaque_name'       => 'required|trim|max_length[' . $this->config->plaqueMaxLength . ']',
            'payment_reference' => 'required|trim|max_length[' . $this->config->paymentReferenceMaxLength . ']',
            'payment_timing'    => 'required|in_list[paid,will_pay]',
            'comments'          => 'permit_empty|trim|max_length[2000]',
            'admin_notes'       => 'permit_empty|trim|max_length[5000]',
        ];

        if ($giftAid) {
            $rules += [
                'house_name_number' => 'required|trim|max_length[100]',
                'street'            => 'permit_empty|trim|max_length[150]',
                'town'              => 'required|trim|max_length[100]',
                'postcode'          => 'required|trim|max_length[10]',
            ];
        }

        $valid  = $this->validate($rules, [
            'house_name_number' => ['required' => 'Gift Aid needs the house name or number.'],
            'town'              => ['required' => 'Gift Aid needs the town or city.'],
            'postcode'          => ['required' => 'Gift Aid needs the postcode.'],
        ]);
        $errors = $valid ? [] : $this->validator->getErrors();

        $reference = trim(preg_replace('/\s+/', ' ', (string) ($post['payment_reference'] ?? '')));
        if (! isset($errors['payment_reference']) && ! preg_match('/^[A-Za-z0-9 \-]+$/', $reference)) {
            $errors['payment_reference'] = 'The payment reference can only use letters, numbers, spaces and hyphens.';
        }

        $postcode = $giftAid ? LegacyPledgeModel::normalisePostcode((string) ($post['postcode'] ?? '')) : null;
        if ($giftAid && ! isset($errors['postcode']) && $postcode === null) {
            $errors['postcode'] = 'Please enter a valid UK postcode.';
        }

        if ($turnOn && empty($post['gift_aid_confirmed'])) {
            $errors['gift_aid_confirmed'] = 'To add Gift Aid, confirm that the donor has made the Gift Aid declaration.';
        }

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $trim = static fn(string $key): ?string => ($v = trim((string) ($post[$key] ?? ''))) !== '' ? $v : null;

        $data = [
            'title'             => $trim('title'),
            'first_name'        => $trim('first_name'),
            'last_name'         => $trim('last_name'),
            'email'             => $trim('email'),
            'phone'             => $trim('phone'),
            'plaque_name'       => trim(preg_replace('/\s+/u', ' ', $post['plaque_name'])),
            'payment_reference' => strtoupper($reference),
            'payment_timing'    => $post['payment_timing'],
            'comments'          => $trim('comments'),
            'gift_aid'          => $giftAid ? 1 : 0,
            // The address is held only for HMRC, so it goes when Gift Aid does.
            'house_name_number' => $giftAid ? $trim('house_name_number') : null,
            'street'            => $giftAid ? $trim('street') : null,
            'town'              => $giftAid ? $trim('town') : null,
            'postcode'          => $postcode,
        ];

        if ($turnOn) {
            $data += [
                'gift_aid_declared_at' => date('Y-m-d H:i:s'),
                'gift_aid_version'     => $this->config->giftAidVersion,
                'gift_aid_wording'     => $this->config->giftAidWording(),
            ];
        } elseif (! $giftAid) {
            $data += ['gift_aid_declared_at' => null, 'gift_aid_version' => null, 'gift_aid_wording' => null];
        }

        $notes = $trim('admin_notes');
        $log   = $this->changeLog($pledge, $data);
        if ($log !== null) {
            $notes = ($notes !== null ? $notes . "\n" : '') . $log;
        }
        $data['admin_notes'] = $notes !== null ? mb_substr($notes, -5000) : null;

        $this->pledges->update($id, $data);

        return redirect()->to(self::BASE)->with('success', 'Pledge ' . $pledge['pledge_ref'] . ' updated.');
    }

    /** "[5 Nov 2026 14:02] Sunny: Gift Aid turned on; changed email, postcode", or null if nothing changed. */
    private function changeLog(array $before, array $after): ?string
    {
        $labels = [
            'title' => 'title', 'first_name' => 'first name', 'last_name' => 'last name',
            'email' => 'email', 'phone' => 'mobile', 'plaque_name' => 'plaque name',
            'payment_reference' => 'payment reference', 'payment_timing' => 'payment timing',
            'comments' => 'comments', 'house_name_number' => 'house name/number',
            'street' => 'street', 'town' => 'town', 'postcode' => 'postcode',
        ];

        $changed = [];
        foreach ($labels as $field => $label) {
            if ((string) ($before[$field] ?? '') !== (string) ($after[$field] ?? '')) {
                $changed[] = $label;
            }
        }

        $parts = [];
        if ((bool) $before['gift_aid'] !== (bool) $after['gift_aid']) {
            $parts[] = $after['gift_aid'] ? 'Gift Aid turned on (declaration confirmed by donor)' : 'Gift Aid turned off';
            // The address follows Gift Aid, so don't list it twice.
            $changed = array_diff($changed, ['house name/number', 'street', 'town', 'postcode']);
        }
        if ($changed !== []) {
            $parts[] = 'changed ' . implode(', ', $changed);
        }

        if ($parts === []) {
            return null;
        }

        return '[' . date('j M Y H:i') . '] ' . (session()->get('admin_name') ?? 'Admin') . ': ' . implode('; ', $parts);
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
