<?php
namespace App\Models;

use CodeIgniter\Model;

class PasswordResetModel extends Model
{
    protected $table = 'password_resets';
    protected $primaryKey = 'id';
    protected $allowedFields = ['member_id', 'token', 'created_at', 'expires_at', 'used_at'];

    public function findValidToken(string $token)
    {
        return $this->where('token', $token)
            ->where('used_at', null)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->first();
    }

}
