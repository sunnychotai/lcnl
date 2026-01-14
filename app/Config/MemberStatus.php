<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class MemberStatus extends BaseConfig
{
    /**
     * Reasons for disabling a member
     * key   = stored in DB
     * label = shown to admin
     * icon  = Bootstrap icon
     * class = optional UI styling
     */
    public array $disableReasons = [
        'deceased' => [
            'label' => 'Deceased',
            'icon' => 'bi-heartbreak',
            'class' => 'text-danger',
        ],
        'moved_house' => [
            'label' => 'Moved House',
            'icon' => 'bi-house-dash',
        ],
        'manual' => [
            'label' => 'Disabled (Manual)',
            'icon' => 'bi-slash-circle',
        ],
    ];
}
