<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * The committees that have their own page. Drives the committee cards on
 * About Us and the "other committees" links at the foot of each committee page.
 */
class Committees extends BaseConfig
{
    /**
     * Keyed by route path.
     *   image — card picture; 'contain' for logos, 'cover' for group photos
     *   tone  — colour family, matching the committee page header (.cmt-tone-*)
     *
     * @var array<string, array{name: string, summary: string, image: string, fit: string, tone: string}>
     */
    public array $pages = [
        'committee' => [
            'name'    => 'Executive Committee',
            'summary' => 'The elected committee that runs LCNL. Meet every member, with photos.',
            'image'   => 'assets/img/committee/executive-800.webp',
            'fit'     => 'cover',
            'tone'    => 'brand',
        ],
        'mahila' => [
            'name'    => 'Mahila Committee',
            'summary' => 'Bringing women together through cultural, social and charitable activities.',
            'image'   => 'assets/img/committee/mahila-800.webp',
            'fit'     => 'cover',
            'tone'    => 'ruby',
        ],
        'yls' => [
            'name'    => 'Young Lohana Society',
            'summary' => 'Socials, sports and cultural events for young adults.',
            'image'   => 'assets/img/committee/yls-logo-320.webp',
            'fit'     => 'contain',
            'tone'    => 'yls',
        ],
        'youth' => [
            'name'    => 'Lohana Youth Club',
            'summary' => 'Activities and friendships for families with children aged 13 to 18.',
            'image'   => 'assets/img/committee/lyc-group-800.webp',
            'fit'     => 'cover',
            'tone'    => 'lyc',
        ],
        'lcf' => [
            'name'    => 'Lohana Charitable Foundation',
            'summary' => 'The charity that looks after the Dhamecha Lohana Centre for the community.',
            'image'   => 'assets/img/logos/lcf.jpg',
            'fit'     => 'contain',
            'tone'    => 'lcf',
        ],
    ];
}
