<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberModel extends Model
{
    protected $table            = 'members';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'first_name','last_name','email','mobile','password_hash',
        'postcode','status','verified_at','verified_by','consent_at',
        'source','created_at','updated_at','deleted_at'
    ];

    protected $beforeInsert = ['normalizeFields'];
    protected $beforeUpdate = ['normalizeFields'];

    protected function normalizeFields(array $data): array
    {
        if (isset($data['data']['email']) && $data['data']['email'] !== null) {
            $data['data']['email'] = strtolower(trim($data['data']['email']));
        }
        if (isset($data['data']['mobile']) && $data['data']['mobile'] !== null) {
            // keep +, strip spaces/dashes
            $m = preg_replace('/[^\+\d]/', '', (string) $data['data']['mobile']);
            $data['data']['mobile'] = $m ?: null;
        }
        if (isset($data['data']['postcode']) && $data['data']['postcode'] !== null) {
            $data['data']['postcode'] = strtoupper(trim($data['data']['postcode']));
        }
        return $data;
    }

    // Query helpers
    public function listByStatus(?string $status, ?string $q = null, int $limit = 50): array
    {
        $builder = $this->builder();
        if ($status && $status !== 'all') {
            $builder->where('status', $status);
        }
        if ($q) {
            $builder->groupStart()
                    ->like('first_name', $q)
                    ->orLike('last_name', $q)
                    ->orLike('email', $q)
                    ->orLike('mobile', $q)
                    ->groupEnd();
        }
        return $builder->orderBy('created_at', 'DESC')->limit($limit)->get()->getResultArray();
    }

    public function countsByStatus(): array
    {
        $rows = $this->select('status, COUNT(*) as cnt')->groupBy('status')->findAll();
        $map = ['pending'=>0,'active'=>0,'disabled'=>0];
        foreach ($rows as $r) { $map[$r['status']] = (int) $r['cnt']; }
        return $map;
    }
}
