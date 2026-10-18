<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Database\BaseBuilder;

class MembershipReportsController extends BaseController
{
    // Each card is the row count of the CSV it sits next to, so the two can't disagree.
    public function index()
    {
        $stats = [
            'total'    => $this->allMembers()->countAllResults(),
            'life'     => $this->lifeMembers()->countAllResults(),
            'non_life' => $this->nonLifeMembers()->countAllResults(),
            'stripe'   => $this->stripeMembers()->countAllResults(),
        ];

        $activeTab = 'reports';
        return view('admin/membership/reports', compact('stats', 'activeTab'));
    }

    // GET /admin/membership/reports/export
    public function exportAll()
    {
        return $this->csv($this->allMembers(), 'all_members_' . date('Ymd') . '.csv');
    }

    // GET /admin/membership/reports/life/export
    public function exportLife()
    {
        return $this->csv($this->lifeMembers(), 'life_members_' . date('Ymd') . '.csv');
    }

    // GET /admin/membership/reports/non-life/export
    public function exportNonLife()
    {
        return $this->csv($this->nonLifeMembers(), 'non_life_members_' . date('Ymd') . '.csv');
    }

    // GET /admin/membership/reports/stripe/export
    public function exportStripe()
    {
        return $this->csv($this->stripeMembers(), 'stripe_paid_members_' . date('Ymd') . '.csv');
    }

    /**
     * Every member who isn't soft-deleted, one row each, with their current
     * membership: the latest active row, or the latest row of any status if
     * none is active. Member and membership status are columns, not filters.
     */
    private function allMembers(): BaseBuilder
    {
        $db = \Config\Database::connect();
        $memberships = $db->prefixTable('memberships');

        $currentMembership = "ms.id = (
            SELECT x.id FROM {$memberships} x
            WHERE x.member_id = m.id
            ORDER BY CASE WHEN x.status = 'active' THEN 0 ELSE 1 END, x.id DESC
            LIMIT 1
        )";

        return $db->table('members m')
            ->select('
                m.id,
                m.first_name,
                m.last_name,
                m.email,
                m.mobile,
                m.address1,
                m.address2,
                m.city,
                m.postcode,
                m.gender,
                m.date_of_birth,
                m.status,
                m.verified_at,
                m.created_at,
                ms.membership_type,
                ms.membership_number,
                ms.status AS membership_status,
                ms.start_date AS membership_start
            ')
            ->join("{$memberships} ms", $currentMembership, 'left', false)
            ->where('m.deleted_at', null)
            ->orderBy('m.last_name', 'ASC')
            ->orderBy('m.first_name', 'ASC');
    }

    // Life is the membership type, whatever the member or membership status.
    private function lifeMembers(): BaseBuilder
    {
        return $this->allMembers()->where("LOWER(ms.membership_type) = 'life'", null, false);
    }

    // Everyone else, including members whose type is blank.
    private function nonLifeMembers(): BaseBuilder
    {
        return $this->allMembers()->where("COALESCE(LOWER(ms.membership_type), '') <> 'life'", null, false);
    }

    // Active members with a paid Stripe payment, one row each with their latest
    // payment; paid_payments shows anyone who has paid more than once.
    private function stripeMembers(): BaseBuilder
    {
        $db = \Config\Database::connect();
        $payments = $db->prefixTable('membership_payments');
        $paid = "p.member_id = m.id AND p.provider = 'stripe' AND p.status = 'paid'";

        return $db->table('members m')
            ->select("
                m.id,
                m.first_name,
                m.last_name,
                m.email,
                m.mobile,
                m.address1,
                m.address2,
                m.city,
                m.postcode,
                m.status AS member_status,
                mp.amount_minor,
                mp.currency,
                mp.paid_at,
                mp.stripe_payment_intent_id,
                mp.stripe_checkout_session_id,
                mp.stripe_customer_id,
                (SELECT COUNT(*) FROM {$payments} p WHERE {$paid}) AS paid_payments
            ", false)
            ->join("{$payments} mp", "mp.id = (SELECT MAX(p.id) FROM {$payments} p WHERE {$paid})", 'inner', false)
            ->where('m.status', 'active')
            ->where('m.deleted_at', null)
            ->orderBy('mp.paid_at', 'DESC');
    }

    private function csv(BaseBuilder $b, string $filename)
    {
        $out = fopen('php://temp', 'r+');
        fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

        $rows = $b->get()->getResultArray();
        if ($rows) {
            fputcsv($out, array_keys($rows[0]));
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
        }

        rewind($out);
        $body = stream_get_contents($out);
        fclose($out);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setBody($body);
    }
}
