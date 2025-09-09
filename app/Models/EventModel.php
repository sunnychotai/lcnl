<?php namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table      = 'events';
    protected $primaryKey = 'id';

    protected $useTimestamps = true;
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'title', 'event_date', 'time_from', 'time_to', 'location',
        'committee', 'description', 'image', 'is_valid'
    ];

    public function getUpcoming($limit = 10)
{
    return $this->where('event_date >=', date('Y-m-d'))
                ->where('is_valid', 1)
                ->orderBy('event_date', 'ASC')
                ->limit($limit)
                ->findAll();
}



}
