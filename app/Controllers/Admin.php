<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function dashboard()
    {
        // protect route
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        return view('admin/dashboard');
    }
}
