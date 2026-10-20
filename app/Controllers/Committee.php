<?php
namespace App\Controllers;

use App\Models\CommitteeModel;
use App\Models\EventModel;

class Committee extends BaseController
{
    /**
     * Executive roles that are officer posts, in the order they are listed.
     * Roles starting "EXECUTIVE COMMITTEE" are ordinary members; everything
     * else (LCF, RCT, Mahila, Senior and YLS posts) represents another body.
     */
    private const OFFICER_ROLES = [
        'PRESIDENT', 'VICE PRESIDENT', 'SECRETARY', 'ASSISTANT SECRETARY', 'TREASURER',
        'ASSISTANT TREASURER', 'SOCIAL SECRETARY', 'ASSISTANT SOCIAL SECRETARY', 'MEMBERSHIP SECRETARY',
        'ASSISTANT MEMBERSHIP SECRETARY', 'WEBMASTER', 'ASSISTANT WEBMASTER',
        'IMMEDIATE PAST PRESIDENT', 'IMMEDIATE PAST SECRETARY',
    ];

    public function committee()
    {
        $rows = (new CommitteeModel())
            ->where('committee', 'Executive')
            ->orderBy('id', 'ASC')
            ->findAll();

        $groups = ['officers' => [], 'members' => [], 'representatives' => []];

        foreach ($rows as $m) {
            $role = strtoupper(trim((string) $m['role']));
            $group = in_array($role, self::OFFICER_ROLES, true) ? 'officers'
                : (str_starts_with($role, 'EXECUTIVE COMMITTEE') ? 'members' : 'representatives');

            $groups[$group][] = [
                'name'  => trim($m['firstname'] . ' ' . $m['surname']),
                // Ordinary members' role is just "Executive Committee", which the heading already says
                'role'  => $group === 'members' ? (str_contains($role, 'CO-OPT') ? 'Co-opted' : null) : $this->roleLabel($role),
                'photo' => 'assets/img/committee/' . basename((string) $m['image']),
            ];
        }

        return view('committees/committee', [
            'title'           => 'Executive Committee',
            'metaDescription' => 'The LCNL Executive Committee 2025–27: the elected officers and members who lead the community.',
            'groups'          => $groups,
        ]);
    }

    public function lcf()
    {
        $trustees = [
            'pradip-dhamecha.jpg'  => 'Pradip Dhamecha',
            'vinod-thakrar.jpg'    => 'Vinod Thakrar',
            'bhavesh-radia.jpg'    => 'Bhavesh Radia',
            'janu-kotecha.jpg'     => 'Janu Kotecha',
            'vimal-pau.jpg'        => 'Vimal Pau',
            'yatinbhai-dawada.jpg' => 'Yatinbhai Dawada',
            'jagdish-nagrecha.jpg' => 'Jagdish Nagrecha',
            'sudhir-karia.jpg'     => 'Sudhir Karia',
            'rashik-kantaria.jpg'  => 'Rashik Kantaria',
            'reena-popat.jpg'      => 'Reena Popat',
            'ramesh-kantaria.jpg'  => 'Ramesh Kantaria',
            'yagnish-chotai.jpg'   => 'Yagnish Chotai',
            'narendra-thakrar.jpg' => 'Narendra Thakrar',
            'ronak-paw.jpg'        => 'Ronak Paw',
            'jeet-rughani.jpg'     => 'Jeet Rughani',
            'ajay-gokani.jpg'      => 'Ajay Gokani',
        ];

        $people = [];
        foreach ($trustees as $file => $name) {
            $people[] = [
                'name'  => $name,
                'role'  => $name === 'Vinod Thakrar' ? 'Chairperson' : null,
                'photo' => 'assets/img/committee/lcf/' . $file,
            ];
        }

        return view('committees/lcf', [
            'title'           => 'Lohana Charitable Foundation',
            'metaDescription' => 'The Lohana Charitable Foundation (LCF) oversees governance, community assets, and the Dhamecha Lohana Centre. Meet our trustees and learn about our charitable work.',
            'trustees'        => $people,
        ]);
    }

    public function mahila()
    {
        return view('committees/mahila', [
            'title'           => 'Mahila Committee',
            'metaDescription' => 'The LCNL Mahila Committee — the women\'s committee behind many of the community\'s events and cultural activities.',
            'events'          => $this->upcoming('Mahila'),
        ]);
    }

    public function yls()
    {
        return view('committees/yls', [
            'title'           => 'Young Lohana Society',
            'metaDescription' => 'The Young Lohana Society (YLS) — social, sporting and cultural activities for young adults in the LCNL community.',
        ]);
    }

    public function youth()
    {
        return view('committees/youth', [
            'title'           => 'Lohana Youth Club',
            'metaDescription' => 'The Lohana Youth Club (LYC) — activities, events and friendships for families with children aged 13 to 18.',
            'events'          => $this->upcoming('Youth'),
        ]);
    }

    /** The next few events run by one committee. */
    private function upcoming(string $committee): array
    {
        return (new EventModel())
            ->where('event_date >=', date('Y-m-d'))
            ->where('committee', $committee)
            ->orderBy('event_date', 'ASC')
            ->findAll(6);
    }

    /** "LCNL REP for RCT" → "LCNL Rep for RCT", keeping the organisations' initials. */
    private function roleLabel(string $role): string
    {
        $label = ucwords(strtolower($role));
        $label = preg_replace_callback('/(?<=\s)(For|Of|And|The)\b/', static fn(array $m): string => strtolower($m[1]), $label);

        return preg_replace_callback(
            '/\b(Lcnl|Lcf|Rct|Yls)\b/',
            static fn(array $m): string => strtoupper($m[1]),
            $label
        );
    }
}
