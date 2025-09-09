<?php namespace App\Models;

use CodeIgniter\Model;

class CommitteeModel extends Model
{
    protected $table      = 'committee';
    protected $primaryKey = 'id';
    protected $returnType = 'array';   // ✅ always array

    protected $allowedFields = [
        'firstname', 'surname', 'email', 'role', 'committee', 'display_order', 'image', 'url'
    ];

    protected $useTimestamps = true;

    public function getAllOrdered()
    {
        return $this->orderBy('display_order', 'ASC')->findAll();
    }
}
