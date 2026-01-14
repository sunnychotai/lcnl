<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberModel extends Model
{
    protected $table = 'members';
    protected $primaryKey = 'id';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'first_name',
        'last_name',
        'email',
        'mobile',
        'password_hash',
        'address1',
        'address2',
        'city',
        'postcode',
        'status',
        'verified_at',
        'verified_by',
        'consent_at',
        'source',
        'last_login',
        'created_at',
        'updated_at',
        'deleted_at',
        'date_of_birth',
        'gender',
        'activation_sent_at',
        'activated_at',
        'is_valid_email',
        'disabled_reason',
        'disabled_notes',
        'disabled_at',
        'disabled_by',
    ];

    protected $beforeInsert = ['normalizeFields'];
    protected $beforeUpdate = ['normalizeFields'];

    /**
     * Normalise key fields before save
     */
    protected function normalizeFields(array $data): array
    {
        if (isset($data['data']['email']) && $data['data']['email'] !== null) {
            $data['data']['email'] = strtolower(trim($data['data']['email']));
        }
        if (isset($data['data']['mobile']) && $data['data']['mobile'] !== null) {
            // Keep '+' and digits only, strip spaces/dashes
            $m = preg_replace('/[^\+\d]/', '', (string) $data['data']['mobile']);
            $data['data']['mobile'] = $m ?: null;
        }
        if (isset($data['data']['postcode']) && $data['data']['postcode'] !== null) {
            $data['data']['postcode'] = strtoupper(trim($data['data']['postcode']));
        }
        if (isset($data['data']['city']) && $data['data']['city'] !== null) {
            $data['data']['city'] = ucwords(strtolower(trim($data['data']['city'])));
        }
        return $data;
    }

    /**
     * Reusable listing helper
     */
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
                ->orLike('city', $q)
                ->groupEnd();
        }

        return $builder->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Count members grouped by status
     */
    public function countsByStatus(): array
    {
        $rows = $this->select('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->findAll();

        $map = ['pending' => 0, 'active' => 0, 'disabled' => 0];
        foreach ($rows as $r) {
            $map[$r['status']] = (int) $r['cnt'];
        }
        return $map;
    }

    /**
     * Reusable filtered search (for index/export)
     */
    public function search(?string $status = 'all', ?string $q = null)
    {
        $builder = $this->builder();

        if ($status !== 'all') {
            $builder->where('status', $status);
        }

        if ($q) {
            $builder->groupStart()
                ->like('first_name', $q)
                ->orLike('last_name', $q)
                ->orLike('email', $q)
                ->orLike('mobile', $q)
                ->orLike('city', $q)
                ->groupEnd();
        }

        return $builder->orderBy('created_at', 'DESC');
    }
}
