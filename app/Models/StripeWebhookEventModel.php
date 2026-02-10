<?php

namespace App\Models;

use CodeIgniter\Model;

class StripeWebhookEventModel extends Model
{
    protected $table = 'stripe_webhook_events';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'stripe_event_id',
        'event_type',
        'api_version',
        'livemode',
        'payload_json',
        'signature_header',
        'received_at',
        'processed_at',
        'process_status',
        'process_error'
    ];
    protected $useTimestamps = false;
}
