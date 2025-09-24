<?php

namespace App\Controllers;
use App\Models\FaqModel;
use App\Models\CommitteeModel;
use App\Models\EventModel;

class Home extends BaseController
{
public function index()
{
    $eventModel = new EventModel();
    $data['upcomingEvents'] = $eventModel->getUpcomingEvents([], 10);

    // === Live Stream Auto Selection ===
    $streams = [
        1  => 'VW0mUF6P1Zk',
        2  => '1pT6vJt4LW0',
        3  => 'nln_27kmOsI',
        4  => 'Ti7OZCj_6nc',
        5  => 'QdGwgvrBxDg',
        6  => 'O7sYOYHj5s8',
        7  => 'yMnTesCsfMs',
        8  => 'k_DHIPRGhIc',   // Aatham
        9  => 'aZGgF0Mvksc',
        10 => 'R4CqwOBXj0g',   // Sharad Poonam
    ];

    // Define Day 1 date (Navratri start)
    $startDate = new \DateTime('2025-09-22'); 
    $today     = new \DateTime();

    // Work out which day we are on
    $diff = $startDate->diff($today)->days + 1; // 1-based index
    $dayNumber = min(max($diff, 1), count($streams));

    // Get video ID
    $data['videoId']   = $streams[$dayNumber] ?? null;
    $data['dayNumber'] = $dayNumber;

    return view('home', $data);
}


public function events()
{
    $eventModel = new \App\Models\EventModel();

    // Get all upcoming events
    $events = $eventModel
        ->where('is_valid', 1)
        ->where('event_date >=', date('Y-m-d'))
        ->orderBy('event_date', 'ASC')
        ->findAll();

    // Group events by month-year
    $groupedEvents = [];
    foreach ($events as $event) {
        $month = date('F Y', strtotime($event['event_date']));
        $groupedEvents[$month][] = $event;
    }

    return view('events', [
        'groupedEvents' => $groupedEvents
    ]);
}

public function mahila()
{
    $committeeModel = new \App\Models\CommitteeModel();
    $eventModel     = new \App\Models\EventModel();

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
        'members'       => $members,
        'groupedEvents' => $groupedEvents
    ]);
}


    public function gallery() { return view('gallery'); }
    public function contact() { return view('contact'); }
    public function bereavement()
        {
            $faqModel = new FaqModel();
            $data['faqs'] = $faqModel->getByGroup('Bereavement'); // <-- pull from DB
            return view('bereavement', $data); // <-- point to your updated bereavement view
        }    
    
    public function membership() { return view('membership'); }

    public function aboutus() { return view('aboutus'); }

    public function lcf() {    
        $data = ['metaDescription' => 'The Lohana Charitable Foundation (LCF) oversees governance, community assets, and the Dhamecha Lohana Centre. Meet our trustees and learn about our charitable work.'];
        return view('committees/lcf', $data);
     }

    public function committee() { 
        
        $committeeModel = new CommitteeModel();
        $members = $committeeModel
        ->where('committee', 'Executive')   // 👈 filter
        ->orderBy('id', 'ASC')              // or whatever column you use
        ->findAll();
        return view('committees/committee', [
            'members' => $members
        ]);
    }

    public function faq()
{
    $faqModel = new \App\Models\FaqModel();
    $data['groupedFaqs'] = $faqModel->getGrouped(); // fetch grouped FAQs

    return view('faqs/index', $data);
}

public function eventDetail($id)
{
    $eventModel = new \App\Models\EventModel();

    // Get the selected event
    $event = $eventModel->find($id);

    if (! $event) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Event not found');
    }

    // Get 6 upcoming events (excluding current one)
    $upcomingEvents = $eventModel
        ->where('is_valid', 1)
        ->where('event_date >=', date('Y-m-d'))
        ->where('id !=', $id)
        ->orderBy('event_date', 'ASC')
        ->limit(6)
        ->findAll();

    return view('event_detail', [
        'event' => $event,
        'upcomingEvents' => $upcomingEvents
    ]);
}

public function yls()
{
    $committeeModel = new \App\Models\CommitteeModel();
    $eventModel     = new \App\Models\EventModel();

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
        'members'       => $members,
        'groupedEvents' => $groupedEvents
    ]);
}


public function youth()
{
    $committeeModel = new \App\Models\CommitteeModel();
    $eventModel     = new \App\Models\EventModel();

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

    // Group events by month for display
    $groupedEvents = [];
    foreach ($events as $e) {
        $month = date('F Y', strtotime($e['event_date']));
        $groupedEvents[$month][] = $e;
    }

    return view('committees/youth', [
        'members'       => $members,
        'groupedEvents' => $groupedEvents
    ]);
}

    public function sample(){    return view('sample'); }

    public function privacy(){    return view('privacy'); }

}
