<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberFamilyModel;

class FamilyController extends BaseController
{
    private array $relations = ['Spouse', 'Son', 'Daughter', 'Mother', 'Father', 'Grandparent', 'Sibling', 'Other'];
    private array $genders   = ['male', 'female', 'other', 'prefer_not_to_say'];

    public function create(int $memberId)
    {
        $rules = [
            'name'          => 'required|min_length[2]|max_length[120]',
            'relation'      => 'required|in_list[' . implode(',', $this->relations) . ']',
            'year_of_birth' => 'permit_empty|integer|greater_than_equal_to[1900]|less_than_equal_to[' . date('Y') . ']',
            'gender'        => 'permit_empty|in_list[' . implode(',', $this->genders) . ']',
            'notes'         => 'permit_empty|max_length[255]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Validation failed.')->withInput();
        }

        (new MemberFamilyModel())->insert([
            'member_id'     => $memberId,
            'name'          => trim((string)$this->request->getPost('name')),
            'relation'      => $this->request->getPost('relation'),
            'year_of_birth' => $this->request->getPost('year_of_birth') ?: null,
            'gender'        => $this->request->getPost('gender') ?: null,
            'notes'         => $this->request->getPost('notes') ?: null,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
        return redirect()->to(base_url("admin/membership/{$memberId}"))->with('message', 'Family member added.');
    }

    public function update(int $memberId, int $id)
    {
        $rules = [
            'name'          => 'required|min_length[2]|max_length[120]',
            'relation'      => 'required|in_list[' . implode(',', $this->relations) . ']',
            'year_of_birth' => 'permit_empty|integer|greater_than_equal_to[1900]|less_than_equal_to[' . date('Y') . ']',
            'gender'        => 'permit_empty|in_list[' . implode(',', $this->genders) . ']',
            'notes'         => 'permit_empty|max_length[255]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Validation failed.')->withInput();
        }

        (new MemberFamilyModel())->save([
            'id'            => $id,
            'name'          => trim((string)$this->request->getPost('name')),
            'relation'      => $this->request->getPost('relation'),
            'year_of_birth' => $this->request->getPost('year_of_birth') ?: null,
            'gender'        => $this->request->getPost('gender') ?: null,
            'notes'         => $this->request->getPost('notes') ?: null,
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
        return redirect()->to(base_url("admin/membership/{$memberId}"))->with('message', 'Family member updated.');
    }

    public function delete(int $memberId, int $id)
    {
        (new MemberFamilyModel())->delete($id);
        return redirect()->to(base_url("admin/membership/{$memberId}"))->with('message', 'Family member removed.');
    }
}
