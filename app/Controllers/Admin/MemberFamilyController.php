<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberFamilyModel;
use Config\Family as FamilyConfig;

class MemberFamilyController extends BaseController
{
    private array $relations; // ['son' => ['label'=>'Son','icon'=>'bi-gender-male'], ...]
    private array $relationKeys;
    private array $genders;

    public function __construct()
    {
        $config = new FamilyConfig();

        $this->relations = $config->relations;
        $this->relationKeys = array_keys($config->relations); // ['son','daughter','spouse',...]
        $this->genders = $config->genders;
    }


    private function input(): array
    {
        $json = $this->request->getJSON(true) ?: [];
        $post = $this->request->getPost();
        return array_merge($json, $post);
    }

    private function ok(array $extra = [])
    {
        return $this->response->setJSON(array_merge([
            'success' => true,
            'csrf' => [
                'tokenName' => csrf_token(),
                'tokenHash' => csrf_hash(),
            ]
        ], $extra));
    }

    private function fail(string $message, array $extra = [])
    {
        return $this->response->setJSON(array_merge([
            'success' => false,
            'message' => $message,
            'csrf' => [
                'tokenName' => csrf_token(),
                'tokenHash' => csrf_hash(),
            ]
        ], $extra));
    }


    /**
     * ADD FAMILY MEMBER
     */
    public function add()
    {
        $model = new MemberFamilyModel();
        $data = $this->input();

        // Validate relation is from config
        $relation = strtolower(trim($data['relation'] ?? ''));
        if (!in_array($relation, $this->relationKeys, true)) {
            return $this->fail("Invalid relation value.");
        }

        $row = [
            'member_id' => (int) ($data['member_id'] ?? 0),
            'name' => trim($data['name'] ?? ''),
            'email' => trim($data['email'] ?? '') ?: null,
            'relation' => $relation,
            'year_of_birth' => $data['year_of_birth'] !== '' ? (int) $data['year_of_birth'] : null,
            'gender' => trim($data['gender'] ?? '') ?: null,
            'notes' => trim($data['notes'] ?? '') ?: null,
        ];

        $id = $model->insert($row, true);

        if (!$id) {
            return $this->fail('Unable to add family member', [
                'errors' => $model->errors()
            ]);
        }

        $row['id'] = $id;

        // Enrich response with label + icon
        $relationInfo = $this->relations[$relation] ?? ['label' => ucfirst($relation), 'icon' => 'bi-person'];

        return $this->ok([
            'id' => $id,
            'row' => $row,
            'relation_label' => $relationInfo['label'],
            'relation_icon' => $relationInfo['icon'],
        ]);
    }


    /**
     * UPDATE FAMILY MEMBER
     */
    public function update()
    {
        $dataIn = $this->input();
        $id = (int) ($dataIn['id'] ?? 0);

        if ($id <= 0) {
            return $this->fail('Invalid ID');
        }

        $relation = strtolower(trim($dataIn['relation'] ?? ''));
        if (!in_array($relation, $this->relationKeys, true)) {
            return $this->fail("Invalid relation value.");
        }

        $data = [
            'name' => trim($dataIn['name'] ?? ''),
            'email' => trim($dataIn['email'] ?? '') ?: null,
            'relation' => $relation,
            'year_of_birth' => $dataIn['year_of_birth'] !== '' ? (int) $dataIn['year_of_birth'] : null,
            'gender' => trim($dataIn['gender'] ?? '') ?: null,
            'notes' => trim($dataIn['notes'] ?? '') ?: null,
        ];

        $model = new MemberFamilyModel();

        if (!$model->update($id, $data)) {
            return $this->fail('Unable to update family member', [
                'errors' => $model->errors()
            ]);
        }

        // Enrich response
        $relationInfo = $this->relations[$relation] ?? ['label' => ucfirst($relation), 'icon' => 'bi-person'];

        return $this->ok([
            'relation_label' => $relationInfo['label'],
            'relation_icon' => $relationInfo['icon'],
        ]);
    }


    /**
     * DELETE FAMILY MEMBER
     */
    public function delete()
    {
        $dataIn = $this->input();
        $id = (int) ($dataIn['id'] ?? 0);

        if ($id <= 0) {
            return $this->fail('Invalid ID');
        }

        $model = new MemberFamilyModel();

        if (!$model->delete($id)) {
            return $this->fail('Unable to delete family member');
        }

        return $this->ok();
    }
}
