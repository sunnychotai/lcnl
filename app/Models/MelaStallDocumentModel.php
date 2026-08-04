<?php

namespace App\Models;

use CodeIgniter\Model;

class MelaStallDocumentModel extends Model
{
    protected $table         = 'mela_stall_documents';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'booking_id', 'original_name', 'stored_name', 'mime_type', 'size_bytes',
    ];

    public function forBooking(int $bookingId): array
    {
        return $this->where('booking_id', $bookingId)->orderBy('id', 'ASC')->findAll();
    }
}
