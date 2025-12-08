<?php

namespace App\Controllers\Account;

use App\Controllers\BaseController;
use App\Models\MemberFamilyModel;
use Config\Family as FamilyConfig;

class FamilyController extends BaseController
{
    /** Loaded from Config\Family */
    private array $relations;
    private array $genders;

    public function __construct()
    {
        $config = new FamilyConfig();

        // Example structure:
        // 'son' => ['label' => 'Son', 'icon' => 'bi-gender-male']
        $this->relations = $config->relations;

        // ['male', 'female', 'other', 'prefer_not_to_say']
        $this->genders = $config->genders;
    }

    /** Ensure logged in */
    private function memberIdOrRedirect(): ?int
    {
        $memberId = (int) (session()->get('member_id') ?? 0);

        if (!$memberId) {
            return redirect()->to('/membership/login');
        }

        return $memberId;
    }

    /** Index page */
    public function index()
    {
        $memberId = $this->memberIdOrRedirect();

        $family = (new MemberFamilyModel())
            ->where('member_id', $memberId)
            ->orderBy('name', 'ASC')
            ->findAll();

        $years = range((int) date('Y'), 1900);

        return view('account/family_index', [
            'family' => $family,
            'relations' => $this->relations,   // now includes label + icon
            'genders' => $this->genders,
            'years' => $years,
        ]);
    }

    /** Create */
    public function create()
    {
        $memberId = $this->memberIdOrRedirect();

        // Allowed relation keys → spouse, son, daughter, etc.
        $relationKeys = implode(',', array_keys($this->relations));
        $genderKeys = implode(',', $this->genders);

        $rules = [
            'name' => 'required|min_length[2]|max_length[120]',
            'email' => 'permit_empty|valid_email|max_length[120]',
            'relation' => "required|in_list[$relationKeys]",
            'year_of_birth' => 'permit_empty|integer|greater_than_equal_to[1900]|less_than_equal_to[' . date('Y') . ']',
            'gender' => "permit_empty|in_list[$genderKeys]",
            'notes' => 'permit_empty|max_length[255]',
            'telephone' => 'permit_empty|min_length[5]|max_length[30]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $payload = [
            'member_id' => $memberId,
            'name' => trim($this->request->getPost('name')),
            'email' => trim($this->request->getPost('email')) ?: null,
            'relation' => strtolower($this->request->getPost('relation')),
            'year_of_birth' => $this->request->getPost('year_of_birth') ?: null,
            'gender' => $this->request->getPost('gender') ?: null,
            'notes' => $this->request->getPost('notes') ?: null,
            'telephone' => $this->request->getPost('telephone') ?: null,
        ];

        $model = new MemberFamilyModel();
        $id = $model->insert($payload, true);

        if (!$id) {
            return redirect()->back()
                ->with('errors', $model->errors())
                ->withInput();
        }

        return redirect()->route('account.family')
            ->with('message', 'Family member added.');
    }

    /** Update */
    public function update(int $id)
    {
        $memberId = $this->memberIdOrRedirect();
        $model = new MemberFamilyModel();

        $row = $model->find($id);

        if (!$row || (int) $row['member_id'] !== $memberId) {
            return redirect()->route('account.family')
                ->with('error', 'Not found.');
        }

        $relationKeys = implode(',', array_keys($this->relations));
        $genderKeys = implode(',', $this->genders);

        $rules = [
            'name' => 'required|min_length[2]|max_length[120]',
            'email' => 'permit_empty|valid_email|max_length[120]',
            'relation' => "required|in_list[$relationKeys]",
            'year_of_birth' => 'permit_empty|integer|greater_than_equal_to[1900]|less_than_equal_to[' . date('Y') . ']',
            'gender' => "permit_empty|in_list[$genderKeys]",
            'notes' => 'permit_empty|max_length[255]',
            'telephone' => 'permit_empty|min_length[5]|max_length[30]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $payload = [
            'id' => $id,
            'name' => trim($this->request->getPost('name')),
            'email' => trim($this->request->getPost('email')) ?: null,
            'relation' => strtolower($this->request->getPost('relation')),
            'year_of_birth' => $this->request->getPost('year_of_birth') ?: null,
            'gender' => $this->request->getPost('gender') ?: null,
            'notes' => $this->request->getPost('notes') ?: null,
            'telephone' => $this->request->getPost('telephone') ?: null,
        ];

        $model->save($payload);

        return redirect()->route('account.family')
            ->with('message', 'Family member updated.');
    }

    /** Delete */
    public function delete(int $id)
    {
        $memberId = $this->memberIdOrRedirect();
        $model = new MemberFamilyModel();

        $row = $model->find($id);

        if (!$row || (int) $row['member_id'] !== $memberId) {
            return redirect()->route('account.family')
                ->with('error', 'Not found.');
        }

        $model->delete($id);

        return redirect()->route('account.family')
            ->with('message', 'Family member removed.');
    }
}
