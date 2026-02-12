<?php

namespace App\Models;

use CodeIgniter\Model;

class EventRegistrationModel extends Model
{
    protected $table = 'event_registrations';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'event_id',
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
        'ip_address'
    ];

    protected $useTimestamps = true;
    protected $returnType = 'array';

    public function getTotalParticipantsForEventId(int $eventId): int
    {
        $row = $this->selectSum('num_participants')
            ->where('event_id', $eventId)
            ->whereIn('status', ['submitted', 'confirmed'])
            ->get()
            ->getRow();

        return (int) ($row->num_participants ?? 0);
    }

    public function getTotalRegistrationsForEventId(int $eventId): int
    {
        return (int) $this->where('event_id', $eventId)
            ->whereIn('status', ['submitted', 'confirmed'])
            ->countAllResults();
    }



}

