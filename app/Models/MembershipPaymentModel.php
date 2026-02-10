<?php

namespace App\Models;

use CodeIgniter\Model;

class MembershipPaymentModel extends Model
{
    protected $table = 'membership_payments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'member_id',
        'provider',
        'purpose',
        'status',
        'amount_minor',
        'currency',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
        'stripe_customer_id',
        'idempotency_key',
        'failure_code',
        'failure_message',
        'paid_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
