<?php

namespace App\Controllers\Account;

use App\Controllers\BaseController;
use App\Services\ChecklistService;
use App\Models\EventModel;

class DashboardController extends BaseController
{
    public function __construct(private ChecklistService $check = new ChecklistService())
    {
    }

    public function index()
    {
        log_message('debug', 'SESSION AT DASHBOARD: ' . print_r(session()->get(), true));

        $memberId = (int) (session()->get('member_id') ?? 0);
        if (!$memberId) {
            return redirect()->to('/membership/login');
        }

        $tasks = $this->check->memberTasks($memberId);

        // ✅ Upcoming events
        $events = (new EventModel())
            ->where('event_date >=', date('Y-m-d'))
            ->orderBy('event_date', 'ASC')
            ->limit(3)
            ->findAll();

        return view('account/dashboard', [
            'tasks' => $tasks,
            'memberName' => (string) (session()->get('member_name') ?? 'Member'),
            'events' => $events,
        ]);
    }
}
