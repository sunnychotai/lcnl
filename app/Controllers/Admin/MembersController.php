<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\MemberFamilyModel;
use App\Services\MemberService;
use App\Models\MemberAuditLogModel;

class MembersController extends BaseController
{
    private MemberModel $members;
    private MemberService $svc;

    public function __construct()
    {
        $this->members = new MemberModel();
        $this->svc = new MemberService();
    }

    /** List members with filters, search, AJAX */
    public function index()
    {
        $status = $this->request->getGet('status') ?: 'all'; // pending|active|disabled|all
        $q = trim((string) $this->request->getGet('q'));

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
            'pending' => (int) (new MemberModel())->where('status', 'pending')->countAllResults(),
            'active' => (int) (new MemberModel())->where('status', 'active')->countAllResults(),
            'disabled' => (int) (new MemberModel())->where('status', 'disabled')->countAllResults(),
            'all' => (int) (new MemberModel())->countAllResults(),
        ];

        // AJAX mode (for dynamic search/filter)
        if ($this->request->isAJAX() || $this->request->getGet('ajax')) {
            $rowsHtml = view('admin/membership/_rows', [
                'rows' => $rows,
                'status' => $status,
                'q' => $q,
                'pager' => $pager
            ]);
            $pagerHtml = $pager->links('default', 'default_full');
            return $this->response->setJSON([
                'rowsHtml' => $rowsHtml,
                'pagerHtml' => $pagerHtml,
            ]);
        }

        return view('admin/membership/index', compact('rows', 'status', 'q', 'counts', 'pager'));
    }

    /** Show one member */
    public function show($id)
    {
        $memberModel = new MemberModel();
        $familyModel = new MemberFamilyModel();

        $m = $memberModel->find($id);
        if (!$m) {
            return redirect()->back()->with('error', 'Member not found.');
        }

        // 👇 ADD THIS – LOAD FAMILY MEMBERS
        $family = $familyModel
            ->where('member_id', $id)
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('admin/membership/show', [
            'm' => $m,
            'family' => $family, // 👈 MUST BE PASSED
        ]);
    }

    public function toggleEmailValidity(int $id)
    {
        // ✅ Only require POST (AJAX check is unreliable)
        if ($this->request->getMethod() !== 'POST') {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        $memberModel = new MemberModel();


        $member = $memberModel->find($id);
        if (!$member) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Member not found']);
        }

        $old = (int) ($member['is_valid_email'] ?? 0);
        $new = $old === 1 ? 0 : 1;

        $reason = trim((string) $this->request->getPost('reason'));
        $reason = $reason !== '' ? mb_substr($reason, 0, 255) : null;

        $memberModel->update($id, [
            'is_valid_email' => $new,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $oldLabel = $old ? 'VALID' : 'INVALID';
        $newLabel = $new ? 'VALID' : 'INVALID';

        $description = "Email validity changed from {$oldLabel} to {$newLabel}";
        if ($reason) {
            $description .= " ({$reason})";
        }

        $adminId = session()->get('user_id') ?: 0;

        $this->auditMemberChange([
            'member_id' => $id,
            'type' => 'email',
            'field_name' => 'is_valid_email',
            'old_value' => $old,
            'new_value' => $new,
            'description' => $description,
        ]);


        return $this->response->setJSON([
            'success' => true,
            'is_valid_email' => $new,
            'label' => $new ? 'Verified' : 'Invalid',
            'csrf' => [
                'tokenName' => csrf_token(),
                'tokenHash' => csrf_hash(),
            ],
        ]);
    }

    public function queueActivationEmail(int $id)
    {
        $memberModel = new \App\Models\MemberModel();
        $emailQueue = new \App\Models\EmailQueueModel();

        $member = $memberModel->find($id);

        if (!$member) {
            return redirect()
                ->back()
                ->with('error', 'Member not found.');
        }

        if ($member['status'] !== 'pending') {
            return redirect()
                ->back()
                ->with('error', 'Only pending members can be re-sent an activation email.');
        }

        if ((int) ($member['is_valid_email'] ?? 0) !== 1) {
            return redirect()
                ->back()
                ->with('error', 'This member’s email is marked as invalid.');
        }

        // Prevent duplicate pending activation emails
        $existing = $emailQueue
            ->where('type', 'member_activation')
            ->where('related_id', $member['id'])
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return redirect()
                ->back()
                ->with('message', 'Activation email is already queued.');
        }

        $htmlTemplate = file_get_contents(APPPATH . 'Views/emails/membership_activation.php');
        $textTemplate = file_exists(APPPATH . 'Views/emails/membership_activation.txt')
            ? file_get_contents(APPPATH . 'Views/emails/membership_activation.txt')
            : null;

        $emailQueue->insert([
            'type' => 'member_activation',
            'related_id' => (int) $member['id'],
            'to_email' => $member['email'],
            'to_name' => trim($member['first_name'] . ' ' . $member['last_name']),
            'subject' => 'Activate your LCNL membership',
            'body_html' => $htmlTemplate,
            'body_text' => $textTemplate,
            'priority' => 5,
            'status' => 'pending',
            'scheduled_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()
            ->back()
            ->with('message', 'Activation email re-queued successfully.');
    }

    /** Show manual add member form */
    public function create()
    {
        return view('admin/membership/create', [
            'defaults' => [
                'status' => 'pending',
                'membership_type' => 'Standard',
                'is_valid_email' => 1,
            ],
        ]);
    }

    /** Store manually added member */
    public function store()
    {
        $payload = $this->request->getPost();

        /* ---------------------------------------------------------
         * Validation
         * --------------------------------------------------------- */
        $rules = [
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'email' => 'required|valid_email|is_unique[members.email]',
            'mobile' => 'permit_empty|regex_match[/^\+?\d{7,15}$/]',
            'status' => 'required|in_list[pending,active,disabled]',
            'membership_type' => 'required|in_list[standard,life]',
            'address1' => 'required|max_length[255]',
            'city' => 'required|max_length[100]',
            'postcode' => 'required|max_length[12]',
            'date_of_birth' => 'permit_empty|valid_date[Y-m-d]',
            'gender' => 'permit_empty|in_list[male,female,other,prefer_not_to_say]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode(' ', $this->validator->getErrors()));
        }

        /* ---------------------------------------------------------
         * Core flags
         * --------------------------------------------------------- */
        $adminId = (int) (session('user_id') ?? 0);
        $isActive = $payload['status'] === 'active';
        $isValidEmail = (int) ($payload['is_valid_email'] ?? 1);

        /* ---------------------------------------------------------
         * Create member
         * --------------------------------------------------------- */
        $memberData = [
            'first_name' => trim($payload['first_name']),
            'last_name' => trim($payload['last_name']),
            'email' => strtolower(trim($payload['email'])),
            'mobile' => trim($payload['mobile'] ?? ''),
            'date_of_birth' => $payload['date_of_birth'] ?? null,
            'gender' => $payload['gender'] ?? null,
            'address1' => trim($payload['address1']),
            'address2' => trim($payload['address2'] ?? ''),
            'city' => trim($payload['city']),
            'postcode' => trim($payload['postcode']),
            'status' => $payload['status'],
            'is_valid_email' => $isValidEmail,
            'password_hash' => password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT),
            'verified_at' => $isActive ? date('Y-m-d H:i:s') : null,
            'verified_by' => $isActive ? $adminId : null,
            'source' => 'admin_manual',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $memberId = $this->members->insert($memberData);

        /* ---------------------------------------------------------
         * Membership record (default = Standard)
         * --------------------------------------------------------- */
        $membershipType = $payload['membership_type'] === 'life'
            ? 'life'
            : 'standard';

        $membershipModel = new \App\Models\MembershipModel();
        $membershipModel->insert([
            'member_id' => $memberId,
            'membership_type' => $membershipType,
            'status' => 'active',
            'start_date' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        /* ---------------------------------------------------------
         * Audit log
         * --------------------------------------------------------- */
        $this->auditMemberChange([
            'member_id' => $memberId,
            'type' => 'admin',
            'field_name' => 'member',
            'old_value' => null,
            'new_value' => 'created',
            'description' => $isActive
                ? 'Member manually created and auto-verified by admin'
                : 'Member manually created by admin',
        ]);

        /* ---------------------------------------------------------
         * Activation email (ONLY if safe)
         * --------------------------------------------------------- */
        if (
            $payload['status'] === 'pending'
            && $isValidEmail === 1
            && !empty($payload['send_activation'])
        ) {
            $this->queueActivationEmail($memberId);
        }

        /* ---------------------------------------------------------
         * Done
         * --------------------------------------------------------- */
        return redirect()
            ->to(base_url("admin/membership/{$memberId}"))
            ->with('message', 'Member added successfully.');
    }




    public function edit($id)
    {
        $id = (int) $id;
        $m = $this->members->find($id);

        if (!$m) {
            return redirect()->to('/admin/membership')->with('error', 'Member not found.');
        }

        // Load family members
        $family = (new \App\Models\MemberFamilyModel())
            ->where('member_id', $id)
            ->orderBy('id', 'ASC')
            ->findAll();

        return view('admin/membership/edit', [
            'm' => $m,
            'family' => $family
        ]);
    }

    public function updateType($memberId)
    {
        $type = $this->request->getPost('membership_type');

        $membershipModel = new \App\Models\MembershipModel();

        // Update the latest record OR create if missing
        $row = $membershipModel->where('member_id', $memberId)
            ->orderBy('id', 'DESC')
            ->first();

        if ($row) {
            $membershipModel->update($row['id'], [
                'membership_type' => $type,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $membershipModel->insert([
                'member_id' => $memberId,
                'membership_type' => $type,
                'status' => 'active',
                'start_date' => date('Y-m-d'),
            ]);
        }

        return redirect()->back()->with('message', 'Membership type updated successfully.');
    }


    /** Update submission */
    public function update($id)
    {
        $id = (int) $id;
        $payload = $this->request->getPost();

        $rules = [
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'email' => "required|valid_email|is_unique[members.email,id,{$id}]",
            'mobile' => "permit_empty|regex_match[/^\+?\d{7,15}$/]",
            'status' => 'required|in_list[pending,active,disabled]',
            'postcode' => 'permit_empty|max_length[12]',
            'city' => 'permit_empty|max_length[100]',
            'address1' => 'permit_empty|max_length[255]',
            'address2' => 'permit_empty|max_length[255]',
            'date_of_birth' => 'permit_empty|valid_date[Y-m-d]',
            'gender' => 'permit_empty|in_list[male,female,other,prefer_not_to_say]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode(' ', $this->validator->getErrors()));
        }

        unset($payload['password_hash']);
        $payload['updated_at'] = date('Y-m-d H:i:s');

        $before = $this->members->find($id);

        foreach ($payload as $field => $newValue) {
            if (!array_key_exists($field, $before)) {
                continue;
            }

            $oldValue = (string) ($before[$field] ?? '');

            if ((string) $newValue !== $oldValue) {
                $this->auditMemberChange([
                    'member_id' => $id,
                    'type' => 'profile',
                    'field_name' => $field,
                    'old_value' => $oldValue,
                    'new_value' => (string) $newValue,
                    'description' => ucfirst(str_replace('_', ' ', $field)) . ' updated via admin edit',
                ]);
            }
        }


        $this->members->update($id, $payload);
        return redirect()->to(base_url("admin/membership/{$id}"))
            ->with('message', 'Member updated successfully.');
    }

    public function updateMembershipType($memberId)
    {
        $type = $this->request->getPost('membership_type');
        $adminId = session()->get('admin_id'); // your admin session key

        $membershipModel = new \App\Models\MembershipModel();
        $historyModel = new \App\Models\MembershipHistoryModel();

        $existing = $membershipModel
            ->where('member_id', $memberId)
            ->orderBy('id', 'DESC')
            ->first();

        $oldType = $existing['membership_type'] ?? null;

        // Only record history if the type actually changes
        if ($oldType !== $type) {
            $historyModel->insert([
                'member_id' => $memberId,
                'changed_by' => $adminId,
                'old_type' => $oldType,
                'new_type' => $type,
                'notes' => 'Membership type updated via admin dashboard',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // Update membership
        if ($existing) {
            $membershipModel->update($existing['id'], [
                'membership_type' => $type,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $membershipModel->insert([
                'member_id' => $memberId,
                'membership_type' => $type,
                'status' => 'active',
                'start_date' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->back()->with('message', 'Membership type updated successfully.');
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
    public function sendActivationNow($id)
    {
        $memberModel = new MemberModel();
        $member = $memberModel->find($id);

        if (!$member) {
            return redirect()->back()->with('error', 'Member not found.');
        }

        if ($member['status'] !== 'pending') {
            return redirect()->back()->with('error', 'This member is already active.');
        }

        // Create new token
        $token = bin2hex(random_bytes(32));

        // Store token for verification flow
        $verificationModel = new \App\Models\MemberVerificationModel();
        $verificationModel->insert([
            'member_id' => $id,
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+24 hours')),
        ]);

        // Email link
        $link = base_url('membership/verify/' . $token);

        // Prepare email content
        $fullName = trim($member['first_name'] . ' ' . $member['last_name']);

        (new \App\Models\EmailQueueModel())->enqueue([
            'to_email' => $member['email'],
            'to_name' => $fullName,
            'subject' => 'Verify Your LCNL Account',
            'body_html' => view('emails/account_activation', [
                'name' => $fullName,
                'link' => $link,
            ]),
            'body_text' => "Please verify your account using this link: $link",
            'priority' => 2,
        ]);

        return redirect()->back()->with('message', 'Verification email resent successfully.');
    }


    /** Export CSV (full dataset except password) */
    public function export()
    {
        $status = $this->request->getGet('status') ?: 'all';
        $q = trim((string) $this->request->getGet('q'));

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
            foreach ($headers as $h)
                $line[] = $r[$h] ?? '';
            fputcsv($fh, $line);
        }
        rewind($fh);
        return stream_get_contents($fh);
    }
}
