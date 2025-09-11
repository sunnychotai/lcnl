<?php namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table      = 'events';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title', 'description', 'event_date', 'location', 'time_from', 'time_to','image', 'committee', 'ticketinfo','eventterms','contactinfo'
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
}
