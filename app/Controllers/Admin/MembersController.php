<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Services\MemberService;

class MembersController extends BaseController
{
    private MemberModel $members;
    private MemberService $svc;

    public function __construct()
    {
        $this->members = new MemberModel();
        $this->svc     = new MemberService();
    }

    /** List members with basic filters & search */
    public function index()
    {
        $status = $this->request->getGet('status') ?: 'pending'; // pending|active|disabled|all
        $q      = trim((string) $this->request->getGet('q'));

        $builder = $this->members->builder();
        $builder->select('*');

        if ($status !== 'all') {
            $builder->where('status', $status);
        }

        if ($q !== '') {
            $builder->groupStart()
                ->like('first_name', $q)
                ->orLike('last_name', $q)
                ->orLike('email', $q)
                ->orLike('mobile', $q)
            ->groupEnd();
        }

        $builder->orderBy('created_at', 'DESC');
        $rows = $builder->get(100)->getResultArray();

        // Quick counts for tabs
        $counts = [
            'pending'  => (int) $this->members->where('status','pending')->countAllResults(false),
            'active'   => (int) $this->members->where('status','active')->countAllResults(false),
            'disabled' => (int) $this->members->where('status','disabled')->countAllResults(false),
            'all'      => (int) $this->members->countAllResults(),
        ];

        return view('admin/membership/members/index', compact('rows','status','q','counts'));
    }

    /** Show one member (read-only details + actions) */
    public function show($id)
    {
        $id = (int) $id;
        $m  = $this->members->find($id);
        if (! $m) {
            return redirect()->to('/admin/membership/members')->with('error', 'Member not found.');
        }
        return view('admin/membership/members/show', compact('m'));
    }

    /** Activate */
    public function activate($id)
    {
        $this->svc->activate((int) $id, (int) (session('user_id') ?? 0));
        return redirect()->back()->with('message', 'Member activated.');
    }

    /** Disable */
    public function disable($id)
    {
        $this->svc->disable((int) $id);
        return redirect()->back()->with('message', 'Member disabled.');
    }

    /** Resend verification (stub until email queue is enabled) */
    public function resend($id)
    {
        return redirect()->back()->with('message', 'Email queue not enabled yet.');
    }
}
