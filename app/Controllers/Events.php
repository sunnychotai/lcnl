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
            'title'           => 'Events',
            'metaDescription' => 'Upcoming events, festivals and celebrations at Lohana Community North London.',
            'groupedEvents'   => $groupedEvents
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

        // Share card: use the event's own image where one exists, else the site default
        $ogImage = null;
        if (!empty($event['image']) && is_file(FCPATH . $event['image'])) {
            $ogImage = $event['image'];
        }

        return view('events/event_detail', [
            'title'           => $event['title'],
            'metaDescription' => $this->shareDescription($event),
            'ogImage'         => $ogImage,
            'event'           => $event,
            'upcomingEvents'  => $upcomingEvents
        ]);
    }

    /**
     * Build the share/meta description for an event: the first ~200 characters of
     * its description as a single line, falling back to the date if it has none.
     */
    private function shareDescription(array $event): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string) ($event['description'] ?? ''))));

        if ($text === '') {
            return 'LCNL event on ' . date('j F Y', strtotime($event['event_date']));
        }

        if (mb_strlen($text) <= 200) {
            return $text;
        }

        $cut = mb_substr($text, 0, 200);
        $lastSpace = mb_strrpos($cut, ' ');

        return ($lastSpace !== false ? mb_substr($cut, 0, $lastSpace) : $cut) . '…';
    }
}
