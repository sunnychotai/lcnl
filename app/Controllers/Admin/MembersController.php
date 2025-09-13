<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\MemberService;

class MembersController extends BaseController
{
    private MemberService $svc;

    public function __construct()
    {
        $this->svc = new MemberService();
    }

    public function index()
    {
        $status = $this->request->getGet('status') ?? 'pending';
        $q      = trim((string) $this->request->getGet('q'));

        $members = $this->svc->list($status, $q, 50);
        $counts  = $this->svc->counts();

        return view('admin/members/index', compact('members','status','q','counts'));
    }

    public function show(int $id)
    {
        // Optional: load one member with future family info
        return view('admin/members/show', ['member' => []]);
    }

    public function activate(int $id)
    {
        $adminId = session()->get('user_id') ?? null;
        $this->svc->activate($id, $adminId);

        return redirect()->back()->with('message', 'Member activated.');
    }

    public function disable(int $id)
    {
        $this->svc->disable($id);

        return redirect()->back()->with('message', 'Member disabled.');
    }

    public function resend(int $id)
    {
        // Future: enqueue verification email
        return redirect()->back()->with('message', 'Verification email queued (coming soon).');
    }
}
