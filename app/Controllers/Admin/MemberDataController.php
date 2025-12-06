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
        $model   = new MemberModel();

        // ---------------------------------------------------------
        // DataTables core parameters
        // ---------------------------------------------------------
        $draw   = (int) $request->getPost('draw');
        $start  = (int) $request->getPost('start');
        $length = (int) $request->getPost('length');
        $searchTerm = trim((string) $request->getPost('searchTerm'));
        $status     = trim((string) $request->getPost('status') ?? 'all');

        // Safety: Prevent "show all rows" (-1) or invalid
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
                ->orLike('email', $searchTerm)
                ->orLike('mobile', $searchTerm)
                ->orLike('city', $searchTerm)
                ->groupEnd();
        }

        // ---------------------------------------------------------
        // Count FILTERED rows (before limit)
        // ---------------------------------------------------------
        $recordsFiltered = $builder->countAllResults(false);

        // ---------------------------------------------------------
        // Sorting specification
        // ---------------------------------------------------------
        $order = $request->getPost('order')[0] ?? null;

        // Map DataTables column index → actual DB column
        $columnMap = [
            0 => 'id',
            1 => 'first_name',
            2 => 'email',
            3 => 'mobile',
            4 => 'city',
            5 => 'status',
            6 => 'created_at'
        ];

        if ($order) {
            $colIndex = (int) $order['column'];
            $dir = strtolower($order['dir']) === 'asc' ? 'ASC' : 'DESC';

            if (isset($columnMap[$colIndex])) {
                $builder->orderBy($columnMap[$colIndex], $dir);
            } else {
                // Default fallback
                $builder->orderBy('id', 'DESC');
            }
        } else {
            // Default ordering
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

            $statusBadge =
                '<span class="badge bg-' .
                ($r['status'] === 'active'
                    ? 'success'
                    : ($r['status'] === 'pending'
                        ? 'warning text-dark'
                        : 'secondary')
                ) .
                '">' . ucfirst($r['status']) . '</span>';

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
                'id'          => $r['id'],
                'name'        => $fullName,
                'email'       => esc($r['email']),
                'mobile'      => esc($r['mobile']),
                'city'        => esc($r['city']),
                'status_badge' => $statusBadge,
                'created_at'  => esc($r['created_at']),
                'actions'     => $actions
            ];
        }, $results);

        // ---------------------------------------------------------
        // Total rows in table (unfiltered count)
        // ---------------------------------------------------------
        $recordsTotal = $model->countAll();

        // ---------------------------------------------------------
        // Return JSON response
        // ---------------------------------------------------------
        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data
        ]);
    }
}
