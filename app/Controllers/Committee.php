<?php
namespace App\Controllers;

use App\Models\CommitteeModel;

class Committee extends BaseController
{
    public function index()
    {
        $committeeModel = new CommitteeModel();
        $members = $committeeModel->getAllOrdered();

        return view('committee', [
            'title'           => 'Our Committees',
            'metaDescription' => 'Meet the committees that run Lohana Community North London — Executive, Mahila Mandal, Youth and YLS.',
            'members'         => $members
        ]);
    }

    public function committee()
    {

        $committeeModel = new CommitteeModel();
        $members = $committeeModel
            ->where('committee', 'Executive')   // 👈 filter
            ->orderBy('id', 'ASC')              // or whatever column you use
            ->findAll();
        return view('committees/committee', [
            'title'           => 'Executive Committee',
            'metaDescription' => 'The LCNL Executive Committee — the elected members who lead the community.',
            'members'         => $members
        ]);
    }

    public function lcf()
    {
        $data = [
            'title'           => 'Lohana Charitable Foundation',
            'metaDescription' => 'The Lohana Charitable Foundation (LCF) oversees governance, community assets, and the Dhamecha Lohana Centre. Meet our trustees and learn about our charitable work.',
        ];
        return view('committees/lcf', $data);
    }

    public function mahila()
    {
        $committeeModel = new \App\Models\CommitteeModel();
        $eventModel = new \App\Models\EventModel();

        // Mahila members
        $members = $committeeModel
            ->where('committee', 'Mahila')
            ->orderBy('id', 'ASC')
            ->findAll();

        // Upcoming events for Executive + Mahila committees (limit 10)
        $events = $eventModel
            ->where('event_date >=', date('Y-m-d'))
            ->whereIn('committee', ['Executive', 'Mahila'])
            ->orderBy('event_date', 'ASC')
            ->findAll(10);

        // Group events by month for display
        $groupedEvents = [];
        foreach ($events as $e) {
            $month = date('F Y', strtotime($e['event_date']));
            $groupedEvents[$month][] = $e;
        }

        return view('committees/mahila', [
            'title'           => 'Mahila Mandal',
            'metaDescription' => 'The LCNL Mahila Mandal — the women\'s committee behind many of the community\'s events and cultural activities.',
            'members'         => $members,
            'groupedEvents'   => $groupedEvents
        ]);
    }

    public function yls()
    {
        $committeeModel = new \App\Models\CommitteeModel();
        $eventModel = new \App\Models\EventModel();

        // Mahila  members
        $members = $committeeModel
            ->where('committee', 'YLS')
            ->orderBy('id', 'ASC')
            ->findAll();

        // Upcoming events for Executive + Mahila committees (limit 10)
        $events = $eventModel
            ->where('event_date >=', date('Y-m-d'))
            ->whereIn('committee', ['YLS'])
            ->orderBy('event_date', 'ASC')
            ->findAll(10);

        // Group events by month for display
        $groupedEvents = [];
        foreach ($events as $e) {
            $month = date('F Y', strtotime($e['event_date']));
            $groupedEvents[$month][] = $e;
        }

        return view('committees/yls', [
            'title'           => 'Young Lohana Society',
            'metaDescription' => 'The Young Lohana Society (YLS) — social, sporting and cultural activities for young adults in the LCNL community.',
            'members'         => $members,
            'groupedEvents'   => $groupedEvents
        ]);
    }


    public function youth()
    {
        $committeeModel = new \App\Models\CommitteeModel();
        $eventModel = new \App\Models\EventModel();

        // Mahila  members
        $members = $committeeModel
            ->where('committee', 'YC')
            ->orderBy('id', 'ASC')
            ->findAll();

        // Upcoming events for Executive + Mahila committees (limit 10)
        $events = $eventModel
            ->where('event_date >=', date('Y-m-d'))
            ->whereIn('committee', ['Youth'])
            ->orderBy('event_date', 'ASC')
            ->findAll(10);

        return view('committees/youth', [
            'title'           => 'Youth Committee',
            'metaDescription' => 'The LCNL Youth Committee — activities, events and opportunities for the community\'s younger members.',
            'members'         => $members,
            'events'          => $events
        ]);
    }



}
