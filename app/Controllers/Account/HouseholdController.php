<?php

namespace App\Controllers\Account;

use App\Controllers\BaseController;
use App\Services\FamilyService;

class HouseholdController extends BaseController
{
    private FamilyService $svc;

    public function __construct()
    {
        $this->svc = new FamilyService();
    }

    public function index()
    {
        // ✅ Use member_id (not user_id). The authMember filter already gates access,
        // but keep a safe fallback to the member login:
        $memberId = (int) (session()->get('member_id') ?? 0);
        if (!$memberId) {
            return redirect()->to('/membership/login');
        }

        $house = $this->svc->householdForMember($memberId);

        return view('account/household', [
            'house' => $house,
            'memberId' => $memberId,
        ]);
    }

    public function create()
    {
        $memberId = (int) (session()->get('member_id') ?? 0);
        if (!$memberId) {
            return redirect()->to('/membership/login');
        }

        $payload = [
            'household_name' => $this->request->getPost('household_name') ?: null,
            'address1' => $this->request->getPost('address1') ?: null,
            'address2' => $this->request->getPost('address2') ?: null,
            'city' => $this->request->getPost('city') ?: null,
            'postcode' => $this->request->getPost('postcode') ?: null,
        ];

        $this->svc->ensureLeadHousehold($memberId, $payload);

        return redirect()->to(route_to('account.household'))
            ->with('message', 'Household created.');
    }

    public function addDependent()
    {
        $memberId = (int) (session()->get('member_id') ?? 0);
        if (!$memberId) {
            return redirect()->to('/membership/login');
        }

        $house = $this->svc->householdForMember($memberId);
        if (!$house) {
            return redirect()->back()->with('error', 'Please create a household first.');
        }

        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name' => 'permit_empty|max_length[100]',
            'email' => 'permit_empty|valid_email|is_unique[members.email]',
            'mobile' => 'permit_empty|regex_match[/^\+?\d{7,15}$/]|is_unique[members.mobile]',
            'postcode' => 'permit_empty|max_length[12]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->svc->addDependent((int) $house['id'], $this->request->getPost());

        return redirect()->to(route_to('account.household'))->with('message', 'Dependent added.');
    }

    public function linkExisting()
    {
        $memberId = (int) (session()->get('member_id') ?? 0);
        if (!$memberId) {
            return redirect()->to('/membership/login');
        }

        $house = $this->svc->householdForMember($memberId);
        if (!$house) {
            return redirect()->back()->with('error', 'Please create a household first.');
        }

        $email = (string) $this->request->getPost('email');
        $role = (string) $this->request->getPost('role');

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Please enter a valid email.');
        }

        $ok = $this->svc->linkExistingByEmail((int) $house['id'], $email, $role ?: 'spouse');

        return redirect()->to(route_to('account.household'))->with(
            $ok ? 'message' : 'error',
            $ok ? 'Member linked.' : 'No LCNL account found with that email.'
        );
    }
}
