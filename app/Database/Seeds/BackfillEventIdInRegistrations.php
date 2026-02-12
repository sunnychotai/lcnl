<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BackfillEventIdInRegistrations extends Seeder
{
    public function run()
    {
        // Example mapping by title (adjust for your data)
        $event = $this->db->table('events')
            ->where('title', 'Maha Shivratri 2026')
            ->get()
            ->getRowArray();

        if (!$event) {
            echo "Event not found: Maha Shivratri 2026\n";
            return;
        }

        $eventId = (int) $event['id'];

        $this->db->table('event_registrations')
            ->where('event_name', 'Maha Shivratri 2026')
            ->update(['event_id' => $eventId]);

        echo "Backfilled event_id for registrations.\n";
    }
}
