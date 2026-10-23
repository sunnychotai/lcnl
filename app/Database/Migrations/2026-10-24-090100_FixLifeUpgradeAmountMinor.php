<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Life upgrades were recorded with amount_minor = 75 (75p) instead of 7500
 * (£75). Stripe charged the right amount from the price; only our record was
 * wrong.
 */
class FixLifeUpgradeAmountMinor extends Migration
{
    public function up()
    {
        $this->db->table('membership_payments')
            ->where('purpose', 'life_upgrade')
            ->where('amount_minor', 75)
            ->update(['amount_minor' => 7500]);
    }

    public function down()
    {
        // Nothing to undo: 75p was never a real amount.
    }
}
