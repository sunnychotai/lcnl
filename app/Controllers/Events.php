<?php

namespace App\Controllers;
use App\Models\EventModel;

class Events extends BaseController
{

    public function index()
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

        return view('events/index', [
            'groupedEvents' => $groupedEvents
        ]);
    }

    public function eventDetail($id)
    {
        $eventModel = new \App\Models\EventModel();
        $registrationModel = new \App\Models\EventRegistrationModel();

        $event = $eventModel->find($id);

        if (!$event) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Event not found');
        }

        // Default registration values
        $event['current_registrations'] = 0;
        $event['is_full'] = false;

        if (!empty($event['requires_registration'])) {

            $current = $registrationModel
                ->getTotalRegistrationsForEventId((int) $event['id']);

            $event['current_registrations'] = $current;

            if (!empty($event['capacity']) && $current >= $event['capacity']) {
                $event['is_full'] = true;
            }
        }

        // Get 6 upcoming events (excluding current one)
        $upcomingEvents = $eventModel
            ->where('is_valid', 1)
            ->where('event_date >=', date('Y-m-d'))
            ->where('id !=', $id)
            ->orderBy('event_date', 'ASC')
            ->limit(6)
            ->findAll();

        return view('events/event_detail', [
            'event' => $event,
            'upcomingEvents' => $upcomingEvents
        ]);
    }


}
