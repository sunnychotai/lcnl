<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\MembershipModel;
use CodeIgniter\Database\BaseBuilder;

/**
 * MembershipReportsController
 *
 * Supports:
 * - Dashboard: GET  /admin/membership/reports
 * - Export all: GET /admin/membership/reports/export?format=csv|xlsx|pdf (CSV implemented here)
 * - Individual report pages:
 *   - /admin/membership/reports/active-life
 *   - /admin/membership/reports/email-invalid
 *   - /admin/membership/reports/mobile-missing
 *   - /admin/membership/reports/missing-gender
 *   - /admin/membership/reports/missing-dob
 *   - /admin/membership/reports/not-verified
 *   - /admin/membership/reports/disabled
 *   - /admin/membership/reports/pending
 *
 * Individual report pages support: ?export=csv
 * (routes can also call the explicit /data and /export endpoints).
 */
class MembershipReportsController extends BaseController
{
    protected MemberModel $members;
    protected MembershipModel $memberships;

    public function __construct()
    {
        $this->members = new MemberModel();           // table: members
        $this->memberships = new MembershipModel();   // table: memberships
    }

    /* --------------------------
     |  DASHBOARD (cards + list)
     * -------------------------*/
    public function index()
    {
        // Use fresh instances so WHEREs don't stack
        $stats = [
            'total' => (int) (new MemberModel())->where('deleted_at', null)->countAllResults(),

            'life' => (int) (new MembershipModel())
                ->where('membership_type', 'life')
                ->where('status', 'active')
                ->countAllResults(),

            'standard' => (int) (new MembershipModel())
                ->where('membership_type', 'standard')
                ->where('status', 'active')
                ->countAllResults(),

            'disabled' => (int) (new MemberModel())
                ->where('status', 'disabled')
                ->where('deleted_at', null)
                ->countAllResults(),

            'email_invalid' => (int) (new MemberModel())
                ->where('is_valid_email', 0)
                ->where('deleted_at', null)
                ->countAllResults(),

            'mobile_missing' => (int) (new MemberModel())
                ->groupStart()
                ->where('mobile', '')
                ->orWhere('mobile IS NULL', null, false)
                ->groupEnd()
                ->where('deleted_at', null)
                ->countAllResults(),
        ];

        $stats += [
            'missing_gender' => (int) (new MemberModel())
                ->groupStart()
                ->where('gender', null)
                ->orWhere('gender', '')
                ->groupEnd()
                ->where('deleted_at', null)
                ->countAllResults(),

            'missing_dob' => (int) (new MemberModel())
                ->groupStart()
                ->where('date_of_birth IS NULL', null, false)
                ->orWhere("CAST(date_of_birth AS CHAR) = '0000-00-00'", null, false)
                ->orWhere("CAST(date_of_birth AS CHAR) = ''", null, false)
                ->groupEnd()
                ->where('deleted_at', null)
                ->countAllResults(),

            'not_verified' => (int) (new MemberModel())
                ->where('verified_at', null)
                ->where('deleted_at', null)
                ->where('is_valid_email', 1)
                ->countAllResults(),

            'pending' => (int) (new MemberModel())
                ->where('status', 'pending')
                ->where('deleted_at', null)
                ->where('is_valid_email', 1)
                ->countAllResults(),
        ];

        $activeTab = 'reports';

        // ✅ Dashboard view (your new UI page)
        return view('admin/membership/reports', compact('stats', 'activeTab'));
    }

    /* --------------------------------------------------
     |  EXPORT ALL (dashboard dropdown)
     |  GET: /admin/membership/reports/export?format=csv|xlsx|pdf
     * -------------------------------------------------*/
    public function export()
    {
        $format = strtolower((string) $this->request->getGet('format'));

        if ($format === '' || $format === 'csv') {
            $b = $this->baseMembersWithMembership();
            $b->where('m.deleted_at', null);
            $b->orderBy('m.id', 'DESC');
            return $this->exportCsvStream($b, 'membership_reports_all_' . date('Ymd_His') . '.csv');
        }

        return redirect()->back()->with('error', 'Export format not implemented yet: ' . $format);
    }

    /* --------------------------------------------------
     |  FILTERS: status, membership_type, city (optional)
     * -------------------------------------------------*/
    private function applyCommonFilters(BaseBuilder $b, ?string $status, ?string $type, ?string $city): BaseBuilder
    {
        if ($status && $status !== 'all') {
            $b->where('m.status', $status);
        }

        if ($type && $type !== 'all') {
            $b->where('ms.membership_type', $type);
        }

        if (!empty($city)) {
            $b->like('m.city', $city);
        }

        // Always exclude soft-deleted members
        $b->where('m.deleted_at', null);

        return $b;
    }

    /* ----------------------------------------------------------------
     |  Utility: members + latest ACTIVE membership (LEFT JOIN)
     * ---------------------------------------------------------------*/
    private function baseMembersWithMembership(): BaseBuilder
    {
        $db = \Config\Database::connect();

        // Latest ACTIVE membership per member (by max id)
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

        $b = $db->table('members m');

        $b->select('
            m.id,
            m.first_name,
            m.last_name,
            m.email,
            m.is_valid_email,
            m.mobile,
            m.city,
            m.status,
            m.verified_at,
            m.disabled_reason,
            m.disabled_notes,
            m.created_at,
            m.updated_at,
            ms.membership_type,
            ms.status AS membership_status,
            ms.start_date AS membership_start,
            ms.updated_at AS membership_updated
        ');

        $b->join("$latestMemberships ms", 'm.id = ms.member_id', 'left', false);

        return $b;
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

        // Total BEFORE search term is applied (but AFTER report-specific filters)
        $bCount = clone $b;
        $recordsTotal = (int) $bCount->countAllResults(false);

        // Search
        if ($term !== '' && $searchableCols) {
            $b->groupStart();
            foreach ($searchableCols as $col) {
                $b->orLike($col, $term);
            }
            $b->groupEnd();
        }

        // Total AFTER search
        $bCount2 = clone $b;
        $recordsFiltered = (int) $bCount2->countAllResults(false);

        // Ordering (safe map based on DataTables columns[x][data])
        $order = $req->getPost('order')[0] ?? null;
        if ($order) {
            $idx = (int) ($order['column'] ?? 0);
            $dir = strtoupper((string) ($order['dir'] ?? 'DESC')) === 'ASC' ? 'ASC' : 'DESC';

            $columns = $req->getPost('columns') ?? [];
            $requested = $columns[$idx]['data'] ?? 'id';

            $safeMap = [
                'id' => 'm.id',
                'first_name' => 'm.first_name',
                'last_name' => 'm.last_name',
                'name' => 'm.first_name',
                'email' => 'm.email',
                'mobile' => 'm.mobile',
                'city' => 'm.city',
                'status' => 'm.status',
                'created_at' => 'm.created_at',
                'membership_type' => 'ms.membership_type',
                'membership_status' => 'ms.status',
                'membership_start' => 'ms.start_date',
                'membership_updated' => 'ms.updated_at',
            ];

            $colName = $safeMap[$requested] ?? 'm.id';
            $b->orderBy($colName, $dir);
        } else {
            $b->orderBy('m.id', 'DESC');
        }

        // Paging
        if ($len > 0) {
            $b->limit($len, $start);
        }

        $data = $b->get()->getResultArray();

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
            'csrf' => [
                'tokenName' => csrf_token(),
                'tokenHash' => csrf_hash(),
            ],
        ]);
    }

    /* ---------------------------------------------------------------
     |  CSV export helper (streams)
     * --------------------------------------------------------------*/
    private function exportCsvStream(BaseBuilder $b, string $filename)
    {
        $sql = $b->getCompiledSelect();

        $db = \Config\Database::connect();
        $query = $db->query($sql, [], false); // unbuffered

        $this->response->setHeader('Content-Type', 'text/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $this->response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
        $this->response->setHeader('Pragma', 'no-cache');
        $this->response->setHeader('Expires', '0');

        $this->response->sendHeaders();

        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM

        $firstRow = $query->getUnbufferedRow('array');

        if ($firstRow) {
            fputcsv($out, array_keys($firstRow));
            fputcsv($out, $firstRow);

            while ($row = $query->getUnbufferedRow('array')) {
                fputcsv($out, $row);
            }
        } else {
            fputcsv($out, ['id', 'first_name', 'last_name', 'email', 'mobile', 'city', 'status', 'membership_type']);
        }

        fclose($out);
        $query->freeResult();
        exit(0);
    }

    /* =========================================================
     * REPORT PAGES
     * (Keeping your existing view folder: admin/membership_reports/*)
     * =========================================================*/

    // -------------------------
    // Active Life Members
    // -------------------------
    public function activeLife()
    {
        if (strtolower((string) $this->request->getGet('export')) === 'csv') {
            return $this->activeLifeExport();
        }

        $activeTab = 'reports';
        return view('admin/membership_reports/active_life', compact('activeTab'));
    }

    public function activeLifeData()
    {
        $status = $this->request->getPost('status') ?: 'all';
        $city = trim((string) $this->request->getPost('city'));

        $b = $this->baseMembersWithMembership();

        // Force report definition
        $b->where('ms.membership_type', 'life');
        $b->where('ms.status', 'active'); // ✅ correct

        $this->applyCommonFilters($b, $status, 'life', $city);

        return $this->dtRespond($b, [
            'm.first_name',
            'm.last_name',
            'm.email',
            'm.mobile',
            'm.city',
            'ms.membership_type',
            'm.status',
        ]);
    }

    public function activeLifeExport()
    {
        $status = $this->request->getGet('status') ?: 'all';
        $city = trim((string) $this->request->getGet('city'));

        $b = $this->baseMembersWithMembership();

        $b->where('ms.membership_type', 'life');
        $b->where('ms.status', 'active'); // ✅ FIXED (was ms.membership_status)

        $this->applyCommonFilters($b, $status, 'life', $city);
        $b->orderBy('m.id', 'DESC');

        return $this->exportCsvStream($b, 'active_life_' . date('Ymd_His') . '.csv');
    }

    // -------------------------
    // Email Invalid
    // -------------------------
    public function emailInvalid()
    {
        if (strtolower((string) $this->request->getGet('export')) === 'csv') {
            return $this->emailInvalidExport();
        }

        $activeTab = 'reports';
        return view('admin/membership_reports/email_invalid', compact('activeTab'));
    }

    public function emailInvalidData()
    {
        $status = $this->request->getPost('status') ?: 'all';
        $type = $this->request->getPost('membership_type') ?: 'all';
        $city = trim((string) $this->request->getPost('city'));

        $b = $this->baseMembersWithMembership();

        // "invalid email" definition: marked invalid OR lcnl.org (if you want that)
        $b->groupStart()
            ->where('m.is_valid_email', 0)
            ->orLike('m.email', '@lcnl.org', 'after')
            ->groupEnd();

        $this->applyCommonFilters($b, $status, $type, $city);

        return $this->dtRespond($b, [
            'm.first_name',
            'm.last_name',
            'm.email',
            'm.mobile',
            'm.city',
            'ms.membership_type',
            'm.status',
        ]);
    }

    public function emailInvalidExport()
    {
        $status = $this->request->getGet('status') ?: 'all';
        $type = $this->request->getGet('membership_type') ?: 'all';
        $city = trim((string) $this->request->getGet('city'));

        $b = $this->baseMembersWithMembership();

        $b->groupStart()
            ->where('m.is_valid_email', 0)
            ->orLike('m.email', '@lcnl.org', 'after')
            ->groupEnd();

        $this->applyCommonFilters($b, $status, $type, $city);
        $b->orderBy('m.id', 'DESC');

        return $this->exportCsvStream($b, 'email_invalid_' . date('Ymd_His') . '.csv');
    }

    // -------------------------
    // Mobile Missing
    // -------------------------
    public function mobileMissing()
    {
        if (strtolower((string) $this->request->getGet('export')) === 'csv') {
            return $this->mobileMissingExport();
        }

        $activeTab = 'reports';
        return view('admin/membership_reports/mobile_missing', compact('activeTab'));
    }

    public function mobileMissingData()
    {
        $status = $this->request->getPost('status') ?: 'all';
        $type = $this->request->getPost('membership_type') ?: 'all';
        $city = trim((string) $this->request->getPost('city'));

        $b = $this->baseMembersWithMembership();
        $b->groupStart()
            ->where('m.mobile', '')
            ->orWhere('m.mobile IS NULL', null, false)
            ->groupEnd();

        $this->applyCommonFilters($b, $status, $type, $city);

        return $this->dtRespond($b, [
            'm.first_name',
            'm.last_name',
            'm.email',
            'm.mobile',
            'm.city',
            'ms.membership_type',
            'm.status',
        ]);
    }

    public function mobileMissingExport()
    {
        $status = $this->request->getGet('status') ?: 'all';
        $type = $this->request->getGet('membership_type') ?: 'all';
        $city = trim((string) $this->request->getGet('city'));

        $b = $this->baseMembersWithMembership();
        $b->groupStart()
            ->where('m.mobile', '')
            ->orWhere('m.mobile IS NULL', null, false)
            ->groupEnd();

        $this->applyCommonFilters($b, $status, $type, $city);
        $b->orderBy('m.id', 'DESC');

        return $this->exportCsvStream($b, 'mobile_missing_' . date('Ymd_His') . '.csv');
    }

    // -------------------------
    // Missing Gender
    // -------------------------
    public function missingGender()
    {
        if (strtolower((string) $this->request->getGet('export')) === 'csv') {
            return $this->missingGenderExport();
        }

        $activeTab = 'reports';
        return view('admin/membership_reports/missing_gender', compact('activeTab'));
    }

    public function missingGenderData()
    {
        $status = $this->request->getPost('status') ?: 'all';
        $type = $this->request->getPost('membership_type') ?: 'all';
        $city = trim((string) $this->request->getPost('city'));

        $b = $this->baseMembersWithMembership();
        $b->groupStart()
            ->where('m.gender', null)
            ->orWhere('m.gender', '')
            ->groupEnd();

        $this->applyCommonFilters($b, $status, $type, $city);

        return $this->dtRespond($b, [
            'm.first_name',
            'm.last_name',
            'm.email',
            'm.mobile',
            'm.city',
            'ms.membership_type',
            'm.status',
        ]);
    }

    public function missingGenderExport()
    {
        $status = $this->request->getGet('status') ?: 'all';
        $type = $this->request->getGet('membership_type') ?: 'all';
        $city = trim((string) $this->request->getGet('city'));

        $b = $this->baseMembersWithMembership();
        $b->groupStart()
            ->where('m.gender', null)
            ->orWhere('m.gender', '')
            ->groupEnd();

        $this->applyCommonFilters($b, $status, $type, $city);
        $b->orderBy('m.id', 'DESC');

        return $this->exportCsvStream($b, 'missing_gender_' . date('Ymd_His') . '.csv');
    }

    // -------------------------
    // Missing DOB
    // -------------------------
    public function missingDob()
    {
        if (strtolower((string) $this->request->getGet('export')) === 'csv') {
            return $this->missingDobExport();
        }

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

        return $this->dtRespond($b, [
            'm.first_name',
            'm.last_name',
            'm.email',
            'm.mobile',
            'm.city',
            'ms.membership_type',
            'm.status',
        ]);
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

    // -------------------------
    // Not Verified
    // -------------------------
    public function notVerified()
    {
        if (strtolower((string) $this->request->getGet('export')) === 'csv') {
            return $this->notVerifiedExport();
        }

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

        return $this->dtRespond($b, [
            'm.first_name',
            'm.last_name',
            'm.email',
            'm.mobile',
            'm.city',
            'ms.membership_type',
            'm.status',
        ]);
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

    // -------------------------
    // Pending
    // -------------------------
    public function pending()
    {
        if (strtolower((string) $this->request->getGet('export')) === 'csv') {
            return $this->pendingExport();
        }

        $activeTab = 'reports';
        return view('admin/membership_reports/pending_members', compact('activeTab'));
    }

    public function pendingData()
    {
        $status = $this->request->getPost('status') ?: 'all';
        $type = $this->request->getPost('membership_type') ?: 'all';
        $city = trim((string) $this->request->getPost('city'));

        $b = $this->baseMembersWithMembership();
        $b->where('m.status', 'pending');

        $this->applyCommonFilters($b, $status, $type, $city);

        return $this->dtRespond($b, [
            'm.first_name',
            'm.last_name',
            'm.email',
            'm.mobile',
            'm.city',
            'ms.membership_type',
            'm.status',
        ]);
    }

    public function pendingExport()
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

    // Backward compatible aliases
    public function pendingMembers()
    {
        return $this->pending();
    }
    public function pendingMembersData()
    {
        return $this->pendingData();
    }
    public function pendingMembersExport()
    {
        return $this->pendingExport();
    }

    // -------------------------
    // Disabled
    // -------------------------
    public function disabled()
    {
        if (strtolower((string) $this->request->getGet('export')) === 'csv') {
            return $this->disabledExport();
        }

        $activeTab = 'reports';
        return view('admin/membership_reports/disabled', compact('activeTab'));
    }

    public function disabledData()
    {
        $type = $this->request->getPost('membership_type') ?: 'all';
        $city = trim((string) $this->request->getPost('city'));

        $b = $this->baseMembersWithMembership();
        $b->where('m.status', 'disabled');

        $this->applyCommonFilters($b, 'disabled', $type, $city);

        return $this->dtRespond($b, [
            'm.first_name',
            'm.last_name',
            'm.email',
            'm.mobile',
            'm.city',
            'ms.membership_type',
            'm.status',
            'm.disabled_reason',
            'm.disabled_notes',
        ]);
    }

    public function disabledExport()
    {
        $type = $this->request->getGet('membership_type') ?: 'all';
        $city = trim((string) $this->request->getGet('city'));

        $b = $this->baseMembersWithMembership();
        $b->where('m.status', 'disabled');

        $this->applyCommonFilters($b, 'disabled', $type, $city);
        $b->orderBy('m.id', 'DESC');

        return $this->exportCsvStream($b, 'disabled_members_' . date('Ymd_His') . '.csv');
    }
}
