<?php

namespace App\Models;

use CodeIgniter\Model;

class ChopdaRegistrationModel extends Model
{
    protected $table          = 'chopda_registrations';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'registration_ref', 'first_name', 'last_name', 'email', 'phone', 'num_attendees',
        'payment_reference', 'payment_timing', 'amount', 'privacy_consent', 'comments',
        'payment_received', 'payment_received_at', 'payment_marked_by',
        'confirmed_sent_at', 'reminder_sent_at', 'reminder_count',
        'status', 'admin_notes', 'ip_address',
    ];

    /** Registrations that hold a pooja place. */
    public function countActive(): int
    {
        return $this->where('status !=', 'cancelled')->countAllResults();
    }

    /**
     * Short reference, e.g. CP26-7F3A2C. Never used to look a registration up
     * from a public URL, so it only needs to be unique, not secret.
     */
    public function generateRef(): string
    {
        do {
            $ref = 'CP26-' . strtoupper(bin2hex(random_bytes(3)));
        } while ($this->where('registration_ref', $ref)->withDeleted()->countAllResults() > 0);

        return $ref;
    }
}
