<?php

namespace App\Models;

use CodeIgniter\Model;

class ConnexionsAttendeeModel extends Model
{
    protected $table         = 'connexions_attendees';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'booking_id', 'gender', 'position', 'first_name', 'last_name', 'email', 'phone',
    ];

    /** A booking's attendees: girls, then boys, each in the order they were entered. */
    public function forBooking(int $bookingId): array
    {
        return $this->where('booking_id', $bookingId)
            ->orderBy('gender', 'DESC')
            ->orderBy('position', 'ASC')
            ->findAll();
    }

    /**
     * Attendees of several bookings, grouped by booking id.
     *
     * @return array<int, list<array>>
     */
    public function forBookings(array $bookingIds): array
    {
        if ($bookingIds === []) {
            return [];
        }

        $grouped = [];
        $rows    = $this->whereIn('booking_id', $bookingIds)
            ->orderBy('gender', 'DESC')
            ->orderBy('position', 'ASC')
            ->findAll();

        foreach ($rows as $row) {
            $grouped[(int) $row['booking_id']][] = $row;
        }

        return $grouped;
    }
}
