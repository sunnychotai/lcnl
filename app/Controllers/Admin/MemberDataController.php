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

        if ($length <= 0 || $length > 500) {
            $length = 25;
        }

        // ---------------------------------------------------------
        // Base Query
        // ---------------------------------------------------------
        $builder = $model->builder();

        $builder->select("
            id,
            first_name,
            last_name,
            email,
            is_valid_email,
            mobile,
            city,
            status,
            created_at
        ");

        // ---------------------------------------------------------
        // Status Filter
        // ---------------------------------------------------------
        if ($status !== 'all') {
            $builder->where('status', $status);
        }

        // ---------------------------------------------------------
        // Search Filter
        // ---------------------------------------------------------
        if ($searchTerm !== '') {
            $builder->groupStart()
                ->like('first_name', $searchTerm)
                ->orLike('last_name', $searchTerm)
                ->orWhere("CONCAT(first_name, ' ', last_name) LIKE", "%{$searchTerm}%")
                ->orWhere("CONCAT(last_name, ' ', first_name) LIKE", "%{$searchTerm}%")
                ->orLike('email', $searchTerm)
                ->orLike('mobile', $searchTerm)
                ->orLike('city', $searchTerm)
                ->groupEnd();
        }

        // ---------------------------------------------------------
        // Count FILTERED rows
        // ---------------------------------------------------------
        $recordsFiltered = $builder->countAllResults(false);

        // ---------------------------------------------------------
        // Sorting
        // ---------------------------------------------------------
        $order = $request->getPost('order')[0] ?? null;

        $columnMap = [
            0 => 'id',
            1 => 'first_name',
            2 => 'email',
            3 => 'is_valid_email', // 👈 NEW (Email Valid column)
            4 => 'mobile',
            5 => 'city',
            6 => 'status',
            7 => 'created_at'
        ];


        if ($order) {
            $colIndex = (int) $order['column'];
            $dir = strtolower($order['dir']) === 'asc' ? 'ASC' : 'DESC';

            $builder->orderBy($columnMap[$colIndex] ?? 'id', $dir);
        } else {
            $builder->orderBy('id', 'DESC');
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
