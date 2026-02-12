<?php
namespace App\Models;
use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title',
        'slug',
        'description',
        'event_date',
        'location',
        'time_from',
        'time_to',
        'image',
        'committee',
        'ticketinfo',
        'eventterms',
        'contactinfo',
        'requires_registration',
        'capacity',
        'registration_open',

    ];
    protected $useTimestamps = true;

    // Fetch top 10 events (optionally filtered by committee)
    public function getUpcomingEvents($committees = [], $limit = 10)
    {
        $builder = $this->where('event_date >=', date('Y-m-d'))
            ->orderBy('event_date', 'ASC');

        if (!empty($committees)) {
            $builder->whereIn('committee', $committees);
        }

        return $builder->findAll($limit);
    }

    public function getRegisterableEvents()
    {
        return $this->where('is_valid', 1)
            ->where('requires_registration', 1)
            ->where('registration_open', 1)
            ->where('event_date >=', date('Y-m-d'))
            ->orderBy('event_date', 'ASC')
            ->findAll();
    }
}
