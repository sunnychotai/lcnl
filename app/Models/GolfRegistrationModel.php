<?php

namespace App\Models;

use CodeIgniter\Model;

class GolfRegistrationModel extends Model
{
    protected $table      = 'golf_registrations';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'registration_ref', 'team_name',
        'p1_first_name', 'p1_last_name', 'p1_email', 'p1_phone', 'p1_handicap', 'p1_meal', 'p1_tshirt',
        'p2_first_name', 'p2_last_name', 'p2_email', 'p2_phone', 'p2_handicap', 'p2_meal', 'p2_tshirt',
        'p3_first_name', 'p3_last_name', 'p3_email', 'p3_phone', 'p3_handicap', 'p3_meal', 'p3_tshirt',
        'p4_first_name', 'p4_last_name', 'p4_email', 'p4_phone', 'p4_handicap', 'p4_meal', 'p4_tshirt',
        'status', 'agreed_terms', 'ip_address',
    ];

    public function countTotalPlayers(): int
    {
        $rows = $this->whereNotIn('status', ['cancelled'])->findAll();
        $count = 0;
        foreach ($rows as $row) {
            if (!empty($row['p1_first_name'])) $count++;
            if (!empty($row['p2_first_name'])) $count++;
            if (!empty($row['p3_first_name'])) $count++;
            if (!empty($row['p4_first_name'])) $count++;
        }
        return $count;
    }

    public function generateRef(string $surname): string
    {
        // Letters only, uppercase, max 12 chars
        $clean = strtoupper(preg_replace('/[^A-Za-z]/', '', $surname));
        $clean = substr($clean, 0, 12) ?: 'GUEST';

        $base = 'GOLF26-' . $clean;

        if (!$this->where('registration_ref', $base)->first()) {
            return $base;
        }

        $i = 2;
        while ($this->where('registration_ref', $base . '-' . $i)->first()) {
            $i++;
        }

        return $base . '-' . $i;
    }
}
