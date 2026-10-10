<?php

namespace App\Models;

use CodeIgniter\Model;

class LegacyPledgeModel extends Model
{
    protected $table          = 'legacy_pledges';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'pledge_ref', 'title', 'first_name', 'last_name', 'email', 'phone',
        'house_name_number', 'street', 'town', 'postcode',
        'plaque_name', 'plaque_name_submitted',
        'gift_aid', 'gift_aid_declared_at', 'gift_aid_version', 'gift_aid_wording',
        'payment_reference', 'payment_timing', 'amount',
        'confirmed_donation', 'privacy_consent', 'comments',
        'payment_received', 'payment_received_at', 'payment_marked_by',
        'thank_you_sent_at', 'reminder_sent_at', 'reminder_count',
        'status', 'admin_notes', 'ip_address',
    ];

    /** Pledges that count towards the target. */
    public function countActive(): int
    {
        return $this->where('status !=', 'cancelled')->countAllResults();
    }

    /**
     * Short reference, e.g. L50-7F3A2C. Never used to look a pledge up from
     * a public URL, so it only needs to be unique, not secret.
     */
    public function generateRef(): string
    {
        do {
            $ref = 'L50-' . strtoupper(bin2hex(random_bytes(3)));
        } while ($this->where('pledge_ref', $ref)->withDeleted()->countAllResults() > 0);

        return $ref;
    }
}
