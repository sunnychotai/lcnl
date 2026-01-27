<?php

namespace App\Models;

use CodeIgniter\Model;

class EventRegistrationModel extends Model
{
    protected $table = 'event_registrations';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'event_name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'num_participants',
        'num_guests',
        'notes',
        'status',
        'member_id',
        'is_lcnl_member',
        'agreed_terms',
        'ip_address',
    ];


    protected $useTimestamps = true;
    protected $returnType = 'array';

    public function getTotalParticipantsForEvent(string $eventName): int
{
    return (int) $this->selectSum('num_participants')
        ->where('event_name', $eventName)
        ->whereIn('status', ['submitted', 'confirmed'])
        ->get()
        ->getRow()
        ->num_participants ?? 0;
}

public function getTotalRegistrationsForEvent(string $eventName): int
{
    return (int) $this->where('event_name', $eventName)
        ->whereIn('status', ['submitted', 'confirmed'])
        ->countAllResults();
}


}
