<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Family extends BaseConfig
{
    /**
     * Relations list:
     * key = DB value
     * label = display name
     * icon = Bootstrap Icon
     */
    public array $relations = [
        'spouse' => ['label' => 'Spouse', 'icon' => 'bi-heart-fill'],
        'child' => ['label' => 'Child', 'icon' => 'bi-person-fill'],
    ];

    public array $genders = [
        'male',
        'female',
        'other',
        'prefer_not_to_say',
    ];
}
