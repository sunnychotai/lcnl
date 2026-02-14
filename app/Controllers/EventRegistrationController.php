<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EventRegistrationModel;
use App\Models\EmailQueueModel;
use App\Models\EventModel;

class EventRegistrationController extends BaseController
{
    protected $regs;
    protected $emails;
    protected $eventModel;

    public function __construct()
    {
        $this->regs = new EventRegistrationModel();
        $this->emails = new EmailQueueModel();
        $this->eventModel = new EventModel();
    }

    public function register(?string $eventSlug = null)
{
    // If NO slug → show dropdown
    if ($eventSlug === null) {

        $events = $this->eventModel
            ->where('is_valid', 1)
            ->where('requires_registration', 1)
            ->where('event_date >=', date('Y-m-d'))
            ->orderBy('event_date', 'ASC')
            ->findAll();

        return view('events/register_select', [
            'events' => $events
        ]);
    }

    // Load event
    $event = $this->eventModel
        ->where('slug', $eventSlug)
        ->where('is_valid', 1)
        ->first();

    if (!$event) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    if (!$event['requires_registration']) {
        return redirect()->to('/events/' . $event['id']);
    }

    $currentTotal = $this->regs->getTotalRegistrationsForEventId($event['id']);
    $capacity = (int) $event['capacity'];
    $isFull = $capacity > 0 && $currentTotal >= $capacity;

    // 🔥 NEW: Load member data from DB (not session)
    $memberData = null;

    if (session()->get('isMemberLoggedIn')) {
        $memberModel = new \App\Models\MemberModel();
        $memberData = $memberModel->find(session()->get('member_id'));
    }

    // Anti-spam token
    $formToken = bin2hex(random_bytes(16));
    session()->set('event_form_token', $formToken);
    session()->set('event_form_token_time', time());

    return view('events/register', [
        'event'        => $event,
        'isFull'       => $isFull,
        'capacity'     => $capacity,
        'currentTotal' => $currentTotal,
        'formToken'    => $formToken,
        'isMember'     => session()->get('isMemberLoggedIn') === true,
        'memberData'   => $memberData, // ✅ pass to view
    ]);
}



    /**
     * Submit Registration
     */
    public function submit()
    {
        $eventId = (int) $this->request->getPost('event_id');
        $event = $this->eventModel->find($eventId);

        if (!$event || empty($event['requires_registration'])) {
            return redirect()->back()
                ->with('errors', ['Invalid event.']);
        }

        // ----------------------------------------
        // CAPACITY CHECKS
        // ----------------------------------------

        $maxRegistrations = (int) ($event['max_registrations'] ?? 0);
        $maxHeadcount = (int) ($event['max_headcount'] ?? 0);

        $currentRegistrations = $this->regs
            ->getTotalRegistrationsForEventId($eventId);

        $currentHeadcount = $this->regs
            ->getTotalHeadcountForEventId($eventId);

        // New submission values
        $newParticipants = 1; // Always 1 (hidden field)
        $newGuests = (int) ($this->request->getPost('num_guests') ?? 0);
        $newHeadcount = $newParticipants + $newGuests;

        // Check 1️⃣: Registration count limit
        if ($maxRegistrations > 0 && $currentRegistrations >= $maxRegistrations) {
            return redirect()->back()
                ->withInput()
                ->with('errors', [
                    'Sorry, the maximum number of registrations has been reached.'
                ]);
        }

        // Check 2️⃣: Total headcount limit
        if ($maxHeadcount > 0 && ($currentHeadcount + $newHeadcount) > $maxHeadcount) {
            return redirect()->back()
                ->withInput()
                ->with('errors', [
                    'This registration exceeds the remaining attendee capacity.'
                ]);
        }

        // ----------------------------------------
        // VALIDATION
        // ----------------------------------------

        $rules = [
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'email' => 'required|valid_email',
            'phone' => 'required|min_length[7]|max_length[20]',
            'num_guests' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[10]',
            'agreed_terms' => 'required|in_list[1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // ----------------------------------------
        // SAVE REGISTRATION
        // ----------------------------------------

        $data = [
            'event_id' => $eventId,
            'first_name' => strip_tags($this->request->getPost('first_name')),
            'last_name' => strip_tags($this->request->getPost('last_name')),
            'email' => $this->request->getPost('email'),
            'phone' => strip_tags($this->request->getPost('phone')),
            'num_participants' => 1, // Always 1
            'num_guests' => $newGuests,
            'notes' => strip_tags($this->request->getPost('notes')),
            'status' => 'submitted',
            'member_id' => session()->get('isMemberLoggedIn')
                ? (int) session()->get('member_id')
                : null,
            'ip_address' => $this->request->getIPAddress(),
        ];

        $registrationId = $this->regs->insert($data);

        // ----------------------------------------
        // SEND CONFIRMATION EMAIL
        // ----------------------------------------

        $html = view('emails/event_registration_generic', array_merge($data, [
            'event' => $event,
            'event_name' => $event['title'],
            'registration_id' => $registrationId,
            'total_people' => $newHeadcount,
        ]));


        $this->emails->enqueue([
            'to_email' => $data['email'],
            'to_name' => trim($data['first_name'] . ' ' . $data['last_name']),
            'subject' => $event['title'] . ' – Registration Received',
            'body_html' => $html,
            'body_text' => strip_tags($html),
            'priority' => 1,
        ]);

        // ----------------------------------------
        // THANK YOU PAGE DATA
        // ----------------------------------------

        session()->setFlashdata([
            'first_name' => $data['first_name'],
            'event_name' => $event['title'],
        ]);

        return redirect()->to(site_url('events/thanks'));
    }


    public function thankyou()
    {
        return view('events/thanks');
    }
}
