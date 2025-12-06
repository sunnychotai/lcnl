<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberFamilyModel extends Model
{
    protected $table            = 'member_family';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'member_id',
        'name',
        'email',
        'relation',
        'year_of_birth',
        'gender',
        'notes',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    // Optional: basic validation (server-side)
    protected $validationRules = [
        'member_id'     => 'required|is_natural_no_zero',
        'name'          => 'required|min_length[2]|max_length[120]',
        'email'         => 'permit_empty|valid_email',

        'relation'      => 'required|in_list[Spouse,Child,Parent,Sibling,Other]',
        'year_of_birth' => 'permit_empty|integer|greater_than_equal_to[1900]', // ← removed date('Y')
        'gender'        => 'permit_empty|in_list[Male,Female,Other,Prefer not to say]',
        'notes'         => 'permit_empty|max_length[255]',
    ];
}
