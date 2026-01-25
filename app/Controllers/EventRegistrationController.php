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
            'maha-shivratri' => 'Maha Shivratri 2026',
        ];

        $selectedEvent = ($eventSlug && isset($eventMap[$eventSlug]))
            ? $eventMap[$eventSlug]
            : null;

        // Generate unique form token for this session
        $formToken = bin2hex(random_bytes(16));
        session()->set('event_form_token', $formToken);
        session()->set('event_form_token_time', time());

        return view('events/register', [
            'selectedEvent' => $selectedEvent,
            'isMember' => session()->get('isMemberLoggedIn') === true,
            'memberName' => session()->get('member_name'),
            'memberEmail' => session()->get('member_email'),
            'formToken' => $formToken,
        ]);
    }

    /**
     * Handle form submission with comprehensive anti-spam measures
     */
    public function submit()
{
    // ========================================
    // ANTI-SPAM LAYER 1: Honeypot Detection
    // ========================================
    $website = $this->request->getPost('website');
    $company = $this->request->getPost('company');

    if (!empty($website) || !empty($company)) {
        log_message('warning', 'Spam detected (honeypot): ' . $this->request->getIPAddress());
        return redirect()->to(site_url('events/thanks'));
    }

    // ========================================
    // ANTI-SPAM LAYER 2: Rate Limiting by IP
    // ========================================
    $ip = $this->request->getIPAddress();
    $rateLimitKey = 'event_registration_' . md5($ip);
    $recentSubmissions = cache($rateLimitKey) ?? [];

    $recentSubmissions = array_filter($recentSubmissions, function ($time) {
        return (time() - $time) < 3600;
    });

    if (count($recentSubmissions) >= 3) {
        return redirect()->back()
            ->withInput()
            ->with('errors', ['You have submitted too many registrations. Please try again later.']);
    }

    // ========================================
    // ANTI-SPAM LAYER 3: Form Token Validation
    // ========================================
    $submittedToken = $this->request->getPost('form_token');
    $sessionToken   = session()->get('event_form_token');
    $tokenTime      = session()->get('event_form_token_time');

    if (!$submittedToken || $submittedToken !== $sessionToken) {
        return redirect()->back()
            ->withInput()
            ->with('errors', ['Invalid form submission. Please refresh the page and try again.']);
    }

    session()->remove('event_form_token');
    session()->remove('event_form_token_time');

    // ========================================
    // ANTI-SPAM LAYER 4: Time-based Validation
    // ========================================
    $formTime    = (int) $this->request->getPost('form_time');
    $currentTime = time();
    $timeTaken   = $currentTime - $formTime;

    if ($timeTaken < 3) {
        return redirect()->back()
            ->withInput()
            ->with('errors', ['Please take your time to fill out the form carefully.']);
    }

    if ($tokenTime && ($currentTime - $tokenTime) < 2) {
        return redirect()->back()
            ->withInput()
            ->with('errors', ['Form submitted too quickly. Please try again.']);
    }

    if ($tokenTime && ($currentTime - $tokenTime) > 7200) {
        return redirect()->back()
            ->withInput()
            ->with('errors', ['Your session has expired. Please refresh the page and submit again.']);
    }

    // ========================================
    // ANTI-SPAM LAYER 5: Human Confirmation
    // ========================================
    $humanConfirm = $this->request->getPost('human_confirm');
    if (!$humanConfirm || $humanConfirm !== 'on') {
        return redirect()->back()
            ->withInput()
            ->with('errors', ['Please confirm that you are a real person.']);
    }

    // ========================================
    // STANDARD VALIDATION
    // ========================================
    $rules = [
        'event_name'       => 'required|min_length[3]',
        'first_name'       => 'required|min_length[2]|max_length[50]',
        'last_name'        => 'required|min_length[2]|max_length[50]',
        'email'            => 'required|valid_email',
        'phone'            => 'required|min_length[7]|max_length[20]',
        'num_participants' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[2]',
        'num_guests'       => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[10]',
        'notes'            => 'permit_empty|max_length[500]',
        'is_lcnl_member'   => 'required|in_list[0,1]',
        'agreed_terms'     => 'required|in_list[1]',
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()
            ->withInput()
            ->with('errors', $this->validator->getErrors());
    }

    // ========================================
    // EVENT CAPACITY CHECK (MAX 90 PARTICIPANTS)
    // ========================================
    $eventName       = $this->request->getPost('event_name');
    $newParticipants = (int) $this->request->getPost('num_participants');
    $MAX_PARTICIPANTS = 90;

    $currentTotal = (int) $this->regs
        ->selectSum('num_participants')
        ->where('event_name', $eventName)
        ->whereIn('status', ['submitted', 'confirmed'])
        ->get()
        ->getRow()
        ->num_participants ?? 0;

    if (($currentTotal + $newParticipants) > $MAX_PARTICIPANTS) {
        log_message('warning', sprintf(
            'Capacity exceeded for %s. Current=%d, Attempted=%d',
            $eventName,
            $currentTotal,
            $newParticipants
        ));

        return redirect()->back()
            ->withInput()
            ->with('errors', [
                'Sorry, this event is now fully booked and no further registrations can be accepted.'
            ]);
    }

    // ========================================
    // SAVE REGISTRATION
    // ========================================
    $isMember = session()->get('isMemberLoggedIn') === true;

    $data = [
        'event_name'       => $eventName,
        'first_name'       => strip_tags($this->request->getPost('first_name')),
        'last_name'        => strip_tags($this->request->getPost('last_name')),
        'email'            => $this->request->getPost('email'),
        'phone'            => strip_tags($this->request->getPost('phone')),
        'num_participants' => $newParticipants,
        'num_guests'       => (int) ($this->request->getPost('num_guests') ?? 0),
        'notes'            => strip_tags($this->request->getPost('notes')),
        'status'           => 'submitted',
        'member_id'        => $isMember ? (int) session()->get('member_id') : null,
        'is_lcnl_member'   => (int) $this->request->getPost('is_lcnl_member'),
        'agreed_terms'     => 1,
        'ip_address'       => $ip,
    ];

    $registrationId = $this->regs->insert($data);

    $recentSubmissions[] = time();
    cache()->save($rateLimitKey, $recentSubmissions, 3600);

    // ========================================
    // SEND EMAILS
    // ========================================
    $memberEmailHtml = view('emails/event_registration_generic', array_merge($data, [
        'registration_id' => $registrationId,
    ]));

    $this->emails->enqueue([
        'to_email'  => $data['email'],
        'to_name'   => trim($data['first_name'] . ' ' . $data['last_name']),
        'subject'   => 'LCNL Event Registration Received',
        'body_html' => $memberEmailHtml,
        'body_text' => strip_tags($memberEmailHtml),
        'priority'  => 1,
    ]);

    $adminEmailHtml = view('emails/event_registration_admin', array_merge($data, [
        'registration_id' => $registrationId,
        'submission_time' => date('d/m/Y H:i:s'),
        'time_taken'      => $timeTaken . ' seconds',
    ]));

    $this->emails->enqueue([
        'to_email'  => 'info@lcnl.co.uk',
        'to_name'   => 'LCNL Admin',
        'subject'   => 'New Event Registration Submitted',
        'body_html' => $adminEmailHtml,
        'body_text' => strip_tags($adminEmailHtml),
        'priority'  => 2,
    ]);

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
