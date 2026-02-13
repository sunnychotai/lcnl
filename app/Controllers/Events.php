<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\EventRegistrationModel;

class Events extends BaseController
{
    protected $eventModel;
    protected $registrationModel;

    public function __construct()
    {
        $this->eventModel = new EventModel();
        $this->registrationModel = new EventRegistrationModel();
    }

    /* ======================================================
       EVENTS LIST PAGE
    ====================================================== */

    public function index()
    {
        $events = $this->eventModel
            ->where('is_valid', 1)
            ->where('event_date >=', date('Y-m-d'))
            ->orderBy('event_date', 'ASC')
            ->findAll();

        // Group by Month-Year
        $groupedEvents = [];

        foreach ($events as $event) {
            $month = date('F Y', strtotime($event['event_date']));
            $groupedEvents[$month][] = $event;
        }

        return view('events/index', [
            'groupedEvents' => $groupedEvents
        ]);
    }

    /* ======================================================
       EVENT DETAIL PAGE
    ====================================================== */

    public function eventDetail($id)
    {
        $event = $this->eventModel
            ->where('id', $id)
            ->where('is_valid', 1)
            ->first();

        if (!$event) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Event not found');
        }

        // ----------------------------------------------------
        // DEFAULT REGISTRATION VALUES
        // ----------------------------------------------------

        $event['current_registrations'] = 0;
        $event['current_headcount'] = 0;
        $event['is_full'] = false;
        $event['registration_percent'] = null;
        $event['headcount_percent'] = null;

        // ----------------------------------------------------
        // REGISTRATION CALCULATIONS
        // ----------------------------------------------------

        if (!empty($event['requires_registration']) && !empty($event['registration_open'])) {

            $eventId = (int) $event['id'];

            $registrations = (int) $this->registrationModel
                ->getTotalRegistrationsForEventId($eventId);

            $headcount = (int) $this->registrationModel
                ->getTotalHeadcountForEventId($eventId);

            $event['current_registrations'] = $registrations;
            $event['current_headcount'] = $headcount;

            $maxRegistrations = (int) ($event['max_registrations'] ?? 0);
            $maxHeadcount = (int) ($event['max_headcount'] ?? 0);

            // Registration limit
            if ($maxRegistrations > 0 && $registrations >= $maxRegistrations) {
                $event['is_full'] = true;
            }

            // Headcount limit
            if ($maxHeadcount > 0 && $headcount >= $maxHeadcount) {
                $event['is_full'] = true;
            }

            // Percentages (for progress bars)
            if ($maxRegistrations > 0) {
                $event['registration_percent'] =
                    min(100, round(($registrations / $maxRegistrations) * 100));
            }

            if ($maxHeadcount > 0) {
                $event['headcount_percent'] =
                    min(100, round(($headcount / $maxHeadcount) * 100));
            }
        }

        // ----------------------------------------------------
        // UPCOMING EVENTS
        // ----------------------------------------------------

        $upcomingEvents = $this->eventModel
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
