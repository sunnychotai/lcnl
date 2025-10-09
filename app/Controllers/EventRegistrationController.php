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
        $this->regs   = new EventRegistrationModel();
        $this->emails = new EmailQueueModel();
    }

    /** Display the form */
    public function register()
    {
        return view('events/register');
    }

    /** Handle form submission */
   public function submit()
{
    $rules = [
        'first_name'       => 'required|min_length[2]',
        'last_name'        => 'required|min_length[2]',
        'email'            => 'required|valid_email',
        'phone'            => 'required|min_length[7]',
        'num_participants' => 'required|integer|greater_than_equal_to[1]',
        'num_guests'       => 'permit_empty|integer|greater_than_equal_to[0]',
    ];

    if (! $this->validate($rules)) {
        return redirect()->back()
            ->withInput()
            ->with('errors', $this->validator->getErrors());
    }

    $data = [
        'event_name'       => 'Chopda Pujan',
        'first_name'       => $this->request->getPost('first_name'),
        'last_name'        => $this->request->getPost('last_name'),
        'email'            => $this->request->getPost('email'),
        'phone'            => $this->request->getPost('phone'),
        'num_participants' => $this->request->getPost('num_participants'),
        'num_guests'       => $this->request->getPost('num_guests') ?? 0,
        'notes'            => $this->request->getPost('notes'),
        'status'           => 'pending',
    ];

    $this->regs->insert($data);

    // ---- Confirmation email to the registrant ----
    $subject  = 'LCNL Chopda Pujan Registration Confirmation';
    $bodyHtml = view('emails/event_registration', $data);
    $bodyText = strip_tags($bodyHtml);

    $this->emails->enqueue([
        'to_email'  => $data['email'],
        'to_name'   => trim($data['first_name'].' '.$data['last_name']),
        'subject'   => $subject,
        'body_html' => $bodyHtml,
        'body_text' => $bodyText,
        'priority'  => 1,
    ]);

    // ---- Notification email to LCNL Admin ----
    $adminSubject = 'New Chopda Pujan Registration Received';
    $adminBodyHtml = view('emails/event_registration_admin', $data);
$this->emails->enqueue([
    'to_email'  => 'info@lcnl.org',
    'to_name'   => 'LCNL Admin',
    'subject'   => 'New Chopda Pujan Registration Received',
    'body_html' => $adminBodyHtml,
    'body_text' => strip_tags($adminBodyHtml),
    'priority'  => 2,
]);


    // ---- Thank-you message & redirect ----
    session()->setFlashdata([
        'first_name' => $data['first_name'],
        'last_name'  => $data['last_name'],
    ]);

    session()->setFlashdata('message', 'Thank you, '.$data['first_name'].'! We’ve received your registration and emailed you the payment details.');

    return redirect()->to(base_url('events/register/chopda-pujan/thankyou'));
}


/** optional route for direct thank-you access */
public function thankyou()
{
    return view('events/thanks');
}

}
