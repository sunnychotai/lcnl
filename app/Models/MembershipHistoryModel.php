<?php

namespace App\Models;

use CodeIgniter\Model;

class MembershipHistoryModel extends Model
{
    protected $table = 'membership_history';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'member_id',
        'changed_by',
        'old_type',
        'new_type',
        'notes',
        'created_at'
    ];

    public $useTimestamps = false;
}
