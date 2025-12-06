<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberFamilyModel;

class MemberFamilyController extends BaseController
{
    private function input(): array
    {
        // Accept JSON or form POST seamlessly
        $json = $this->request->getJSON(true) ?: [];
        $post = $this->request->getPost();
        return array_merge($json, $post);
    }

    private function ok(array $extra = [])
    {
        $csrf = [
            'tokenName' => csrf_token(),
            'tokenHash' => csrf_hash(),
        ];

        return $this->response->setJSON(
            array_merge(['success' => true], $extra, ['csrf' => $csrf])
        );
    }

    private function fail(string $message, array $extra = [])
    {
        $csrf = [
            'tokenName' => csrf_token(),
            'tokenHash' => csrf_hash(),
        ];

        return $this->response->setJSON(
            array_merge(['success' => false, 'message' => $message], $extra, ['csrf' => $csrf])
        );
    }

    public function add()
    {
        $model = new MemberFamilyModel();
        $data  = $this->input();  // unified input

        $row = [
            'member_id'     => (int) ($data['member_id'] ?? 0),
            'name'          => trim($data['name'] ?? ''),
            'email'         => trim($data['email'] ?? '') ?: null,   // ← NEW
            'relation'      => trim($data['relation'] ?? ''),
            'year_of_birth' => isset($data['year_of_birth']) && $data['year_of_birth'] !== ''
                ? (int) $data['year_of_birth']
                : null,
            'gender'        => ($data['gender'] ?? '') ?: null,
            'notes'         => trim($data['notes'] ?? '') ?: null,
        ];

        $id = $model->insert($row, true);

        if (!$id) {
            return $this->fail('Unable to add family member', [
                'errors' => $model->errors()
            ]);
        }

        $row['id'] = $id;

        return $this->ok([
            'id'  => $id,
            'row' => $row
        ]);
    }

    public function update()
    {
        $dataIn = $this->input();
        $id     = (int) ($dataIn['id'] ?? 0);

        if ($id <= 0) {
            return $this->fail('Invalid ID');
        }

        $data = [
            'name'          => trim($dataIn['name'] ?? ''),
            'email'         => trim($dataIn['email'] ?? '') ?: null,   // ← NEW
            'relation'      => trim($dataIn['relation'] ?? ''),
            'year_of_birth' => isset($dataIn['year_of_birth']) && $dataIn['year_of_birth'] !== ''
                ? (int) $dataIn['year_of_birth']
                : null,
            'gender'        => trim($dataIn['gender'] ?? '') ?: null,
            'notes'         => trim($dataIn['notes'] ?? '') ?: null,
        ];

        $model = new MemberFamilyModel();

        if (!$model->update($id, $data)) {
            return $this->fail('Unable to update family member', [
                'errors' => $model->errors()
            ]);
        }

        return $this->ok();
    }

    public function delete()
    {
        $dataIn = $this->input();
        $id     = (int) ($dataIn['id'] ?? 0);

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
