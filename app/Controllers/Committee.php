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
            'members' => $members
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
            'members' => $members
        ]);
    }

    public function lcf()
    {
        $data = ['metaDescription' => 'The Lohana Charitable Foundation (LCF) oversees governance, community assets, and the Dhamecha Lohana Centre. Meet our trustees and learn about our charitable work.'];
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
            'members' => $members,
            'groupedEvents' => $groupedEvents
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
            'members' => $members,
            'groupedEvents' => $groupedEvents
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
            'members' => $members,
            'events' => $events
        ]);
    }



}
