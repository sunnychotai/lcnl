<?php

namespace App\Controllers\Account;

use App\Controllers\BaseController;
use App\Models\MemberModel;

class ProfileController extends BaseController
{
    public function edit()
    {
        $memberId = (int) (session()->get('member_id') ?? 0);
        if (! $memberId) return redirect()->to('/member/login');

        $m = (new MemberModel())->find($memberId);

        return view('account/profile_edit', ['m' => $m]);
    }

    public function update()
    {
        $memberId = (int) (session()->get('member_id') ?? 0);
        if (! $memberId) return redirect()->to('/member/login');

        $rules = [
            'mobile'   => 'permit_empty|regex_match[/^\+?\d{7,15}$/]|is_unique[members.mobile,id,{id}]',
            'postcode' => 'permit_empty|max_length[12]',
            'consent'  => 'permit_empty|in_list[1]',
        ];

        $data = $this->request->getPost();
        $data['id'] = $memberId; // for is_unique[id,{id}] to ignore self

        if (! $this->validate($rules, [], $data)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $payload = [
            'id'        => $memberId,
            'mobile'    => $this->request->getPost('mobile') ?: null,
            'postcode'  => $this->request->getPost('postcode') ?: null,
            'consent_at'=> $this->request->getPost('consent') ? date('Y-m-d H:i:s') : null,
        ];

        (new MemberModel())->save($payload);

        return redirect()->to(route_to('account.dashboard'))->with('message', 'Profile updated.');
    }
}
