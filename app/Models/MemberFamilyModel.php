<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberFamilyModel extends Model
{
    protected $table = 'member_family';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'member_id',
        'name',
        'relation',
        'year_of_birth',
        'gender',
        'notes',
        'created_at',
        'updated_at'
    ];
}
