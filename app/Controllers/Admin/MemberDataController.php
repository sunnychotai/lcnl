<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use CodeIgniter\HTTP\ResponseInterface;

class MemberDataController extends BaseController
{
    public function list(): ResponseInterface
    {
        $request = service('request');
        $model = new MemberModel();

        // ---------------------------------------------------------
        // DataTables core parameters
        // ---------------------------------------------------------
        $draw = (int) $request->getPost('draw');
        $start = (int) $request->getPost('start');
        $length = (int) $request->getPost('length');
        $searchTerm = trim((string) $request->getPost('searchTerm'));
        $status = trim((string) ($request->getPost('status') ?? 'all'));
        $membershipType = strtolower(trim((string) $request->getPost('membershipType')));

        if ($length <= 0 || $length > 500) {
            $length = 25;
        }

        // ---------------------------------------------------------
        // Base Query
        // ---------------------------------------------------------
        $db = \Config\Database::connect();
        $memberships = $db->prefixTable('memberships');

        // Same rule as the membership reports: the latest active membership,
        // or the latest of any status if none is active.
        $currentMembership = "ms.id = (
            SELECT x.id FROM {$memberships} x
            WHERE x.member_id = m.id
            ORDER BY CASE WHEN x.status = 'active' THEN 0 ELSE 1 END, x.id DESC
            LIMIT 1
        )";

        $builder = $db->table('members m');

        $builder->select("
            m.id,
            m.first_name,
            m.last_name,
            m.email,
            m.is_valid_email,
            m.mobile,
            m.city,
            m.status,
            m.created_at,
            ms.membership_type
        ");

        $builder->join("{$memberships} ms", $currentMembership, 'left', false);

        // ---------------------------------------------------------
        // Status Filter
        // ---------------------------------------------------------
        if ($status !== 'all') {
            $builder->where('m.status', $status);
        }

        // ---------------------------------------------------------
        // Membership Type Filter (Standard = anything not Life, as in the reports)
        // ---------------------------------------------------------
        if ($membershipType === 'life') {
            $builder->where("LOWER(ms.membership_type) = 'life'", null, false);
        } elseif ($membershipType === 'standard') {
            $builder->where("COALESCE(LOWER(ms.membership_type), '') <> 'life'", null, false);
        }

        // ---------------------------------------------------------
        // Search Filter
        // ---------------------------------------------------------
        if ($searchTerm !== '') {
            $builder->groupStart()
                ->like('m.first_name', $searchTerm)
                ->orLike('m.last_name', $searchTerm)
                ->orWhere("CONCAT(m.first_name, ' ', m.last_name) LIKE", "%{$searchTerm}%")
                ->orWhere("CONCAT(m.last_name, ' ', m.first_name) LIKE", "%{$searchTerm}%")
                ->orLike('m.email', $searchTerm)
                ->orLike('m.mobile', $searchTerm)
                ->orLike('m.city', $searchTerm)
                ->groupEnd();
        }

        // ---------------------------------------------------------
        // Count FILTERED rows
        // ---------------------------------------------------------
        $recordsFiltered = $builder->countAllResults(false);

        // ---------------------------------------------------------
        // Sorting (indexes match the table columns; 0 is the checkbox)
        // ---------------------------------------------------------
        $order = $request->getPost('order')[0] ?? null;

        $columnMap = [
            1 => 'm.id',
            2 => 'm.first_name',
            5 => 'm.mobile',
            6 => 'm.city',
            7 => 'ms.membership_type',
            8 => 'm.status',
            9 => 'm.created_at',
        ];

        if ($order) {
            $colIndex = (int) $order['column'];
            $dir = strtolower($order['dir']) === 'asc' ? 'ASC' : 'DESC';

            $builder->orderBy($columnMap[$colIndex] ?? 'm.id', $dir);
        } else {
            $builder->orderBy('m.id', 'DESC');
        }

        // ---------------------------------------------------------
        // Paging
        // ---------------------------------------------------------
        $builder->limit($length, $start);

        // ---------------------------------------------------------
        // Fetch records
        // ---------------------------------------------------------
        $results = $builder->get()->getResultArray();

        // ---------------------------------------------------------
        // Format rows for DataTables
        // ---------------------------------------------------------
        $data = array_map(function ($r) {

            $fullName = esc($r['first_name'] . ' ' . $r['last_name']);
            $email = trim($r['email'] ?? '');
            $isValid = (int) ($r['is_valid_email'] ?? 0);

            /* -------------------------------------------------
             * Email (TEXT ONLY)
             * ------------------------------------------------- */
            $emailHtml = $email !== ''
                ? '<span class="email-primary">' . esc($email) . '</span>'
                : '<span class="text-muted">—</span>';

            /* -------------------------------------------------
             * Email validity (traffic light + toggle)
             * ------------------------------------------------- */
            if ($email !== '') {
                $emailValidityHtml = '
            <div class="d-flex justify-content-center align-items-center gap-2">
                <span
                    class="email-dot ' . ($isValid ? 'email-dot-valid' : 'email-dot-invalid') . '"
                    title="' . ($isValid ? 'Email valid' : 'Email invalid') . '">
                </span>

                <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary js-toggle-email-validity"
                    data-id="' . (int) $r['id'] . '"
                    data-email="' . esc($email) . '"
                    title="' . ($isValid ? 'Mark email invalid' : 'Mark email valid') . '">
                    <i class="bi ' . ($isValid ? 'bi-envelope-x' : 'bi-envelope-check') . '"></i>
                </button>
            </div>';
            } else {
                $emailValidityHtml = '<span class="text-muted">—</span>';
            }

            /* -------------------------------------------------
             * Status badge
             * ------------------------------------------------- */
            $statusBadge =
                '<span class="badge bg-' .
                ($r['status'] === 'active'
                    ? 'success'
                    : ($r['status'] === 'pending'
                        ? 'warning text-dark'
                        : 'secondary')
                ) . '">' . ucfirst($r['status']) . '</span>';

            /* -------------------------------------------------
             * Membership type badge
             * ------------------------------------------------- */
            $type = strtolower((string) ($r['membership_type'] ?? ''));
            if ($type === 'life') {
                $typeBadge = '<span class="badge-type badge-life"><i class="bi bi-star-fill"></i> Life</span>';
            } elseif ($type !== '') {
                $typeBadge = '<span class="badge-type badge-standard">' . esc(ucfirst($type)) . '</span>';
            } else {
                $typeBadge = '<span class="text-muted">—</span>';
            }

            /* -------------------------------------------------
             * Actions
             * ------------------------------------------------- */
            $actions = '
        <div class="d-flex justify-content-end gap-1">
            <a href="' . base_url('admin/membership/' . $r['id']) . '"
               class="btn-action" title="View">
                <i class="bi bi-eye"></i>
            </a>
            <a href="' . base_url('admin/membership/' . $r['id'] . '/edit') . '"
               class="btn-action" title="Edit">
                <i class="bi bi-pencil"></i>
            </a>
        </div>';

            return [
                'id' => (int) $r['id'],
                'name' => $fullName,
                'email_html' => $emailHtml,
                'email_validity_html' => $emailValidityHtml, // 👈 NEW FIELD
                'mobile' => esc($r['mobile'] ?? ''),
                'city' => esc($r['city'] ?? ''),
                'membership_type_badge' => $typeBadge,
                'status_badge' => $statusBadge,
                'created_at' => esc($r['created_at']),
                'actions' => $actions,
            ];
        }, $results);


        // ---------------------------------------------------------
        // Total rows (unfiltered)
        // ---------------------------------------------------------
        $recordsTotal = $model->countAll();

        // ---------------------------------------------------------
        // Return JSON
        // ---------------------------------------------------------
        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,

            // ✅ IMPORTANT: refresh CSRF for DataTables + AJAX actions
            'csrf' => [
                'tokenName' => csrf_token(),
                'tokenHash' => csrf_hash(),
            ],
        ]);
    }
}
