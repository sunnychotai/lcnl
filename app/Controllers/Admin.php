<?php

namespace App\Controllers;

use App\Models\MemberModel;
use App\Models\EmailQueueModel;

class Admin extends BaseController
{
    public function dashboard()
    {
        // Allow any logged-in admin (ADMIN, FINANCE, MEMBERSHIP, EVENTS)
        if (! session()->get('isAdminLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $role = session()->get('admin_role') ?? 'UNKNOWN';

        // Models
        $memberModel = new \App\Models\MemberModel();
        $emailModel  = new \App\Models\EmailQueueModel();

        // Stats (you can make these role-specific later)
        $stats = [
            'active_members'  => $memberModel->where('status', 'active')->countAllResults(),
            'pending_members' => $memberModel->where('status', 'pending')->countAllResults(),
            'emails_sent'     => $emailModel->where('status', 'sent')->countAllResults(),
            'last_login'      => session()->get('last_login') ?? '-',
            'role'            => $role
        ];

        return view('admin/system/dashboard', [
            'stats' => $stats,
            'role'  => $role
        ]);
    }
}
