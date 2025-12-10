<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CronLogModel;

class CronController extends BaseController
{
    public function index()
    {
        $model = new CronLogModel();

        $logs = $model
            ->orderBy('id', 'DESC')
            ->paginate(50);

        return view('admin/system/cron_logs', [
            'logs' => $logs,
            'pager' => $model->pager,   // ✅ correct
        ]);
    }

    public function show($id)
    {
        $model = new CronLogModel();
        $log = $model->find((int) $id);

        if (!$log) {
            return redirect()->to('/admin/system/cron-logs')
                ->with('error', 'Log not found.');
        }

        return view('admin/system/cron_logs_view', [
            'log' => $log,
        ]);
    }
}
