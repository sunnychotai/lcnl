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
                $r['status'] === 'pending' ? '<span class="badge bg-warning">Pending</span>' : ($r['status'] === 'sent' ? '<span class="badge bg-success">Sent</span>' : ($r['status'] === 'failed' ? '<span class="badge bg-danger">Failed</span>' : ($r['status'] === 'sending' ? '<span class="badge bg-info">Sending</span>' : ($r['status'] === 'invalid' ? '<span class="badge bg-dark">Invalid</span>' :
                                '<span class="badge bg-secondary">' . esc($r['status']) . '</span>'))));

            $priorityBadge = match ((int) $r['priority']) {
                1 => '<span class="badge bg-danger">P1</span>',
                2 => '<span class="badge bg-warning text-dark">P2</span>',
                3 => '<span class="badge bg-primary">P3</span>',
                4 => '<span class="badge bg-secondary">P4</span>',
                5 => '<span class="badge bg-light text-dark">P5</span>',
                default => '<span class="badge bg-dark">?</span>',
            };

            $actions = '<div class="btn-group btn-group-sm" role="group">';
            $actions .= '<a href="' . base_url("admin/system/emails/view/" . $r['id']) . '" 
                            class="btn btn-outline-primary" title="View">
                            <i class="bi bi-eye"></i>
                         </a>';

            if ($r['status'] === 'failed' || $r['status'] === 'invalid') {
                $actions .= '<a href="' . base_url("admin/system/emails/retry/" . $r['id']) . '" 
                                class="btn btn-outline-warning" title="Retry">
                                <i class="bi bi-arrow-repeat"></i>
                             </a>';
            }

            $actions .= '<a href="' . base_url("admin/system/emails/delete/" . $r['id']) . '" 
                            class="btn btn-outline-danger" 
                            onclick="return confirm(\'Delete this email?\')" 
                            title="Delete">
                            <i class="bi bi-trash"></i>
                         </a>';
            $actions .= '</div>';

            return [
                'id'           => (int)$r['id'],
                'to_email'     => esc($r['to_name']) . '<br><small class="text-muted">' . esc($r['to_email']) . '</small>',
                'subject'      => '<span title="' . esc($r['subject']) . '">' .
                    (strlen($r['subject']) > 50 ? esc(substr($r['subject'], 0, 50)) . '...' : esc($r['subject'])) .
                    '</span>',
                'priority'     => $priorityBadge,
                'status'       => $badge,
                'attempts'     => (int)$r['attempts'],
                'scheduled_at' => $r['scheduled_at'] ? '<small>' . date('Y-m-d H:i', strtotime($r['scheduled_at'])) . '</small>' : '–',
                'sent_at'      => $r['sent_at'] ? '<small>' . date('Y-m-d H:i', strtotime($r['sent_at'])) . '</small>' : '–',
                'actions'      => $actions,
            ];
        }, $rows);

        return $this->response->setJSON([
            'draw' => (int)$draw,
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
