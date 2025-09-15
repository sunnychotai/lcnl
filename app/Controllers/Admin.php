<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function dashboard()
    {
        if (! session()->get('isAdminLoggedIn') || session()->get('admin_role') !== 'ADMIN') {
            return redirect()->to('/auth/login');
        }

        return view('admin/dashboard');
    }
}
