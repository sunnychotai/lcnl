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

        // Count filtered
        $recordsFiltered = $builder->countAllResults(false);

        // Sorting
        $order = $request->getPost('order')[0] ?? null;
        $cols = [
            0 => 'id',
            1 => 'to_email',
            2 => 'subject',
            3 => 'status',
            4 => 'priority',
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

            return [
                $r['id'],
                esc($r['to_name']) . "<br><small>" . esc($r['to_email']) . "</small>",
                esc($r['subject']),
                $badge,
                $r['priority'],
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
}
