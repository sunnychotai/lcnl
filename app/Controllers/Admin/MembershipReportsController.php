<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\MembershipModel;
use CodeIgniter\Database\BaseBuilder;

class MembershipReportsController extends BaseController
{
    public function index()
    {
        $total = (int) (new MemberModel())->where('deleted_at', null)->countAllResults();

        $life = (int) (new MembershipModel())
            ->where('membership_type', 'life')
            ->where('status', 'active')
            ->countAllResults();

        $db = \Config\Database::connect();
        $stripe = (int) $db->query("
            SELECT COUNT(DISTINCT mp.member_id)
            FROM membership_payments mp
            INNER JOIN members m ON m.id = mp.member_id
            WHERE mp.provider = 'stripe'
              AND mp.status = 'paid'
              AND m.status = 'active'
              AND m.deleted_at IS NULL
        ")->getRow()->{'COUNT(DISTINCT mp.member_id)'};

        $stats = [
            'total'    => $total,
            'life'     => $life,
            'non_life' => max(0, $total - $life),
            'stripe'   => $stripe,
        ];

        $activeTab = 'reports';
        return view('admin/membership/reports', compact('stats', 'activeTab'));
    }

    // GET /admin/membership/reports/export
    public function exportAll()
    {
        $b = $this->baseQuery();
        $b->where('m.deleted_at', null)->orderBy('m.last_name', 'ASC')->orderBy('m.first_name', 'ASC');
        return $this->streamCsv($b, 'all_members_' . date('Ymd') . '.csv');
    }

    // GET /admin/membership/reports/life/export
    public function exportLife()
    {
        $b = $this->baseQuery();
        $b->where('ms.membership_type', 'life')
            ->where('ms.status', 'active')
            ->where('m.deleted_at', null)
            ->orderBy('m.last_name', 'ASC')
            ->orderBy('m.first_name', 'ASC');
        return $this->streamCsv($b, 'life_members_' . date('Ymd') . '.csv');
    }

    // GET /admin/membership/reports/non-life/export
    public function exportNonLife()
    {
        $b = $this->baseQuery();
        $b->where('ms.membership_type !=', 'life')
            ->where('m.deleted_at', null)
            ->orderBy('m.last_name', 'ASC')
            ->orderBy('m.first_name', 'ASC');
        return $this->streamCsv($b, 'non_life_members_' . date('Ymd') . '.csv');
    }

    // GET /admin/membership/reports/stripe/export
    public function exportStripe()
    {
        $db = \Config\Database::connect();
        $b = $db->table('members m');
        $b->select('
            m.id,
            m.first_name,
            m.last_name,
            m.email,
            m.mobile,
            m.address1,
            m.address2,
            m.city,
            m.status AS member_status,
            mp.amount_minor,
            mp.currency,
            mp.paid_at,
            mp.stripe_payment_intent_id,
            mp.stripe_checkout_session_id,
            mp.stripe_customer_id
        ');
        $b->join('membership_payments mp', 'm.id = mp.member_id', 'inner');
        $b->where('mp.provider', 'stripe');
        $b->where('mp.status', 'paid');
        $b->where('m.status', 'active');
        $b->where('m.deleted_at', null);
        $b->orderBy('mp.paid_at', 'DESC');

        return $this->streamCsv($b, 'stripe_paid_members_' . date('Ymd') . '.csv');
    }

    private function baseQuery(): BaseBuilder
    {
        $db = \Config\Database::connect();

        $latestMemberships = "(
            SELECT ms.member_id, ms.id, ms.membership_type, ms.status, ms.start_date
            FROM memberships ms
            INNER JOIN (
                SELECT member_id, MAX(id) as max_id
                FROM memberships
                WHERE status = 'active'
                GROUP BY member_id
            ) latest ON ms.id = latest.max_id
        )";

        $b = $db->table('members m');
        $b->select('
            m.id,
            m.first_name,
            m.last_name,
            m.email,
            m.mobile,
            m.address1,
            m.address2,
            m.city,
            m.gender,
            m.date_of_birth,
            m.status,
            m.verified_at,
            m.created_at,
            ms.membership_type,
            ms.status AS membership_status,
            ms.start_date AS membership_start
        ');
        $b->join("$latestMemberships ms", 'm.id = ms.member_id', 'left', false);

        return $b;
    }

    private function streamCsv(BaseBuilder $b, string $filename)
    {
        $db  = \Config\Database::connect();
        $sql = $b->getCompiledSelect();
        $query = $db->query($sql, [], false);

        $this->response->setHeader('Content-Type', 'text/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $this->response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
        $this->response->setHeader('Pragma', 'no-cache');
        $this->response->setHeader('Expires', '0');
        $this->response->sendHeaders();

        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

        $first = $query->getUnbufferedRow('array');
        if ($first) {
            fputcsv($out, array_keys($first));
            fputcsv($out, $first);
            while ($row = $query->getUnbufferedRow('array')) {
                fputcsv($out, $row);
            }
        }

        fclose($out);
        $query->freeResult();
        exit(0);
    }
}
