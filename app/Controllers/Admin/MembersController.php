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

    // Use model query builder
    $builder = $this->members;

    if ($status !== 'all') {
        $builder = $builder->where('status', $status);
    }

    if ($q !== '') {
        $builder = $builder->groupStart()
            ->like('first_name', $q)
            ->orLike('last_name', $q)
            ->orLike('email', $q)
            ->orLike('mobile', $q)
        ->groupEnd();
    }

    // Use paginate instead of get()
    $rows = $builder->orderBy('created_at', 'DESC')
        ->paginate(10); // show 10 per page

    // Quick counts for tabs
    // Quick counts for tabs
$counts = [
    'pending'  => (int) (new MemberModel())->where('status','pending')->countAllResults(),
    'active'   => (int) (new MemberModel())->where('status','active')->countAllResults(),
    'disabled' => (int) (new MemberModel())->where('status','disabled')->countAllResults(),
    'all'      => (int) (new MemberModel())->countAllResults(),
];


    // Get pager instance for the view
    $pager = $this->members->pager;

    return view('admin/membership/members/index', compact('rows','status','q','counts','pager'));
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

    public function export()
{
    $status = $this->request->getGet('status') ?: 'all';
    $q      = trim((string) $this->request->getGet('q'));

    $builder = $this->members;

    if ($status !== 'all') {
        $builder = $builder->where('status', $status);
    }

    if ($q !== '') {
        $builder = $builder->groupStart()
            ->like('first_name', $q)
            ->orLike('last_name', $q)
            ->orLike('email', $q)
            ->orLike('mobile', $q)
        ->groupEnd();
    }

    $rows = $builder->orderBy('created_at', 'DESC')->findAll();

    // CSV headers
    $filename = 'members_export_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    // Write header row
    fputcsv($output, ['ID', 'First Name', 'Last Name', 'Email', 'Mobile', 'Status', 'Created At']);

    // Write data rows
    foreach ($rows as $r) {
        fputcsv($output, [
            $r['id'],
            $r['first_name'] ?? '',
            $r['last_name'] ?? '',
            $r['email'] ?? '',
            $r['mobile'] ?? '',
            $r['status'] ?? '',
            $r['created_at'] ?? ''
        ]);
    }

    fclose($output);
    exit;
}

}
