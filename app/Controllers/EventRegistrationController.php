<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EventRegistrationModel;
use App\Models\EmailQueueModel;

class EventRegistrationController extends BaseController
{
    protected $regs;
    protected $emails;

    public function __construct()
    {
        $this->regs = new EventRegistrationModel();
        $this->emails = new EmailQueueModel();
    }

    /**
     * Show registration form
     * Supports optional event slug e.g. /events/register/chopda-pujan
     */
    public function register(?string $eventSlug = null)
    {
        // Map slug → display name (DB later)
        $eventMap = [
            'chopda-pujan' => 'Chopda Pujan 2025',
            'new-year-bhajans' => 'New Year Bhajans 2026',
            'navratri-garba' => 'Navratri Garba 2025',
        ];

        $selectedEvent = ($eventSlug && isset($eventMap[$eventSlug]))
            ? $eventMap[$eventSlug]
            : null;

        return view('events/register', [
            'selectedEvent' => $selectedEvent,
            'isMember' => session()->get('isMemberLoggedIn') === true,
            'memberName' => session()->get('member_name'),
            'memberEmail' => session()->get('member_email'),
        ]);
    }

    /**
     * Handle form submission
     */
    public function submit()
    {
        $rules = [
            'event_name' => 'required|min_length[3]',
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'email' => 'required|valid_email',
            'phone' => 'required|min_length[7]',
            'num_participants' => 'required|integer|greater_than_equal_to[1]',
            'num_guests' => 'permit_empty|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $isMember = session()->get('isMemberLoggedIn') === true;

        $data = [
            'event_name' => $this->request->getPost('event_name'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'num_participants' => (int) $this->request->getPost('num_participants'),
            'num_guests' => (int) ($this->request->getPost('num_guests') ?? 0),
            'notes' => $this->request->getPost('notes'),
            'status' => 'submitted',
            'member_id' => $isMember ? (int) session()->get('member_id') : null,
        ];

        // Persist registration
        $this->regs->insert($data);

        /* -------------------------------------------------
         * Member confirmation email (generic + branded)
         * ------------------------------------------------- */
        $memberEmailHtml = view('emails/event_registration_generic', array_merge($data, [
            'title' => 'LCNL Event Registration Received',
        ]));

        $this->emails->enqueue([
            'to_email' => $data['email'],
            'to_name' => trim($data['first_name'] . ' ' . $data['last_name']),
            'subject' => 'LCNL Event Registration Received',
            'body_html' => $memberEmailHtml,
            'body_text' => strip_tags($memberEmailHtml),
            'priority' => 1,
        ]);

        /* -------------------------------------------------
         * Admin notification email
         * ------------------------------------------------- */
        $adminEmailHtml = view('emails/event_registration_admin', array_merge($data, [
            'title' => 'New Event Registration – LCNL',
        ]));

        $this->emails->enqueue([
            'to_email' => 'info@lcnl.co.uk',
            'to_name' => 'LCNL Admin',
            'subject' => 'New Event Registration Submitted',
            'body_html' => $adminEmailHtml,
            'body_text' => strip_tags($adminEmailHtml),
            'priority' => 2,
        ]);

        // Flash data for thank-you page
        session()->setFlashdata([
            'first_name' => $data['first_name'],
            'event_name' => $data['event_name'],
        ]);

        return redirect()->to(site_url('events/thanks'));
    }

    /**
     * Thank-you page
     */
    public function thankyou()
    {
        return view('events/thanks');
    }
}
