<?php

namespace App\Controllers;

use App\Models\ConnexionsAttendeeModel;
use App\Models\ConnexionsBookingModel;
use App\Services\ConnexionsBookingService;
use Config\Connexions;

/**
 * Connexions – Singles Mix & Mingle booking.
 *
 * The booker picks girl and boy tickets, names everyone, and goes straight to
 * Stripe Checkout. Places are held while they pay. Once paid, the booker and
 * every attendee are emailed a confirmation to bring on the night.
 */
class ConnexionsController extends BaseController
{
    /** Session key holding the booking the current visitor has just made. */
    private const SESSION_KEY = 'connexions_booking_id';

    /** Characters allowed in phone numbers. */
    private const PHONE = 'regex_match[/^[0-9 +()\-]+$/]';

    protected Connexions $config;
    protected ConnexionsBookingModel $bookings;
    protected ConnexionsAttendeeModel $attendees;
    protected ConnexionsBookingService $service;

    public function __construct()
    {
        $this->config    = config('Connexions');
        $this->bookings  = new ConnexionsBookingModel();
        $this->attendees = new ConnexionsAttendeeModel();
        $this->service   = new ConnexionsBookingService();
    }

    public function index()
    {
        return view('connexions/index', $this->pageData());
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
            log_message('info', 'Connexions: honeypot triggered from {ip}', ['ip' => $this->request->getIPAddress()]);

            return redirect()->to($this->url())->with('error',
                'Thank you — if your booking was received you will get a confirmation email shortly.');
        }

        // Nobody fills this form in under three seconds.
        $startedAt = (int) ($post['form_time'] ?? 0);
        if ($startedAt === 0 || (time() - $startedAt) < 3) {
            return redirect()->back()->withInput()
                ->with('errors', ['Submission rejected. Please try again.']);
        }

        if (service('throttler')->check('connexions-' . md5($this->request->getIPAddress()), 3, MINUTE) === false) {
            return redirect()->back()->withInput()
                ->with('errors', ['Too many submissions. Please wait a minute and try again.']);
        }

        $max    = $this->config->maxPerGender;
        $counts = [
            'girl' => (int) ($post['girl_tickets'] ?? 0),
            'boy'  => (int) ($post['boy_tickets'] ?? 0),
        ];

        $rules = [
            'first_name'      => ['rules' => 'required|max_length[100]', 'errors' => ['required' => 'Please enter your first name.']],
            'last_name'       => ['rules' => 'required|max_length[100]', 'errors' => ['required' => 'Please enter your surname.']],
            'email'           => ['rules' => 'required|valid_email|max_length[255]', 'errors' => [
                'required' => 'Please enter your email address.', 'valid_email' => 'Please enter a valid email address.',
            ]],
            'phone'           => ['rules' => 'required|min_length[7]|max_length[30]|' . self::PHONE, 'errors' => [
                'required' => 'Please enter your mobile number.', 'min_length' => 'Please enter a valid mobile number.',
                self::ruleName(self::PHONE) => 'Please enter a valid mobile number.',
            ]],
            'girl_tickets'    => ['rules' => 'required|is_natural|less_than_equal_to[' . $max . ']', 'errors' => [
                'less_than_equal_to' => 'Please choose up to ' . $max . ' girl tickets.',
            ]],
            'boy_tickets'     => ['rules' => 'required|is_natural|less_than_equal_to[' . $max . ']', 'errors' => [
                'less_than_equal_to' => 'Please choose up to ' . $max . ' boy tickets.',
            ]],
            'eligible'        => ['rules' => 'required', 'errors' => [
                'required' => 'Please confirm that everyone you are booking for is one of the ' . $this->config->eligibility . '.',
            ]],
            'privacy_consent' => ['rules' => 'required', 'errors' => [
                'required' => 'Please confirm you are happy for us to hold these details.',
            ]],
        ];

        // One set of details per ticket. Boxes beyond the number of tickets
        // chosen are ignored, whatever is in them.
        foreach ($counts as $gender => $n) {
            for ($i = 1; $i <= min($n, $max); $i++) {
                $who = $this->config->genders[$gender]['one'] . ' ' . $i;
                $key = 'attendees.' . $gender . '.' . $i . '.';

                $rules[$key . 'first_name'] = ['rules' => 'required|max_length[100]', 'errors' => [
                    'required' => $who . ': please enter their first name.', 'max_length' => $who . ': first name is too long.',
                ]];
                $rules[$key . 'last_name'] = ['rules' => 'required|max_length[100]', 'errors' => [
                    'required' => $who . ': please enter their surname.', 'max_length' => $who . ': surname is too long.',
                ]];
                $rules[$key . 'email'] = ['rules' => 'required|valid_email|max_length[255]', 'errors' => [
                    'required'    => $who . ': please enter their email address.',
                    'valid_email' => $who . ': please enter a valid email address.',
                    'max_length'  => $who . ': email address is too long.',
                ]];
                $rules[$key . 'phone'] = ['rules' => 'required|min_length[7]|max_length[30]|' . self::PHONE, 'errors' => [
                    'required'                  => $who . ': please enter their mobile number.',
                    'min_length'                => $who . ': please enter a valid mobile number.',
                    'max_length'                => $who . ': please enter a valid mobile number.',
                    self::ruleName(self::PHONE) => $who . ': please enter a valid mobile number.',
                ]];
            }
        }

        $valid  = $this->validate($rules);
        $errors = $valid ? [] : $this->validator->getErrors();

        if (! isset($errors['girl_tickets']) && ! isset($errors['boy_tickets']) && $counts['girl'] + $counts['boy'] === 0) {
            $errors['tickets'] = 'Please choose at least one ticket.';
        }

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $left = $this->service->placesLeft();
        if ($problem = $this->service->shortfall($counts['girl'], $counts['boy'], $left)) {
            return $this->service->isFull()
                ? redirect()->to($this->url())->with('error', $this->closedMessage())
                : redirect()->back()->withInput()->with('errors', ['tickets' => $problem . ' Please choose fewer tickets.']);
        }

        $clean  = static fn($v): string => trim(preg_replace('/\s+/', ' ', (string) $v));
        $tickets = $counts['girl'] + $counts['boy'];
        $db      = db_connect();

        $db->transStart();

        $id = $this->bookings->insert([
            'booking_ref'           => $this->bookings->generateRef($this->config->referencePrefix),
            'first_name'            => $clean($post['first_name']),
            'last_name'             => $clean($post['last_name']),
            'email'                 => $clean($post['email']),
            'phone'                 => $clean($post['phone']),
            'is_member'             => empty($post['is_member']) ? 0 : 1,
            'eligibility_confirmed' => 1,
            'privacy_consent'       => 1,
            'girl_tickets'          => $counts['girl'],
            'boy_tickets'           => $counts['boy'],
            'ticket_price'          => $this->config->ticketPrice,
            'amount'                => $tickets * $this->config->ticketPrice,
            'pay_token'             => $this->bookings->generatePayToken(),
            'hold_until'            => date('Y-m-d H:i:s', time() + $this->config->holdMinutes * MINUTE),
            'status'                => 'pending',
            'ip_address'            => $this->request->getIPAddress(),
        ], true);

        foreach ($counts as $gender => $n) {
            for ($i = 1; $i <= $n; $i++) {
                $a = $post['attendees'][$gender][$i] ?? [];

                $this->attendees->insert([
                    'booking_id' => $id,
                    'gender'     => $gender,
                    'position'   => $i,
                    'first_name' => $clean($a['first_name'] ?? ''),
                    'last_name'  => $clean($a['last_name'] ?? ''),
                    'email'      => $clean($a['email'] ?? ''),
                    'phone'      => $clean($a['phone'] ?? ''),
                ]);
            }
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            log_message('error', 'Connexions: could not save a booking for {email}', ['email' => $post['email']]);

            return redirect()->back()->withInput()
                ->with('errors', ['Sorry, something went wrong saving your booking. You have not been charged. Please try again.']);
        }

        $booking = $this->bookings->find($id);

        // Someone else took the last places while this was being saved.
        if ($this->service->overbooked($booking)) {
            $this->attendees->where('booking_id', $id)->delete();
            $this->bookings->delete($id, true);

            return redirect()->back()->withInput()->with('errors', ['tickets' => ($this->service->shortfall(
                $counts['girl'], $counts['boy'], $this->service->placesLeft()
            ) ?? 'Sorry, those places have just been taken.') . ' You have not been charged.']);
        }

        // The thank-you page is tied to this browser session rather than to a
        // reference in the URL, so nobody can browse to someone else's booking.
        session()->set(self::SESSION_KEY, (int) $id);

        return $this->toCheckout($booking);
    }

    /** The "finish paying" link from the thank-you page or the email. */
    public function pay(string $token)
    {
        $booking = preg_match('/^[0-9a-f]{48}$/', $token)
            ? $this->bookings->where('pay_token', $token)->first()
            : null;

        if (! $booking || $booking['status'] === 'cancelled') {
            return redirect()->to($this->url())->with('error',
                'That payment link is no longer valid. Please email ' . $this->config->contactEmail . ' if you need help with your booking.');
        }

        session()->set(self::SESSION_KEY, (int) $booking['id']);

        if (! empty($booking['payment_received'])) {
            return redirect()->to($this->url('thank-you'));
        }

        if (! $this->config->isOpen() || ! $this->config->acceptsCard()) {
            return redirect()->to($this->url('thank-you'))->with('error', 'Online booking has now closed, so this booking can no longer be paid for. '
                . 'You have not been charged.');
        }

        return $this->toCheckout($booking);
    }

    /** Stripe's success_url. Settles the payment now in case the webhook is slow. */
    public function paid()
    {
        $sessionId = (string) $this->request->getGet('session_id');

        if (str_starts_with($sessionId, 'cs_')) {
            try {
                $booking = $this->service->fulfilById($sessionId);

                if ($booking) {
                    session()->set(self::SESSION_KEY, (int) $booking['id']);
                }
            } catch (\Throwable $e) {
                log_message('error', 'Connexions: could not check checkout {id} on return: {msg}', [
                    'id' => $sessionId, 'msg' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->to($this->url('thank-you'));
    }

    public function thankYou()
    {
        $id      = (int) session()->get(self::SESSION_KEY);
        $booking = $id > 0 ? $this->bookings->find($id) : null;

        if (! $booking) {
            return redirect()->to($this->url());
        }

        $paid = ! empty($booking['payment_received']);

        return view('connexions/thank_you', $this->pageData([
            'title'     => ($paid ? 'Booking confirmed' : 'Your booking') . ' – Connexions Singles Mix & Mingle',
            'noindex'   => true,
            'booking'   => $booking,
            'attendees' => $this->attendees->forBooking((int) $booking['id']),
            'payUrl'    => $paid || $booking['status'] === 'cancelled' || ! $this->config->isOpen()
                ? null
                : $this->url('pay/' . $booking['pay_token']),
        ]));
    }

    /* =====================================================================
       Helpers
    ===================================================================== */

    private function toCheckout(array $booking)
    {
        try {
            $url = $this->service->checkoutUrl($booking);
        } catch (\OverflowException $e) {
            return redirect()->to($this->url('thank-you'))->with('error',
                $e->getMessage() . ' Your places were released when the payment wasn’t completed, so this booking can’t be paid for. '
                . 'You have not been charged.');
        } catch (\Throwable $e) {
            log_message('error', 'Connexions: could not open Stripe Checkout for {ref}: {msg}', [
                'ref' => $booking['booking_ref'], 'msg' => $e->getMessage(),
            ]);

            return redirect()->to($this->url('thank-you'))->with('error',
                'Sorry, we couldn’t open the secure card payment page just now. You have not been charged. '
                . 'Please try again in a few minutes using the button below.');
        }

        return redirect()->to($url ?? $this->url('thank-you'));
    }

    /** "regex_match[...]" → "regex_match", for keying its error message. */
    private static function ruleName(string $rule): string
    {
        return strstr($rule, '[', true) ?: $rule;
    }

    private function url(string $suffix = ''): string
    {
        return site_url($this->config->slug . ($suffix !== '' ? '/' . $suffix : ''));
    }

    private function isOpen(): bool
    {
        return $this->config->isOpen() && $this->config->acceptsCard() && ! $this->service->isFull();
    }

    private function closedMessage(): string
    {
        $lead = match (true) {
            ! $this->config->isOpen()      => 'Online booking for Connexions has now closed.',
            $this->service->isFull()       => 'Connexions is fully booked.',
            default                        => 'Online booking for Connexions will open shortly.',
        };

        return $lead . ' For any questions, please contact ' . $this->config->contactsLine()
            . ', or email ' . $this->config->contactEmail . '.';
    }

    private function pageData(array $extra = []): array
    {
        return array_merge([
            'title'           => 'Connexions – Singles Mix & Mingle – ' . $this->config->eventLabel(),
            'metaDescription' => 'Connexions Singles Mix & Mingle for Gujarati singles aged 25–40, ' . $this->config->eventLabel()
                . ' at ' . $this->config->venue['name'] . '. £' . $this->config->ticketPrice
                . ' per person including a welcome drink and nibbles. Book online.',
            'config'          => $this->config,
            'isOpen'          => $this->isOpen(),
            'isFull'          => $this->service->isFull(),
            'placesLeft'      => $this->service->placesLeft(),
            'closedMessage'   => $this->closedMessage(),
        ], $extra);
    }
}
