<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Group emails were queued at priority 7, which the email queue admin has
 * no label for (it knows P1–P5), so they showed as "?". They now use 5,
 * "P5 – Bulk"; move any already in the queue across too.
 */
class GroupEmailsToBulkPriority extends Migration
{
    public function up()
    {
        $this->db->table('email_queue')
            ->where('type', 'group_email')
            ->where('priority', 7)
            ->update(['priority' => 5]);
    }

    public function down()
    {
        // Nothing to undo: 5 is the right priority whichever version runs.
    }
}
