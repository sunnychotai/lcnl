<?php

namespace App\Models;

use CodeIgniter\Model;

class FamilyMemberModel extends Model
{
    protected $table          = 'family_members';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $returnType     = 'array';
    protected $allowedFields  = ['family_id','member_id','role','label','created_at','updated_at'];

    public function isLinked(int $familyId, int $memberId): bool
    {
        return (bool) $this->where(['family_id'=>$familyId,'member_id'=>$memberId])->countAllResults();
    }

    public function membersOf(int $familyId): array
    {
        // join to members for display
        return $this->select('family_members.*, members.first_name, members.last_name, members.email, members.mobile, members.status')
                    ->join('members', 'members.id = family_members.member_id')
                    ->where('family_members.family_id', $familyId)
                    ->orderBy("FIELD(role,'lead','spouse','dependent','other')", '', false)
                    ->orderBy('members.last_name','ASC')
                    ->findAll();
    }
}
