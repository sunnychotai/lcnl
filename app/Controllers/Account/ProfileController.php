<?php

namespace App\Controllers\Account;

use App\Controllers\BaseController;
use App\Models\MemberModel;

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

        // Validation rules
        $rules = [
            'mobile' => "permit_empty|regex_match[/^\+?\d{7,15}$/]",
            'postcode' => 'permit_empty|max_length[12]',
            'city' => 'permit_empty|max_length[100]',
            'address1' => 'permit_empty|max_length[255]',
            'address2' => 'permit_empty|max_length[255]',
            'consent' => 'permit_empty|in_list[1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $payload = [
            'id' => $memberId,
            'mobile' => $this->request->getPost('mobile') ?: null,
            'postcode' => $this->request->getPost('postcode') ?: null,
            'address1' => $this->request->getPost('address1') ?: null,
            'address2' => $this->request->getPost('address2') ?: null,
            'city' => $this->request->getPost('city') ?: null,
            'consent_at' => $this->request->getPost('consent') ? date('Y-m-d H:i:s') : null,
        ];

        (new MemberModel())->save($payload);

        return redirect()
            ->to(route_to('account.dashboard'))
            ->with('message', 'Profile updated.');
    }
}
