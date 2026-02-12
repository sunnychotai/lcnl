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

    /**
     * Show registration form
     */
    public function register(?string $eventSlug = null)
    {
        // If NO slug → show dropdown of events requiring registration
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

        // If slug exists → load specific event
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

        $formToken = bin2hex(random_bytes(16));
        session()->set('event_form_token', $formToken);
        session()->set('event_form_token_time', time());

        return view('events/register', [
            'event' => $event,
            'isFull' => $isFull,
            'capacity' => $capacity,
            'currentTotal' => $currentTotal,
            'formToken' => $formToken,
            'isMember' => session()->get('isMemberLoggedIn') === true,
        ]);
    }


    /**
     * Submit Registration
     */
    public function submit()
    {
        $eventId = (int) $this->request->getPost('event_id');

        $event = $this->eventModel->find($eventId);

        if (!$event || !$event['requires_registration']) {
            return redirect()->back()
                ->with('errors', ['Invalid event.']);
        }

        // Capacity check
        $capacity = (int) $event['capacity'];
        $currentTotal = $this->regs->getTotalRegistrationsForEventId($eventId);

        if ($capacity > 0 && $currentTotal >= $capacity) {
            return redirect()->back()
                ->withInput()
                ->with('errors', [
                    'Sorry, this event has reached maximum capacity.'
                ]);
        }

        // Validation
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'email' => 'required|valid_email',
            'phone' => 'required|min_length[7]|max_length[20]',
            'num_participants' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[2]',
            'num_guests' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[10]',
            'agreed_terms' => 'required|in_list[1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'event_id' => $eventId,
            'first_name' => strip_tags($this->request->getPost('first_name')),
            'last_name' => strip_tags($this->request->getPost('last_name')),
            'email' => $this->request->getPost('email'),
            'phone' => strip_tags($this->request->getPost('phone')),
            'num_participants' => (int) $this->request->getPost('num_participants'),
            'num_guests' => (int) $this->request->getPost('num_guests'),
            'notes' => strip_tags($this->request->getPost('notes')),
            'status' => 'submitted',
            'member_id' => session()->get('isMemberLoggedIn')
                ? (int) session()->get('member_id')
                : null,
            'ip_address' => $this->request->getIPAddress(),
        ];

        $registrationId = $this->regs->insert($data);

        // Emails
        $html = view('emails/event_registration_generic', [
            'event' => $event,
            'registration_id' => $registrationId,
        ]);

        $this->emails->enqueue([
            'to_email' => $data['email'],
            'to_name' => trim($data['first_name'] . ' ' . $data['last_name']),
            'subject' => $event['title'] . ' – Registration Received',
            'body_html' => $html,
            'body_text' => strip_tags($html),
            'priority' => 1,
        ]);

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
