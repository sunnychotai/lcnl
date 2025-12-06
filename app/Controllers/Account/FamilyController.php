<?php

namespace App\Controllers\Account;

use App\Controllers\BaseController;
use App\Models\MemberFamilyModel;

class FamilyController extends BaseController
{
    private array $relations = ['Spouse', 'Son', 'Daughter', 'Mother', 'Father', 'Grandparent', 'Sibling', 'Other'];
    private array $genders   = ['male', 'female', 'other', 'prefer_not_to_say'];

    private function memberIdOrRedirect(): ?int
    {
        $memberId = (int) (session()->get('member_id') ?? 0);
        if (!$memberId) {
            redirect()->to('/membership/login')->send();
            exit;
        }
        return $memberId;
    }

    public function index()
    {
        $memberId = $this->memberIdOrRedirect();

        $family = (new MemberFamilyModel())
            ->where('member_id', $memberId)
            ->orderBy('name', 'ASC')
            ->findAll();

        $years = range((int)date('Y'), 1900);

        return view('account/family_index', [
            'family'    => $family,
            'relations' => $this->relations,
            'genders'   => $this->genders,
            'years'     => $years,
        ]);
    }

    public function create()
    {
        $memberId = $this->memberIdOrRedirect();

        $rules = [
            'name'          => 'required|min_length[2]|max_length[120]',
            'email'         => 'permit_empty|valid_email|max_length[120]',   // ← NEW
            'relation'      => 'required|in_list[' . implode(',', $this->relations) . ']',
            'year_of_birth' => 'permit_empty|integer|greater_than_equal_to[1900]|less_than_equal_to[' . date('Y') . ']',
            'gender'        => 'permit_empty|in_list[' . implode(',', $this->genders) . ']',
            'notes'         => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $payload = [
            'member_id'     => $memberId,
            'name'          => trim((string)$this->request->getPost('name')),
            'email'         => trim((string)$this->request->getPost('email')),  // ← NEW
            'relation'      => $this->request->getPost('relation'),
            'year_of_birth' => $this->request->getPost('year_of_birth') ?: null,
            'gender'        => $this->request->getPost('gender') ?: null,
            'notes'         => $this->request->getPost('notes') ?: null,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        (new MemberFamilyModel())->insert($payload);

        return redirect()->route('account.family')->with('message', 'Family member added.');
    }

    public function update(int $id)
    {
        $memberId = $this->memberIdOrRedirect();
        $model = new MemberFamilyModel();

        $row = $model->find($id);

        if (!$row || (int)$row['member_id'] !== $memberId) {
            return redirect()->route('account.family')->with('error', 'Not found.');
        }

        $rules = [
            'name'          => 'required|min_length[2]|max_length[120]',
            'email'         => 'permit_empty|valid_email|max_length[120]', // ← NEW
            'relation'      => 'required|in_list[' . implode(',', $this->relations) . ']',
            'year_of_birth' => 'permit_empty|integer|greater_than_equal_to[1900]|less_than_equal_to[' . date('Y') . ']',
            'gender'        => 'permit_empty|in_list[' . implode(',', $this->genders) . ']',
            'notes'         => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors())->withInput();
        }

        $payload = [
            'id'            => $id,
            'name'          => trim((string)$this->request->getPost('name')),
            'email'         => trim((string)$this->request->getPost('email')),  // ← NEW
            'relation'      => $this->request->getPost('relation'),
            'year_of_birth' => $this->request->getPost('year_of_birth') ?: null,
            'gender'        => $this->request->getPost('gender') ?: null,
            'notes'         => $this->request->getPost('notes') ?: null,
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        $model->save($payload);

        return redirect()->route('account.family')->with('message', 'Family member updated.');
    }

    public function delete(int $id)
    {
        $memberId = $this->memberIdOrRedirect();
        $model = new MemberFamilyModel();

        $row = $model->find($id);

        if (!$row || (int)$row['member_id'] !== $memberId) {
            return redirect()->route('account.family')->with('error', 'Not found.');
        }

        $model->delete($id);

        return redirect()->route('account.family')->with('message', 'Family member removed.');
    }
}
