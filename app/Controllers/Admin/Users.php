<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data['users'] = $this->userModel->findAll();
        return view('admin/system/users/index', $data);
    }

    public function create()
    {
        return view('admin/system/users/form', [
            'action' => base_url('admin/system/users/store')
        ]);
    }

    public function store()
    {
        $rules = [
            'name'     => 'required',
            'email'    => 'required|valid_email|is_unique[lcnl_users.email]',
            'role'     => 'required|in_list[' . $this->roleList() . ']',
            'password' => 'required|min_length[6]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->userModel->save($this->request->getPost(['name', 'email', 'role', 'password']));

        return redirect()->to('admin/system/users')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('User not found');
        }

        return view('admin/system/users/form', [
            'user'   => $user,
            'action' => base_url('admin/system/users/update/'.$id)
        ]);
    }

    public function update($id)
    {
        $rules = [
            'name'  => 'required',
            'email' => 'required|valid_email|is_unique[lcnl_users.email,id,'.$id.']',
            'role'  => 'required|in_list[' . $this->roleList() . ']'
        ];

        // Your own role can't change here (the form disables it), so you
        // can't lock yourself out of the admin.
        $isSelf = (int) $id === (int) session('admin_id');
        if ($isSelf) {
            unset($rules['role']);
        }

        // If password provided, validate it
        $password = $this->request->getPost('password');
        if (! empty($password)) {
            $rules['password'] = 'min_length[6]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost(['name', 'email', 'role', 'password']);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        if ($isSelf) {
            unset($data['role']);
        }

        $this->userModel->update($id, $data);

        return redirect()->to('admin/system/users')->with('success', 'User updated successfully.');
    }

    public function delete($id)
    {
        if ((int) $id === (int) session('admin_id')) {
            return redirect()->to('admin/system/users')->with('error', 'You can\'t delete your own account.');
        }

        $this->userModel->delete($id);
        return redirect()->to('admin/system/users')->with('success', 'User deleted successfully.');
    }

    /** Roles from Config\AdminAccess, for an in_list rule. */
    private function roleList(): string
    {
        return implode(',', array_keys(config('AdminAccess')->roles));
    }
}
