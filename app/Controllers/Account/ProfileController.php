<?php

namespace App\Controllers\Account;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\MemberAuditLogModel;

class ProfileController extends BaseController
{
    public function edit()
    {
        $memberId = (int) (session()->get('member_id') ?? 0);
        if (!$memberId) {
            return redirect()->to('/membership/login');
        }

        $m = (new MemberModel())->find($memberId);

        return view('account/profile_edit', [
            'm' => $m,
        ]);
    }

    public function update()
    {
        $memberId = (int) (session()->get('member_id') ?? 0);
        if (!$memberId) {
            return redirect()->to('/membership/login');
        }

        $memberModel = new MemberModel();
        $auditModel  = new MemberAuditLogModel();

        $existing = $memberModel->find($memberId);
        if (!$existing) {
            return redirect()->back()->with('error', 'Member not found.');
        }

        // Validation rules
        $rules = [
            'mobile'        => "permit_empty|regex_match[/^\+?\d{7,15}$/]",
            'postcode'      => 'permit_empty|max_length[12]',
            'city'          => 'permit_empty|max_length[100]',
            'address1'      => 'permit_empty|max_length[255]',
            'address2'      => 'permit_empty|max_length[255]',
            'consent'       => 'permit_empty|in_list[1]',
            'date_of_birth' => 'permit_empty|valid_date[Y-m-d]',
            'gender'        => 'permit_empty|in_list[male,female,other,prefer_not_to_say]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $now = date('Y-m-d H:i:s');

        $payload = [
            'id'            => $memberId,
            'mobile'        => $this->request->getPost('mobile') ?: null,
            'postcode'      => $this->request->getPost('postcode') ?: null,
            'address1'      => $this->request->getPost('address1') ?: null,
            'address2'      => $this->request->getPost('address2') ?: null,
            'city'          => $this->request->getPost('city') ?: null,
            'date_of_birth' => $this->request->getPost('date_of_birth') ?: null,
            'gender'        => $this->request->getPost('gender') ?: null,
            'consent_at'    => $this->request->getPost('consent') ? $now : null,
            'updated_at'    => $now,
        ];

        // -------------------------------------------------
        // AUDIT — only log actual changes
        // -------------------------------------------------
        foreach ($payload as $field => $newValue) {

            if (!array_key_exists($field, $existing)) {
                continue;
            }

            $oldValue = $existing[$field];

            // Normalise for comparison
            if ((string) $oldValue === (string) $newValue) {
                continue;
            }

            $auditModel->insert([
                'member_id'   => $memberId,
                'type'        => 'profile',
                'field_name'  => $field,
                'old_value'   => $oldValue !== null ? (string) $oldValue : null,
                'new_value'   => $newValue !== null ? (string) $newValue : null,
                'description' => "Member updated {$field}",
                'changed_by'  => $memberId, // self-service change
                'changed_at'  => $now,
            ]);
        }

        // Save member
        $memberModel->save($payload);

        return redirect()
            ->to(route_to('account.dashboard'))
            ->with('message', 'Profile updated.');
    }
}
