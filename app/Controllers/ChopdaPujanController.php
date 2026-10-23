<?php

namespace App\Controllers;

use App\Models\ChopdaRegistrationModel;
use App\Models\EmailQueueModel;
use Config\ChopdaPujan;

/**
 * Chopda Pujan 2026 registration.
 *
 * One registration is one pooja. Nothing is charged here: the registrant is
 * shown LCNL's bank details and payment is matched later in the admin screen.
 */
class ChopdaPujanController extends BaseController
{
    /** Session key holding the registration the current visitor has just made. */
    private const SESSION_KEY = 'chopda_registration_id';

    protected ChopdaPujan $config;
    protected ChopdaRegistrationModel $registrations;
    protected EmailQueueModel $emails;

    public function __construct()
    {
        $this->config        = config('ChopdaPujan');
        $this->registrations = new ChopdaRegistrationModel();
        $this->emails        = new EmailQueueModel();
    }

    public function index()
    {
        return view('chopda/index', $this->pageData());
    }

    public function submit()
    {
        if (! $this->isOpen()) {
            return redirect()->to($this->url())->with('error', $this->closedMessage());
        }

        $post = $this->request->getPost();

        // Honeypot: hidden from people, so anything in it is a bot. Answer as
        // though it worked rather than telling a script what tripped it.
        if (trim((string) ($post['website'] ?? '')) !== '') {
            log_message('info', 'Chopda: honeypot triggered from {ip}', ['ip' => $this->request->getIPAddress()]);

            return redirect()->to($this->url())->with('error',
                'Thank you — if your registration was received you will get a confirmation email shortly.');
        }

        // Nobody fills this form in under three seconds.
        $startedAt = (int) ($post['form_time'] ?? 0);
        if ($startedAt === 0 || (time() - $startedAt) < 3) {
            return redirect()->back()->withInput()
                ->with('errors', ['Submission rejected. Please try again.']);
        }

        if (service('throttler')->check('chopda-' . md5($this->request->getIPAddress()), 3, MINUTE) === false) {
            return redirect()->back()->withInput()
                ->with('errors', ['Too many submissions. Please wait a minute and try again.']);
        }

        $rules = [
            'first_name'        => 'required|trim|max_length[100]',
            'last_name'         => 'required|trim|max_length[100]',
            'email'             => 'required|trim|valid_email|max_length[255]',
            'phone'             => 'required|trim|min_length[7]|max_length[30]',
            'num_attendees'     => 'required|is_natural_no_zero|less_than_equal_to[' . $this->config->maxAttendees . ']',
            'payment_reference' => 'required|trim|max_length[' . $this->config->paymentReferenceMaxLength . ']',
            'payment_timing'    => 'required|in_list[paid,will_pay]',
            'privacy_consent'   => 'required',
            'comments'          => 'permit_empty|trim|max_length[2000]',
        ];

        $messages = [
            'first_name'        => ['required' => 'Please enter your first name.'],
            'last_name'         => ['required' => 'Please enter your last name.'],
            'num_attendees'     => [
                'required'             => 'Please tell us how many people are attending.',
                'is_natural_no_zero'   => 'Please tell us how many people are attending.',
                'less_than_equal_to'   => 'For more than ' . $this->config->maxAttendees . ' people, please contact the organisers.',
            ],
            'payment_reference' => [
                'required'   => 'Please tell us the reference you will use for your bank transfer.',
                'max_length' => 'Bank references can be at most ' . $this->config->paymentReferenceMaxLength . ' characters.',
            ],
            'payment_timing'    => ['required' => 'Please tell us whether you have already made the transfer.'],
            'privacy_consent'   => ['required' => 'Please confirm you are happy for us to hold your details.'],
        ];

        $valid  = $this->validate($rules, $messages);
        $errors = $valid ? [] : $this->validator->getErrors();

        $reference = trim(preg_replace('/\s+/', ' ', (string) ($post['payment_reference'] ?? '')));
        if (! isset($errors['payment_reference']) && ! preg_match('/^[A-Za-z0-9 \-]+$/', $reference)) {
            $errors['payment_reference'] = 'Please use only letters, numbers, spaces and hyphens in your payment reference.';
        }

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        if ($this->isFull()) {
            return redirect()->to($this->url())->with('error', $this->closedMessage());
        }

        $id = $this->registrations->insert([
            'registration_ref'  => $this->registrations->generateRef(),
            'first_name'        => $post['first_name'],
            'last_name'         => $post['last_name'],
            'email'             => $post['email'],
            'phone'             => $post['phone'],
            'num_attendees'     => (int) $post['num_attendees'],
            'payment_reference' => strtoupper($reference),
            'payment_timing'    => $post['payment_timing'],
            'amount'            => $this->config->fee,
            'privacy_consent'   => 1,
            'comments'          => ($post['comments'] ?? '') ?: null,
            'status'            => 'registered',
            'ip_address'        => $this->request->getIPAddress(),
        ], true);

        $registration = $this->registrations->find($id);

        $this->sendConfirmation($registration);
        $this->notifyOrganisers($registration);

        // The thank-you page is tied to this browser session rather than to a
        // reference in the URL, so nobody can browse to someone else's booking.
        session()->set(self::SESSION_KEY, (int) $id);

        return redirect()->to($this->url('thank-you'));
    }

    public function thankYou()
    {
        $id           = (int) session()->get(self::SESSION_KEY);
        $registration = $id > 0 ? $this->registrations->find($id) : null;

        if (! $registration) {
            return redirect()->to($this->url());
        }

        return view('chopda/thank_you', $this->pageData([
            'title'        => 'Thank you for registering – Chopda Pujan 2026',
            'noindex'      => true,
            'registration' => $registration,
        ]));
    }

    /* =====================================================================
       Email
    ===================================================================== */

    private function sendConfirmation(array $registration): void
    {
        $html = view('emails/chopda_registration_confirmation', [
            'registration' => $registration,
            'config'       => $this->config,
        ]);

        $this->emails->enqueue([
            'to_email'   => $registration['email'],
            'to_name'    => $registration['first_name'] . ' ' . $registration['last_name'],
            'subject'    => 'Chopda Pujan 2026 – please complete your payment',
            'type'       => 'chopda_registration_confirmation',
            'related_id' => $registration['id'],
            'body_html'  => $html,
            'body_text'  => html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'priority'   => 1,
        ]);
    }

    private function notifyOrganisers(array $registration): void
    {
        if ($this->config->notifyEmails === []) {
            log_message('warning',
                'Chopda: registration {ref} saved but chopda.notifyEmails is empty, so nobody was notified.',
                ['ref' => $registration['registration_ref']]
            );

            return;
        }

        $html = view('emails/chopda_registration_notification', [
            'registration' => $registration,
            'config'       => $this->config,
        ]);

        foreach ($this->config->notifyEmails as $email) {
            $this->emails->enqueue([
                'to_email'   => $email,
                'to_name'    => 'LCNL',
                'subject'    => 'New Chopda Pujan registration – ' . $registration['first_name'] . ' ' . $registration['last_name'],
                'type'       => 'chopda_registration_notification',
                'related_id' => $registration['id'],
                'body_html'  => $html,
                'body_text'  => html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'priority'   => 1,
            ]);
        }
    }

    /* =====================================================================
       Helpers
    ===================================================================== */

    private function url(string $suffix = ''): string
    {
        return site_url($this->config->slug . ($suffix !== '' ? '/' . $suffix : ''));
    }

    private function isOpen(): bool
    {
        return $this->config->isOpen() && ! $this->isFull();
    }

    private function isFull(): bool
    {
        return $this->config->capacity > 0
            && $this->registrations->countActive() >= $this->config->capacity;
    }

    private function closedMessage(): string
    {
        return 'Online registration for Chopda Pujan has now closed. To ask about a place, please call '
            . implode(' or ', array_map(
                static fn(array $c): string => $c['name'] . ' on ' . $c['phone'],
                $this->config->contacts
            )) . '.';
    }

    private function pageData(array $extra = []): array
    {
        return array_merge([
            'title'           => 'Chopda Pujan 2026 – ' . $this->config->eventLabel(),
            'metaDescription' => 'Join LCNL for Chopda Pujan on ' . $this->config->eventLabel() . ' at the Dhamecha Lohana Centre. '
                . 'Register online: £' . $this->config->fee . ' nyochavar per pooja.',
            'config'          => $this->config,
            'isOpen'          => $this->isOpen(),
            'closedMessage'   => $this->closedMessage(),
        ], $extra);
    }
}
