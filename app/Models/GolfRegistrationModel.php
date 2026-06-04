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
        'p1_first_name', 'p1_last_name', 'p1_email', 'p1_phone', 'p1_handicap', 'p1_meal',
        'p2_first_name', 'p2_last_name', 'p2_email', 'p2_phone', 'p2_handicap', 'p2_meal',
        'p3_first_name', 'p3_last_name', 'p3_email', 'p3_phone', 'p3_handicap', 'p3_meal',
        'status', 'agreed_terms', 'ip_address',
    ];

    public static function generateRef(): string
    {
        // Omits easily-confused characters (0/O, 1/I/L)
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $code  = '';
        for ($i = 0; $i < 6; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return 'GOLF26-' . $code;
    }
}
