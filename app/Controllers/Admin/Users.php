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
            'role'     => 'required|in_list[ADMIN,WEBSITE,MEMBERSHIP]',
            'password' => 'required|min_length[6]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->userModel->save($this->request->getPost());

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
            'role'  => 'required|in_list[ADMIN,WEBSITE,MEMBERSHIP]'
        ];

        // If password provided, validate it
        $password = $this->request->getPost('password');
        if (! empty($password)) {
            $rules['password'] = 'min_length[6]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $this->userModel->update($id, $data);

        return redirect()->to('admin/system/users')->with('success', 'User updated successfully.');
    }

    public function delete($id)
    {
        $this->userModel->delete($id);
        return redirect()->to('admin/system/users')->with('success', 'User deleted successfully.');
    }
}
