<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CommitteeModel;

class CommitteeController extends BaseController
{
    protected $committeeModel;

    public function __construct()
    {
        $this->committeeModel = new CommitteeModel();
    }

    public function index()
{
    $committeeTypes = ['Executive', 'Mahila', 'YLS', 'RCT', 'LSM', 'LSL', 'LCF', 'YC'];

    $members = [];
    foreach ($committeeTypes as $type) {
        $members[$type] = $this->committeeModel
            ->where('committee', $type)
            ->orderBy('display_order', 'ASC')
            ->findAll();
    }

    $data = [
        'committeeTypes' => $committeeTypes,
        'members'        => $members,
    ];

    return view('admin/committee/index', $data);
}

public function create()
{
    $committeeTypes = ['Executive', 'Mahila', 'YLS', 'RCT', 'LSM', 'LSL', 'LCF', 'YC'];

    $data = [
        'committee'        => [],
        'committees'       => $committeeTypes,
        'selectedCommittee'=> $this->request->getGet('committee'),
        'action'           => base_url('admin/committee/store') // ✅ new
    ];

    return view('admin/committee/form', $data);
}



    public function store()
    {
        $validationRules = [
            'firstname'     => 'required|min_length[2]',
            'surname'       => 'required|min_length[2]',
            'email'         => 'required|valid_email',
            'role'          => 'permit_empty|string',
            'committee'     => 'required|in_list[Executive,Mahila,YLS,RCT,LSM,LSL,LCF,YC]',
            'display_order' => 'required|is_natural',
            'image'         => 'permit_empty|string'
        ];

        if (! $this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->committeeModel->save($this->request->getPost());
        return redirect()->to('/admin/committee')->with('success', 'Committee member added successfully');
    }

    public function edit($id)
{
    $committee = $this->committeeModel->find($id);
    $committeeTypes = ['Executive', 'Mahila', 'YLS', 'RCT', 'LSM', 'LSL', 'LCF', 'YC'];

    if (!$committee) {
        return redirect()->to('/admin/committee')->with('error', 'Committee member not found');
    }

    $data = [
        'committee'        => $committee,
        'committees'       => $committeeTypes,
        'selectedCommittee'=> $committee['committee'],
        'action'           => base_url('admin/committee/update/'.$committee['id']) // ✅ update
    ];

    return view('admin/committee/form', $data);
}


    public function update($id)
    {
        $validationRules = [
            'firstname'     => 'required|min_length[2]',
            'surname'       => 'required|min_length[2]',
            'email'         => 'required|valid_email',
            'role'          => 'permit_empty|string',
            'committee'     => 'required|in_list[Executive,Mahila,YLS,RCT,LSM,LSL,LCF,YC]',
            'display_order' => 'required|is_natural',
            'role'          => 'permit_empty|string'
        ];

        if (! $this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->committeeModel->update($id, $this->request->getPost());
        return redirect()->to('/admin/committee')->with('success', 'Committee member updated successfully');
    }

    public function delete($id)
    {
        $this->committeeModel->delete($id);
        return redirect()->to('/admin/committee')->with('success', 'Committee member deleted');
    }

public function clone($id)
{
    $committee = $this->committeeModel->find($id);
    $committeeTypes = ['Executive', 'Mahila', 'YLS', 'RCT', 'LSM', 'LSL', 'LCF', 'YC'];

    if (!$committee) {
        return redirect()->to('/admin/committee')->with('error', 'Committee member not found');
    }

    // ✅ Strip ID so it becomes a new record
    if (isset($committee['id'])) {
        unset($committee['id']);
    }

    $data = [
        'committee'        => $committee,
        'committees'       => $committeeTypes,
        'selectedCommittee'=> $committee['committee'] ?? null,
        'action'           => base_url('admin/committee/store') // ✅ always new
    ];

    return view('admin/committee/form', $data);
}




}
