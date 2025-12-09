<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\MembershipModel;
use CodeIgniter\Database\BaseBuilder;

class MembershipReportsController extends BaseController
{
    protected MemberModel $members;
    protected MembershipModel $memberships;

    public function __construct()
    {
        $this->members = new MemberModel();       // table: members
        $this->memberships = new MembershipModel();   // table: memberships (expects field membership_type)
    }

    /* --------------------------
     |  DASHBOARD (cards + list)
     * -------------------------*/
    public function index()
    {
        // Summary cards
        $stats = [
            'total' => $this->members->where('deleted_at', null)->countAllResults(),
            'life' => $this->memberships->where('membership_type', 'life')->where('status', 'active')->countAllResults(),
            'standard' => $this->memberships->where('membership_type', 'standard')->countAllResults(),
            'email_unknown' => $this->members->like('email', '@lcnl.org', 'before')->where('deleted_at', null)->countAllResults(),
            'mobile_missing' => $this->members->groupStart()->where('mobile', '')->orWhere('mobile IS NULL', null, false)->groupEnd()->where('deleted_at', null)->countAllResults(),
        ];

        // Additional stats with corrected DOB check using CAST
        $stats += [
            'missing_gender' => $this->members
                ->groupStart()->where('gender', null)->orWhere('gender', '')->groupEnd()
                ->where('deleted_at', null)->countAllResults(),

            'missing_dob' => $this->members
                ->groupStart()
                ->where('date_of_birth IS NULL', null, false)
                ->orWhere("CAST(date_of_birth AS CHAR) = '0000-00-00'", null, false)
                ->orWhere("CAST(date_of_birth AS CHAR) = ''", null, false)
                ->groupEnd()
                ->where('deleted_at', null)
                ->countAllResults(),

            'not_verified' => $this->members->where('verified_at', null)
                ->where('deleted_at', null)->countAllResults(),

            'pending' => $this->members->where('status', 'pending')
                ->where('deleted_at', null)->countAllResults(),
        ];

        $activeTab = 'reports';
        return view('admin/membership_reports/index', compact('stats', 'activeTab'));
    }

    /* --------------------------------------------------
     |  FILTERS: status, membership_type, city (optional)
     * -------------------------------------------------*/
    private function applyCommonFilters(BaseBuilder $b, ?string $status, ?string $type, ?string $city): BaseBuilder
    {
        // status: pending|active|disabled|all
        if ($status && $status !== 'all') {
            $b->where('m.status', $status);
        }

        // membership_type: life|standard|all (join already present where needed)
        if ($type && $type !== 'all') {
            $b->where('ms.membership_type', $type);
        }

        // city partial match
        if (!empty($city)) {
            $b->like('m.city', $city);
        }

        // exclude soft-deleted members
        $b->where('m.deleted_at', null);
        return $b;
    }

    /* ----------------------------------------------------------------
     |  Utility: Build a members + memberships LEFT JOIN base builder
     |  FIXED: Proper join to avoid Cartesian product
     * ---------------------------------------------------------------*/
    private function baseMembersWithMembership(): BaseBuilder
    {
        $db = \Config\Database::connect();

        // Create a derived table with latest active membership per member
        $latestMemberships = "(
            SELECT
                ms.member_id,
                ms.id,
                ms.membership_type,
                ms.status,
                ms.start_date,
                ms.updated_at
            FROM memberships ms
            INNER JOIN (
                SELECT member_id, MAX(id) as max_id
                FROM memberships
                WHERE status = 'active'
                GROUP BY member_id
            ) latest ON ms.id = latest.max_id
        )";

        $builder = $db->table('members m');
        $builder->select('m.*, ms.membership_type, ms.status AS membership_status, ms.start_date AS membership_start, ms.updated_at AS membership_updated');
        $builder->join("$latestMemberships ms", 'm.id = ms.member_id', 'left', false);

        return $builder;
    }

    /* -------------------------------------
     |  DataTables server-side boilerplate
     * ------------------------------------*/
    private function dtRespond(BaseBuilder $b, array $searchableCols = [])
    {
        $req = $this->request;
        $draw = (int) ($req->getPost('draw') ?? 1);
        $start = (int) ($req->getPost('start') ?? 0);
        $len = (int) ($req->getPost('length') ?? 25);
        $term = trim((string) ($req->getPost('search')['value'] ?? ''));

        // Clone for counts
        $bCount = clone $b;
        $recordsTotal = $bCount->countAllResults(false);

        // Search
        if ($term !== '' && $searchableCols) {
            $b->groupStart();
            foreach ($searchableCols as $col) {
                $b->orLike($col, $term);
            }
            $b->groupEnd();
        }

        // Count after filter
        $bCount2 = clone $b;
        $recordsFiltered = $bCount2->countAllResults(false);

        // Order
        $order = $this->request->getPost('order')[0] ?? null;
        if ($order) {
            $cols = $this->request->getPost('columns') ?? [];
            $idx = (int) $order['column'];
            $dir = strtoupper($order['dir']) === 'DESC' ? 'DESC' : 'ASC';
            $colName = $cols[$idx]['data'] ?? 'm.id';
            // protect known fields
            $b->orderBy($colName, $dir);
        } else {
            $b->orderBy('m.id', 'DESC');
        }

        // Page
        if ($len > 0) {
            $b->limit($len, $start);
        }

        $data = $b->get()->getResultArray();

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    /* ---------------------------------------------------------------
     |  CSV export helper - streams data efficiently (memory-safe)
     * --------------------------------------------------------------*/
    private function exportCsvStream(BaseBuilder $b, string $filename)
    {
        // Get compiled SQL for unbuffered query
        $sql = $b->getCompiledSelect();

        // Connect and execute unbuffered query
        $db = \Config\Database::connect();
        $query = $db->query($sql, [], false); // false = unbuffered

        // Set headers using CodeIgniter Response
        $this->response->setHeader('Content-Type', 'text/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $this->response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
        $this->response->setHeader('Pragma', 'no-cache');
        $this->response->setHeader('Expires', '0');

        // Send headers
        $this->response->sendHeaders();

        // Open output stream
        $out = fopen('php://output', 'w');

        // Add UTF-8 BOM for Excel compatibility
        fwrite($out, "\xEF\xBB\xBF");

        // Fetch and write first row (includes headers)
        $firstRow = $query->getUnbufferedRow('array');

        if ($firstRow) {
            // Write headers
            fputcsv($out, array_keys($firstRow));
            // Write first data row
            fputcsv($out, $firstRow);

            // Stream remaining rows
            while ($row = $query->getUnbufferedRow('array')) {
                fputcsv($out, $row);
            }
        } else {
            // Empty result - write sample headers
            fputcsv($out, ['id', 'first_name', 'last_name', 'email', 'mobile', 'city', 'status', 'membership_type']);
        }

        fclose($out);

        // Free query result
        $query->freeResult();

        // Important: exit cleanly
        exit(0);
    }

    /* =========================
     *  REPORT: Email Unknown
     * =========================*/
    public function emailUnknown()
    {
        $activeTab = 'reports';
        return view('admin/membership_reports/email_unknown', compact('activeTab'));
    }

    public function emailUnknownData()
    {
        $status = $this->request->getPost('status') ?: 'all';
        $type = $this->request->getPost('membership_type') ?: 'all';
        $city = trim((string) $this->request->getPost('city'));

        $b = $this->baseMembersWithMembership();
        $b->like('m.email', '@lcnl.org', 'before');
        $this->applyCommonFilters($b, $status, $type, $city);

        return $this->dtRespond($b, ['m.first_name', 'm.last_name', 'm.email', 'm.mobile', 'm.city', 'ms.membership_type', 'm.status']);
    }

    public function emailUnknownExport()
    {
        $status = $this->request->getGet('status') ?: 'all';
        $type = $this->request->getGet('membership_type') ?: 'all';
        $city = trim((string) $this->request->getGet('city'));

        $b = $this->baseMembersWithMembership();
        $b->like('m.email', '@lcnl.org', 'before');
        $this->applyCommonFilters($b, $status, $type, $city);
        $b->orderBy('m.id', 'DESC');

        $filename = 'email_unknown_' . date('Ymd_His') . '.csv';
        return $this->exportCsvStream($b, $filename);
    }

    /* =========================
     *  REPORT: Mobile Missing
     * =========================*/
    public function mobileMissing()
    {
        $activeTab = 'reports';
        return view('admin/membership_reports/mobile_missing', compact('activeTab'));
    }

    public function mobileMissingData()
    {
        $status = $this->request->getPost('status') ?: 'all';
        $type = $this->request->getPost('membership_type') ?: 'all';
        $city = trim((string) $this->request->getPost('city'));

        $b = $this->baseMembersWithMembership();
        $b->groupStart()->where('m.mobile', '')->orWhere('m.mobile IS NULL', null, false)->groupEnd();
        $this->applyCommonFilters($b, $status, $type, $city);

        return $this->dtRespond($b, ['m.first_name', 'm.last_name', 'm.email', 'm.mobile', 'm.city', 'ms.membership_type', 'm.status']);
    }

    public function mobileMissingExport()
    {
        $status = $this->request->getGet('status') ?: 'all';
        $type = $this->request->getGet('membership_type') ?: 'all';
        $city = trim((string) $this->request->getGet('city'));

        $b = $this->baseMembersWithMembership();
        $b->groupStart()->where('m.mobile', '')->orWhere('m.mobile IS NULL', null, false)->groupEnd();
        $this->applyCommonFilters($b, $status, $type, $city);
        $b->orderBy('m.id', 'DESC');

        $filename = 'mobile_missing_' . date('Ymd_His') . '.csv';
        return $this->exportCsvStream($b, $filename);
    }

    /* =========================
     *  REPORT: Missing Gender
     * =========================*/
    public function missingGender()
    {
        $activeTab = 'reports';
        return view('admin/membership_reports/missing_gender', compact('activeTab'));
    }

    public function missingGenderData()
    {
        $status = $this->request->getPost('status') ?: 'all';
        $type = $this->request->getPost('membership_type') ?: 'all';
        $city = trim((string) $this->request->getPost('city'));

        $b = $this->baseMembersWithMembership();
        $b->groupStart()->where('m.gender', null)->orWhere('m.gender', '')->groupEnd();
        $this->applyCommonFilters($b, $status, $type, $city);

        return $this->dtRespond($b, ['m.first_name', 'm.last_name', 'm.email', 'm.mobile', 'm.city', 'ms.membership_type', 'm.status']);
    }

    public function missingGenderExport()
    {
        $status = $this->request->getGet('status') ?: 'all';
        $type = $this->request->getGet('membership_type') ?: 'all';
        $city = trim((string) $this->request->getGet('city'));

        $b = $this->baseMembersWithMembership();
        $b->groupStart()->where('m.gender', null)->orWhere('m.gender', '')->groupEnd();
        $this->applyCommonFilters($b, $status, $type, $city);
        $b->orderBy('m.id', 'DESC');

        return $this->exportCsvStream($b, 'missing_gender_' . date('Ymd_His') . '.csv');
    }

    /* =========================
     *  REPORT: Missing DOB
     * =========================*/
    public function missingDob()
    {
        $activeTab = 'reports';
        return view('admin/membership_reports/missing_dob', compact('activeTab'));
    }

    public function missingDobData()
    {
        $status = $this->request->getPost('status') ?: 'all';
        $type = $this->request->getPost('membership_type') ?: 'all';
        $city = trim((string) $this->request->getPost('city'));

        $b = $this->baseMembersWithMembership();
        $b->groupStart()
            ->where('m.date_of_birth IS NULL', null, false)
            ->orWhere("CAST(m.date_of_birth AS CHAR) = '0000-00-00'", null, false)
            ->orWhere("CAST(m.date_of_birth AS CHAR) = ''", null, false)
            ->groupEnd();

        $this->applyCommonFilters($b, $status, $type, $city);

        return $this->dtRespond($b, ['m.first_name', 'm.last_name', 'm.email', 'm.mobile', 'm.city', 'ms.membership_type', 'm.status']);
    }

    public function missingDobExport()
    {
        $status = $this->request->getGet('status') ?: 'all';
        $type = $this->request->getGet('membership_type') ?: 'all';
        $city = trim((string) $this->request->getGet('city'));

        $b = $this->baseMembersWithMembership();
        $b->groupStart()
            ->where('m.date_of_birth IS NULL', null, false)
            ->orWhere("CAST(m.date_of_birth AS CHAR) = '0000-00-00'", null, false)
            ->orWhere("CAST(m.date_of_birth AS CHAR) = ''", null, false)
            ->groupEnd();

        $this->applyCommonFilters($b, $status, $type, $city);
        $b->orderBy('m.id', 'DESC');

        return $this->exportCsvStream($b, 'missing_dob_' . date('Ymd_His') . '.csv');
    }

    /* =========================
     *  REPORT: Not Verified
     * =========================*/
    public function notVerified()
    {
        $activeTab = 'reports';
        return view('admin/membership_reports/not_verified', compact('activeTab'));
    }

    public function notVerifiedData()
    {
        $status = $this->request->getPost('status') ?: 'all';
        $type = $this->request->getPost('membership_type') ?: 'all';
        $city = trim((string) $this->request->getPost('city'));

        $b = $this->baseMembersWithMembership();
        $b->where('m.verified_at', null);
        $this->applyCommonFilters($b, $status, $type, $city);

        return $this->dtRespond($b, ['m.first_name', 'm.last_name', 'm.email', 'm.mobile', 'm.city', 'ms.membership_type', 'm.status']);
    }

    public function notVerifiedExport()
    {
        $status = $this->request->getGet('status') ?: 'all';
        $type = $this->request->getGet('membership_type') ?: 'all';
        $city = trim((string) $this->request->getGet('city'));

        $b = $this->baseMembersWithMembership();
        $b->where('m.verified_at', null);
        $this->applyCommonFilters($b, $status, $type, $city);
        $b->orderBy('m.id', 'DESC');

        return $this->exportCsvStream($b, 'not_verified_' . date('Ymd_His') . '.csv');
    }

    /* =========================
     *  REPORT: Still Pending
     * =========================*/
    public function pendingMembers()
    {
        $activeTab = 'reports';
        return view('admin/membership_reports/pending_members', compact('activeTab'));
    }

    public function pendingMembersData()
    {
        $status = $this->request->getPost('status') ?: 'all';
        $type = $this->request->getPost('membership_type') ?: 'all';
        $city = trim((string) $this->request->getPost('city'));

        $b = $this->baseMembersWithMembership();
        $b->where('m.status', 'pending');
        $this->applyCommonFilters($b, $status, $type, $city);

        return $this->dtRespond($b, ['m.first_name', 'm.last_name', 'm.email', 'm.mobile', 'm.city', 'ms.membership_type', 'm.status']);
    }

    public function pendingMembersExport()
    {
        $status = $this->request->getGet('status') ?: 'all';
        $type = $this->request->getGet('membership_type') ?: 'all';
        $city = trim((string) $this->request->getGet('city'));

        $b = $this->baseMembersWithMembership();
        $b->where('m.status', 'pending');
        $this->applyCommonFilters($b, $status, $type, $city);
        $b->orderBy('m.id', 'DESC');

        return $this->exportCsvStream($b, 'pending_members_' . date('Ymd_His') . '.csv');
    }

    public function activeLife()
    {
        $activeTab = 'reports';
        return view('admin/membership_reports/active_life', compact('activeTab'));
    }

    public function activeLifeData()
    {
        $status = $this->request->getPost('status') ?: 'all';
        $type = 'life'; // force this report
        $city = trim((string) $this->request->getPost('city'));

        $b = $this->baseMembersWithMembership();

        // Explicit filter (avoid relying on user input)
        $b->where('ms.membership_type', 'life');


        $this->applyCommonFilters($b, $status, $type, $city);

        return $this->dtRespond($b, [
            'm.first_name',
            'm.last_name',
            'm.email',
            'm.mobile',
            'm.city',
            'ms.membership_type',
            'm.status'
        ]);
    }
    public function activeLifeExport()
    {
        $status = $this->request->getGet('status') ?: 'all';
        $type = 'life';
        $city = trim((string) $this->request->getGet('city'));

        $b = $this->baseMembersWithMembership();

        $b->where('ms.membership_type', 'Life');


        $this->applyCommonFilters($b, $status, $type, $city);
        $b->orderBy('m.id', 'DESC');

        return $this->exportCsvStream($b, 'active_life_' . date('Ymd_His') . '.csv');
    }



}
