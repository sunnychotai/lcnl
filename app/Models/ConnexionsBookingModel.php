<?php

namespace App\Models;

use CodeIgniter\Model;

class ConnexionsBookingModel extends Model
{
    protected $table          = 'connexions_bookings';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'booking_ref', 'first_name', 'last_name', 'email', 'phone', 'is_member',
        'eligibility_confirmed', 'privacy_consent', 'girl_tickets', 'boy_tickets', 'ticket_price', 'amount',
        'pay_token', 'stripe_checkout_session_id', 'stripe_payment_intent_id', 'hold_until',
        'payment_received', 'payment_received_at', 'payment_marked_by', 'confirmed_sent_at',
        'status', 'cancelled_at', 'admin_notes', 'ip_address',
    ];

    /**
     * Places taken: paid bookings, plus unpaid ones whose Checkout hold has not
     * run out. Pass $exceptId to leave one booking out (it is asking for its
     * places back), or $upToId to count only bookings made up to and including
     * that one (two people racing for the last place: the later one loses).
     *
     * @return array{girl: int, boy: int}
     */
    public function placesTaken(?int $exceptId = null, ?int $upToId = null): array
    {
        $builder = $this->builder()
            ->select('COALESCE(SUM(girl_tickets), 0) AS girl, COALESCE(SUM(boy_tickets), 0) AS boy')
            ->where('deleted_at', null)
            ->groupStart()
                ->where('status', 'paid')
                ->orGroupStart()
                    ->where('status', 'pending')
                    ->where('hold_until >', date('Y-m-d H:i:s'))
                ->groupEnd()
            ->groupEnd();

        if ($exceptId !== null) {
            $builder->where('id !=', $exceptId);
        }
        if ($upToId !== null) {
            $builder->where('id <=', $upToId);
        }

        $row = $builder->get()->getRowArray();

        return ['girl' => (int) $row['girl'], 'boy' => (int) $row['boy']];
    }

    /** Secret for the pay link; long enough that it cannot be guessed. */
    public function generatePayToken(): string
    {
        return bin2hex(random_bytes(24));
    }

    /** Short reference, e.g. CNX26-7F3A2C. Only needs to be unique, not secret. */
    public function generateRef(string $prefix): string
    {
        do {
            $ref = $prefix . '-' . strtoupper(bin2hex(random_bytes(3)));
        } while ($this->where('booking_ref', $ref)->withDeleted()->countAllResults() > 0);

        return $ref;
    }
}
