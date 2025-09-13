<?php

namespace App\Models;

use CodeIgniter\Model;

class FamilyModel extends Model
{
    protected $table          = 'families';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $returnType     = 'array';
    protected $allowedFields  = [
        'household_name', 'lead_member_id', 'invite_code',
        'address1','address2','city','postcode','created_at','updated_at'
    ];

    protected $beforeInsert = ['normalize'];
    protected $beforeUpdate = ['normalize'];

    protected function normalize(array $data): array
    {
        if (isset($data['data']['postcode']) && $data['data']['postcode'] !== null) {
            $data['data']['postcode'] = strtoupper(trim($data['data']['postcode']));
        }
        if (isset($data['data']['household_name'])) {
            $data['data']['household_name'] = trim($data['data']['household_name']);
        }
        return $data;
    }

    public function findByInviteCode(string $code): ?array
    {
        return $this->where('invite_code', strtoupper($code))->first();
    }
}
