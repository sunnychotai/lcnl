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
}
