<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberAuditLogModel extends Model
{
    protected $table = 'member_audit_log';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false; // we use changed_at explicitly

    protected $allowedFields = [
        'member_id',
        'type',
        'field_name',
        'old_value',
        'new_value',
        'description',
        'changed_by',
        'changed_at',
    ];
}
