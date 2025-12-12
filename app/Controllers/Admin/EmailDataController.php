<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EmailQueueModel;
use CodeIgniter\HTTP\ResponseInterface;

class EmailDataController extends BaseController
{
    public function list(): ResponseInterface
    {
        $request = service('request');
        $model = new EmailQueueModel();
        $builder = $model->builder();

        // DataTables vars
        $draw = $request->getPost('draw');
        $start = (int) $request->getPost('start');
        $length = (int) $request->getPost('length');
        $searchVal = trim($request->getPost('search')['value'] ?? '');

        // Prevent excessive size
        if ($length <= 0 || $length > 500) {
            $length = 25;
        }

        // Base select
        $builder->select("
            id,
            to_email,
            to_name,
            subject,
            status,
            priority,
            attempts,
            scheduled_at,
            sent_at
        ");

        // Optional search
        if ($searchVal !== '') {
            $builder->groupStart()
                ->like('to_email', $searchVal)
                ->orLike('to_name', $searchVal)
                ->orLike('subject', $searchVal)
                ->orLike('status', $searchVal)
                ->groupEnd();
        }

        // Filters
        $statusFilter = $request->getPost('status');
        $priorityFilter = $request->getPost('priority');

        // Apply filters
        if (!empty($statusFilter)) {
            $builder->where('status', $statusFilter);
        }

        if (!empty($priorityFilter)) {
            $builder->where('priority', (int) $priorityFilter);
        }


        // Count filtered
        $recordsFiltered = $builder->countAllResults(false);

        // Sorting
        $order = $request->getPost('order')[0] ?? null;
        $cols = [
            0 => 'id',
            1 => 'to_email',
            2 => 'subject',
            3 => 'priority',
            4 => 'status',
            5 => 'attempts',
            6 => 'scheduled_at',
            7 => 'sent_at'
        ];

        if ($order && isset($cols[$order['column']])) {
            $builder->orderBy($cols[$order['column']], $order['dir']);
        } else {
            $builder->orderBy('id', 'DESC');
        }

        // Paging
        $builder->limit($length, $start);

        // Query
        $rows = $builder->get()->getResultArray();

        // Format rows
        $data = array_map(function ($r) {
            $badge =
                $r['status'] === 'pending' ? '<span class="badge bg-warning">Pending</span>' :
                ($r['status'] === 'sent' ? '<span class="badge bg-success">Sent</span>' :
                    ($r['status'] === 'failed' ? '<span class="badge bg-danger">Failed</span>' :
                        '<span class="badge bg-secondary">' . esc($r['status']) . '</span>'));

            $priorityBadge = match ((int) $r['priority']) {
                1 => '<span class="badge bg-danger">P1</span>',
                2 => '<span class="badge bg-warning text-dark">P2</span>',
                3 => '<span class="badge bg-primary">P3</span>',
                4 => '<span class="badge bg-secondary">P4</span>',
                5 => '<span class="badge bg-light text-dark">P5</span>',
                default => '<span class="badge bg-dark">?</span>',
            };

            return [
                $r['id'],
                esc($r['to_name']) . "<br><small>" . esc($r['to_email']) . "</small>",
                esc($r['subject']),
                $priorityBadge,
                $badge,
                $r['attempts'],
                esc($r['scheduled_at']),
                esc($r['sent_at']),
                '
                <a href="' . base_url("admin/system/emails/view/" . $r['id']) . '" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                <a href="' . base_url("admin/system/emails/retry/" . $r['id']) . '" class="btn btn-sm btn-outline-warning"><i class="bi bi-arrow-repeat"></i></a>
                <a href="' . base_url("admin/system/emails/delete/" . $r['id']) . '" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Delete this email?\')"><i class="bi bi-trash"></i></a>
                '
            ];
        }, $rows);

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $model->countAll(),
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    public function stats(): ResponseInterface
    {
        $model = new EmailQueueModel();
        $today = date('Y-m-d 00:00:00');

        return $this->response->setJSON([
            'pending' => $model->where('status', 'pending')->countAllResults(),
            'failed' => $model->where('status', 'failed')->countAllResults(),
            'sentToday' => $model->where('status', 'sent')
                ->where('sent_at >=', $today)
                ->countAllResults(),
        ]);
    }

}
