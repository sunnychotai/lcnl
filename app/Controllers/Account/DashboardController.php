<?php

namespace App\Controllers\Account;

use App\Controllers\BaseController;
use App\Services\ChecklistService;

class DashboardController extends BaseController
{
    public function __construct(private ChecklistService $check = new ChecklistService()) {}

    public function index()
    {
        $memberId = (int) (session()->get('member_id') ?? 0);
        if (! $memberId) return redirect()->to('/member/login');

        $tasks = $this->check->memberTasks($memberId);

        return view('account/dashboard', [
            'tasks'     => $tasks,
            'memberName'=> (string) (session()->get('member_name') ?? 'Member'),
        ]);
    }
}
