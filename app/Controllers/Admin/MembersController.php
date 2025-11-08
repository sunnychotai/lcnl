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

    /** List members with filters, search, AJAX */
    public function index()
    {
        $status = $this->request->getGet('status') ?: 'pending'; // pending|active|disabled|all
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
                ->orLike('city', $q)
                ->groupEnd();
        }

        $rows = $builder->orderBy('created_at', 'DESC')->paginate(10);
        $pager = $this->members->pager;

        $counts = [
            'pending'  => (int) (new MemberModel())->where('status', 'pending')->countAllResults(),
            'active'   => (int) (new MemberModel())->where('status', 'active')->countAllResults(),
            'disabled' => (int) (new MemberModel())->where('status', 'disabled')->countAllResults(),
            'all'      => (int) (new MemberModel())->countAllResults(),
        ];

        // AJAX mode (for dynamic search/filter)
        if ($this->request->isAJAX() || $this->request->getGet('ajax')) {
            $rowsHtml  = view('admin/membership/_rows', [
                'rows' => $rows,
                'status' => $status,
                'q' => $q,
                'pager' => $pager
            ]);
            $pagerHtml = $pager->links('default', 'default_full');
            return $this->response->setJSON([
                'rowsHtml'  => $rowsHtml,
                'pagerHtml' => $pagerHtml,
            ]);
        }

        return view('admin/membership/index', compact('rows', 'status', 'q', 'counts', 'pager'));
    }

    /** Show one member */
    public function show($id)
    {
        $id = (int) $id;
        $m  = $this->members->find($id);
        if (! $m) {
            return redirect()->to('/admin/membership')->with('error', 'Member not found.');
        }
        return view('admin/membership/show', compact('m'));
    }

    /** Edit form */
    public function edit($id)
    {
        $id = (int) $id;
        $m  = $this->members->find($id);
        if (! $m) {
            return redirect()->to('/admin/membership')->with('error', 'Member not found.');
        }
        return view('admin/membership/edit', compact('m'));
    }

    /** Update submission */
    public function update($id)
    {
        $id = (int) $id;
        $payload = $this->request->getPost();

        $rules = [
            'first_name' => 'required|min_length[2]',
            'last_name'  => 'required|min_length[2]',
            'email'      => "required|valid_email|is_unique[members.email,id,{$id}]",
            'mobile'     => "permit_empty|is_unique[members.mobile,id,{$id}]",
            'status'     => 'required|in_list[pending,active,disabled]',
            'postcode'   => 'permit_empty|max_length[12]',
            'city'       => 'permit_empty|max_length[100]',
            'address1'   => 'permit_empty|max_length[255]',
            'address2'   => 'permit_empty|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode(' ', $this->validator->getErrors()));
        }

        unset($payload['password_hash']);
        $payload['updated_at'] = date('Y-m-d H:i:s');

        $this->members->update($id, $payload);
        return redirect()->to(base_url("admin/membership/{$id}"))
            ->with('message', 'Member updated successfully.');
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

    /** Resend verification */
    public function resend($id)
    {
        return redirect()->back()->with('message', 'Email queue not enabled yet.');
    }

    /** Export CSV (full dataset except password) */
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
                ->orLike('city', $q)
                ->groupEnd();
        }

        $fields = [
            'id',
            'first_name',
            'last_name',
            'email',
            'mobile',
            'address1',
            'address2',
            'city',
            'postcode',
            'status',
            'verified_at',
            'verified_by',
            'consent_at',
            'last_login',
            'source',
            'created_at',
            'updated_at',
            'deleted_at'
        ];

        $rows = $builder->select(implode(',', $fields))
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $filename = 'members_export_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        fputcsv($out, $fields);

        foreach ($rows as $r) {
            $line = [];
            foreach ($fields as $f) {
                $line[] = $r[$f] ?? '';
            }
            fputcsv($out, $line);
        }

        fclose($out);
        exit;
    }

    private function toCsv(array $rows, array $headers): string
    {
        $fh = fopen('php://temp', 'r+');
        fputcsv($fh, $headers);
        foreach ($rows as $r) {
            $line = [];
            foreach ($headers as $h) $line[] = $r[$h] ?? '';
            fputcsv($fh, $line);
        }
        rewind($fh);
        return stream_get_contents($fh);
    }
}
