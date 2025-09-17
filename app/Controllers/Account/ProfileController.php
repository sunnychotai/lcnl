<?php

namespace App\Controllers\Account;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\FamilyModel;
use App\Models\FamilyMemberModel;

class ProfileController extends BaseController
{
    public function edit()
    {
        $memberId = (int) (session()->get('member_id') ?? 0);
        if (! $memberId) return redirect()->to('/member/login');

        $m = (new MemberModel())->find($memberId);

        // --- Household summary (MEM-PROF-02) ---
        $familyModel       = new FamilyModel();
        $familyMemberModel = new FamilyMemberModel();

        $family    = $familyModel->where('lead_member_id', $memberId)->first();
        $household = [];

        if ($family) {
            $household = $familyMemberModel
                ->select('family_members.*, m.first_name, m.last_name, m.email, m.mobile, m.status')
                ->join('members m', 'm.id = family_members.member_id')
                ->where('family_members.family_id', $family['id'])
                ->findAll();
        }

        return view('account/profile_edit', [
            'm'         => $m,
            'family'    => $family,
            'household' => $household,
        ]);
    }

    public function update()
    {
        $memberId = (int) (session()->get('member_id') ?? 0);
        if (! $memberId) return redirect()->to('/member/login');

        // 👇 Fix: exclude this member’s own record from unique check
        $rules = [
            'mobile'   => "permit_empty|regex_match[/^\+?\d{7,15}$/]|is_unique[members.mobile,id,{$memberId}]",
            'postcode' => 'permit_empty|max_length[12]',
            'consent'  => 'permit_empty|in_list[1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $payload = [
            'id'         => $memberId,
            'mobile'     => $this->request->getPost('mobile') ?: null,
            'postcode'   => $this->request->getPost('postcode') ?: null,
            'consent_at' => $this->request->getPost('consent') ? date('Y-m-d H:i:s') : null,
        ];

        (new MemberModel())->save($payload);

        return redirect()
            ->to(route_to('account.dashboard'))
            ->with('message', 'Profile updated.');
    }
}
