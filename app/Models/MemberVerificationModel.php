<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberVerificationModel extends Model
{
    protected $table            = 'member_verifications';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'member_id',
        'token',
        'created_at',
        'expires_at',
        'used_at',
    ];

    /**
     * Mark a token as used.
     */
    public function markUsed(int $id): bool
    {
        return $this->update($id, ['used_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Find a valid (unused + unexpired) verification by token.
     */
    public function findValidToken(string $token): ?array
    {
        return $this->where('token', $token)
            ->where('used_at', null)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->first();
    }
}
