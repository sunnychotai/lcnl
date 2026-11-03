<?php

namespace App\Controllers;

use App\Models\ChopdaRegistrationModel;
use App\Models\EmailQueueModel;
use App\Services\ChopdaPaymentService;
use Config\ChopdaPujan;

/**
 * Chopda Pujan 2026 registration.
 *
 * One registration is one pooja. Card registrants go straight on to Stripe
 * Checkout; bank transfer registrants are shown LCNL's bank details and their
 * payment is matched later in the admin screen.
 */
class ChopdaPujanController extends BaseController
{
    /** Session key holding the registration the current visitor has just made. */
    private const SESSION_KEY = 'chopda_registration_id';

    protected ChopdaPujan $config;
    protected ChopdaRegistrationModel $registrations;
    protected EmailQueueModel $emails;
    protected ChopdaPaymentService $payments;

    public function __construct()
    {
        $this->config        = config('ChopdaPujan');
        $this->registrations = new ChopdaRegistrationModel();
        $this->emails        = new EmailQueueModel();
        $this->payments      = new ChopdaPaymentService();
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

        // With one method on offer the form does not ask.
        $methods = $this->config->paymentMethods();
        $method  = count($methods) === 1 ? $methods[0] : (string) ($post['payment_method'] ?? '');

        $rules = [
            'first_name'        => 'required|trim|max_length[100]',
            'last_name'         => 'required|trim|max_length[100]',
            'email'             => 'required|trim|valid_email|max_length[255]',
            'phone'             => 'required|trim|min_length[7]|max_length[30]',
            'num_attendees'     => 'required|is_natural_no_zero|less_than_equal_to[' . $this->config->maxAttendees . ']',
            'privacy_consent'   => 'required',
            'comments'          => 'permit_empty|trim|max_length[2000]',
        ];

        // The reference and "already paid?" questions are for bank transfers only.
        if ($method === 'bank') {
            $rules += [
                'payment_reference' => 'required|trim|max_length[' . $this->config->paymentReferenceMaxLength . ']',
                'payment_timing'    => 'required|in_list[paid,will_pay]',
            ];
        }

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

        if (! in_array($method, $methods, true)) {
            $errors['payment_method'] = 'Please choose how you would like to pay.';
        }

        $reference = trim(preg_replace('/\s+/', ' ', (string) ($post['payment_reference'] ?? '')));
        if ($method === 'bank' && ! isset($errors['payment_reference']) && ! preg_match('/^[A-Za-z0-9 \-]+$/', $reference)) {
            $errors['payment_reference'] = 'Please use only letters, numbers, spaces and hyphens in your payment reference.';
        }

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        if ($this->isFull()) {
            return redirect()->to($this->url())->with('error', $this->closedMessage());
        }

        $ref = $this->registrations->generateRef();

        $id = $this->registrations->insert([
            'registration_ref'  => $ref,
            'first_name'        => $post['first_name'],
            'last_name'         => $post['last_name'],
            'email'             => $post['email'],
            'phone'             => $post['phone'],
            'num_attendees'     => (int) $post['num_attendees'],
            // Card payers never type a reference; the registration ref stands in
            // and still works if they switch to a bank transfer.
            'payment_reference' => $method === 'bank' ? strtoupper($reference) : $ref,
            'payment_timing'    => $method === 'bank' ? $post['payment_timing'] : '',
            'payment_method'    => $method,
            'pay_token'         => $this->registrations->generatePayToken(),
            'amount'            => $this->config->fee,
            'privacy_consent'   => 1,
            'comments'          => ($post['comments'] ?? '') ?: null,
            'status'            => 'registered',
            'ip_address'        => $this->request->getIPAddress(),
        ], true);

        $registration = $this->registrations->find($id);

        // A card payer is emailed once the payment completes, or with a link to
        // finish it if they abandon it; a "please pay" email now would cross
        // with the confirmation.
        if ($method === 'bank') {
            $this->payments->email($registration, 'chopda_registration_confirmation',
                'Chopda Pujan 2026 – please complete your payment');
        }
        $this->notifyOrganisers($registration);

        // The thank-you page is tied to this browser session rather than to a
        // reference in the URL, so nobody can browse to someone else's booking.
        session()->set(self::SESSION_KEY, (int) $id);

        return $method === 'card'
            ? $this->toCheckout($registration)
            : redirect()->to($this->url('thank-you'));
    }

    /** The pay-by-card link from an email or the thank-you page. */
    public function pay(string $token)
    {
        $registration = preg_match('/^[0-9a-f]{48}$/', $token)
            ? $this->registrations->where('pay_token', $token)->first()
            : null;

        if (! $registration || $registration['status'] === 'cancelled') {
            return redirect()->to($this->url())->with('error',
                'That payment link is no longer valid. Please contact us if you need help with your registration.');
        }

        session()->set(self::SESSION_KEY, (int) $registration['id']);

        if (! $this->config->acceptsCard() || ! $this->config->isUpcoming()) {
            return redirect()->to($this->url('thank-you'));
        }

        return $this->toCheckout($registration);
    }

    /** Stripe's success_url. Settles the payment now in case the webhook is slow. */
    public function paid()
    {
        $sessionId = (string) $this->request->getGet('session_id');

        if (str_starts_with($sessionId, 'cs_')) {
            try {
                $registration = $this->payments->fulfilById($sessionId);

                if ($registration) {
                    session()->set(self::SESSION_KEY, (int) $registration['id']);
                }
            } catch (\Throwable $e) {
                log_message('error', 'Chopda: could not check checkout {id} on return: {msg}', [
                    'id' => $sessionId, 'msg' => $e->getMessage(),
                ]);
            }
        }

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
            'payUrl'       => $registration['status'] === 'cancelled' ? null : $this->config->payUrl($registration),
        ]));
    }

    /* =====================================================================
       Payment and email
    ===================================================================== */

    private function toCheckout(array $registration)
    {
        try {
            $url = $this->payments->checkoutUrl($registration);
        } catch (\Throwable $e) {
            log_message('error', 'Chopda: could not open Stripe Checkout for {ref}: {msg}', [
                'ref' => $registration['registration_ref'], 'msg' => $e->getMessage(),
            ]);

            return redirect()->to($this->url('thank-you'))->with('error',
                'Sorry, we couldn’t open the secure card payment page just now. You have not been charged. '
                . 'Please try again in a few minutes'
                . ($this->config->acceptsBank() ? ', or pay by bank transfer using the details below.' : '.'));
        }

        return redirect()->to($url ?? $this->url('thank-you'));
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
            'methods'         => $this->config->paymentMethods(),
            'config'          => $this->config,
            'isOpen'          => $this->isOpen(),
            'closedMessage'   => $this->closedMessage(),
        ], $extra);
    }
}
