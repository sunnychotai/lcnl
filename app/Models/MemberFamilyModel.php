<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Family as FamilyConfig;

class MemberFamilyModel extends Model
{
    protected $table = 'member_family';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'member_id',
        'name',
        'email',
        'relation',
        'year_of_birth',
        'gender',
        'notes',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function __construct()
    {
        parent::__construct();

        // Load config
        $config = new FamilyConfig();

        // Build dynamic validation lists
        $relationKeys = implode(',', array_keys($config->relations));   // e.g. "son,daughter,mother,etc"
        $genderList = implode(',', $config->genders);

        $currentYear = date('Y');

        // Dynamic validation rules
        $this->validationRules = [
            'member_id' => 'required|is_natural_no_zero',
            'name' => 'required|min_length[2]|max_length[120]',
            'email' => 'permit_empty|valid_email|max_length[120]',
            'relation' => "required|in_list[$relationKeys]",
            'year_of_birth' => "permit_empty|integer|greater_than_equal_to[1900]|less_than_equal_to[$currentYear]",
            'gender' => "permit_empty|in_list[$genderList]",
            'notes' => 'permit_empty|max_length[255]',
        ];
    }
}
