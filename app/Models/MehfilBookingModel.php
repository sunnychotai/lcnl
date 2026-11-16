<?php

namespace App\Models;

use CodeIgniter\Model;

class MehfilBookingModel extends Model
{
    protected $table          = 'mehfil_bookings';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'booking_ref', 'first_name', 'last_name', 'email', 'phone', 'postcode', 'is_member',
        'individual_tickets', 'tables', 'price_band', 'individual_price', 'table_price', 'amount',
        'privacy_consent', 'payment_received', 'payment_received_at', 'payment_marked_by',
        'confirmed_sent_at', 'reminder_sent_at', 'reminder_count',
        'status', 'cancelled_at', 'admin_notes', 'ip_address',
    ];

    /** Seats held by bookings that are not cancelled; a table counts as $tableSize. */
    public function seatsTaken(int $tableSize): int
    {
        $row = $this->builder()
            ->select('COALESCE(SUM(individual_tickets), 0) AS individual, COALESCE(SUM(tables), 0) AS tables')
            ->where('status !=', 'cancelled')
            ->where('deleted_at', null)
            ->get()->getRowArray();

        return (int) $row['individual'] + (int) $row['tables'] * $tableSize;
    }

    /**
     * The first free reference in the series XMAS-RONPAW, XMAS-RONPAW2, …
     * Cancelled and deleted bookings keep theirs, so a payment that arrives
     * late can still be traced.
     */
    public function uniqueRef(string $base): string
    {
        $taken = array_map('strtoupper', array_column(
            $this->withDeleted()->select('booking_ref')->like('booking_ref', $base, 'after')->findAll(),
            'booking_ref'
        ));

        $ref = $base;
        for ($n = 2; in_array($ref, $taken, true); $n++) {
            $ref = $base . $n;
        }

        return $ref;
    }
}
