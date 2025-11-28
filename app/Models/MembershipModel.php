<?php

namespace App\Models;

use CodeIgniter\Model;

class MembershipModel extends Model
{
    protected $table = 'memberships';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'member_id',
        'membership_type',
        'membership_number',
        'start_date',
        'end_date',
        'status',
        'notes',
        'created_at',
        'updated_at'
    ];
}
