<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MemberModel;

class MemberAuth extends BaseController
{
    public function login()
    {
        // Render the login view only — no $member usage here.
        return view('member/login');
    }

    public function attempt()
    {
        // Optional throttling
        $throttler = service('throttler');
        if ($throttler && ! $throttler->check('member-login-'.($this->request->getIPAddress()), 10, 60)) {
            return redirect()->back()->withInput()->with('error', 'Too many attempts. Please try again shortly.');
        }

        // Basic validation
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[8]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please check your details and try again.');
        }

        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');

        // Fetch the member
        $members = new MemberModel();
        $member  = $members->where('email', $email)->first();

        // Guard clauses: if not found or bad password, bail early
        if (! $member || ! password_verify($password, $member['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid email or password.');
        }

        // Status must be active
        if (($member['status'] ?? 'pending') !== 'active') {
            return redirect()->back()->with('error', 'Your membership is not active yet.');
        }

        // Establish member session (separate from admin)
        session()->regenerate(true);
        session()->set([
            'member_id'    => (int) $member['id'],
            'member_email' => $member['email'],
            'member_name'  => trim(($member['first_name'] ?? '').' '.($member['last_name'] ?? '')),
            'is_member'    => true,
        ]);

        // Update last_login safely via Query Builder (avoids timestamp quirks)
        $db = db_connect();
        $db->table('members')
           ->where('id', (int) $member['id'])
           ->update(['last_login' => date('Y-m-d H:i:s')]);

return redirect()->to(url_to('account.dashboard'))->with('message', 'Welcome back!');
    }

    public function logout()
    {
        session()->remove(['member_id','member_email','member_name','is_member']);
        session()->regenerate(true);
        return redirect()->to('/member/login')->with('message', 'You are logged out.');
    }
}
