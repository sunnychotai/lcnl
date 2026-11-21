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

        $email = strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            $role = strtoupper((string) $user['role']);

            // Only roles listed in Config\AdminAccess can sign in.
            if (!array_key_exists($role, config('AdminAccess')->roles)) {
                return redirect()->to('/auth/login')->with('error', 'Role not recognized.');
            }

            // New session id on sign-in, so an id planted before login is useless.
            $session->regenerate(true);
            $session->set([
                'isAdminLoggedIn' => true,
                'admin_id' => (int) $user['id'],
                'admin_name' => $user['name'],
                'admin_role' => $role,
            ]);

            return redirect()->to('/admin/system/dashboard'); // every role has the dashboard
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
