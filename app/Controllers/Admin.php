<?php

namespace App\Controllers;

use App\Models\MemberModel;
use App\Models\EmailQueueModel;

class Admin extends BaseController
{
    public function dashboard()
    {
        if (! session()->get('isAdminLoggedIn') || session()->get('admin_role') !== 'ADMIN') {
            return redirect()->to('/auth/login');
        }

        // Models
        $memberModel = new MemberModel();
        $emailModel  = new EmailQueueModel();

        // Stats
        $stats = [
            'active_members'  => $memberModel->where('status', 'active')->countAllResults(),
            'pending_members' => $memberModel->where('status', 'pending')->countAllResults(),
            'emails_sent'     => $emailModel->where('status', 'sent')->countAllResults(),
            'last_login'      => session()->get('last_login') ?? '-'  // fallback if not stored
        ];

        return view('admin/system/dashboard', [
            'stats' => $stats
        ]);
    }
}