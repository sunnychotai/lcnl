<?php

namespace App\Models;

use CodeIgniter\Model;

class MelaStallBookingModel extends Model
{
    protected $table         = 'mela_stall_bookings';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'booking_ref', 'company_name', 'category', 'category_other', 'is_food_stall',
        'items_description', 'contact_name', 'contact_phone', 'contact_email',
        'vehicle_reg', 'comments', 'confirmed_payment', 'agreed_terms',
        'payment_received', 'payment_received_at', 'payment_marked_by',
        'status', 'admin_notes', 'ip_address',
    ];

    /** Stalls that count against capacity. */
    public function countActive(): int
    {
        return $this->where('status !=', 'cancelled')->countAllResults();
    }

    /**
     * Short human-readable reference, e.g. MELA-4F2A.
     * Collisions are vanishingly unlikely but checked anyway.
     */
    public function generateRef(): string
    {
        do {
            $ref = 'MELA-' . strtoupper(bin2hex(random_bytes(2)));
        } while ($this->where('booking_ref', $ref)->withDeleted()->countAllResults() > 0);

        return $ref;
    }

    /**
     * Other bookings sharing this company name or email — surfaced in the admin
     * list so duplicate submissions are visible rather than silently blocked.
     */
    public function findPossibleDuplicates(string $email, string $company, ?int $excludeId = null): array
    {
        $builder = $this->groupStart()
            ->where('contact_email', $email)
            ->orWhere('company_name', $company)
            ->groupEnd()
            ->where('status !=', 'cancelled');

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->findAll();
    }
}
