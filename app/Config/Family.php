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
        'son' => ['label' => 'Son', 'icon' => 'bi-person-fill'],
        'daughter' => ['label' => 'Daughter', 'icon' => 'bi-person-fill'],
        'mother' => ['label' => 'Mother', 'icon' => 'bi-person-standing-dress'],
        'father' => ['label' => 'Father', 'icon' => 'bi-person-standing'],
        'grandparent' => ['label' => 'Grandparent', 'icon' => 'bi-people-fill'],
        'sibling' => ['label' => 'Sibling', 'icon' => 'bi-people'],
        'other' => ['label' => 'Other', 'icon' => 'bi-person'],
    ];

    public array $genders = [
        'male',
        'female',
        'other',
        'prefer_not_to_say',
    ];
}
