<?php

namespace App\Controllers;

use App\Models\MehfilBookingModel;
use App\Services\MehfilBookingService;
use Config\ChristmasMehfil;

/**
 * LCNL Christmas Mehfil 2026 ticket booking.
 *
 * Bookers choose individual tickets and/or tables, then are shown LCNL's bank
 * details and a reference to pay within 48 hours. There is no card payment.
 * An admin marks the booking paid once the transfer arrives.
 */
class ChristmasMehfilController extends BaseController
{
    /** Session key holding the booking the current visitor has just made. */
    private const SESSION_KEY = 'mehfil_booking_id';

    protected ChristmasMehfil $config;
    protected MehfilBookingModel $bookings;
    protected MehfilBookingService $service;

    public function __construct()
    {
        $this->config   = config('ChristmasMehfil');
        $this->bookings = new MehfilBookingModel();
        $this->service  = new MehfilBookingService();
    }

    public function index()
    {
        return view('mehfil/index', $this->pageData());
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
            log_message('info', 'Mehfil: honeypot triggered from {ip}', ['ip' => $this->request->getIPAddress()]);

            return redirect()->to($this->url())->with('error',
                'Thank you — if your booking was received you will get a confirmation email shortly.');
        }

        // Nobody fills this form in under three seconds.
        $startedAt = (int) ($post['form_time'] ?? 0);
        if ($startedAt === 0 || (time() - $startedAt) < 3) {
            return redirect()->back()->withInput()
                ->with('errors', ['Submission rejected. Please try again.']);
        }

        if (service('throttler')->check('mehfil-' . md5($this->request->getIPAddress()), 3, MINUTE) === false) {
            return redirect()->back()->withInput()
                ->with('errors', ['Too many submissions. Please wait a minute and try again.']);
        }

        $rules = [
            'first_name'         => 'required|trim|max_length[100]',
            'last_name'          => 'required|trim|max_length[100]',
            'email'              => 'required|trim|valid_email|max_length[255]',
            'phone'              => 'required|trim|min_length[7]|max_length[30]|regex_match[/^[0-9 +()\-]+$/]',
            'postcode'           => 'required|trim|max_length[10]',
            'individual_tickets' => 'required|is_natural|less_than_equal_to[' . $this->config->maxIndividual . ']',
            'tables'             => 'required|is_natural|less_than_equal_to[' . $this->config->maxTables . ']',
            'privacy_consent'    => 'required',
        ];

        $messages = [
            'first_name'         => ['required' => 'Please enter your first name.'],
            'last_name'          => ['required' => 'Please enter your surname.'],
            'email'              => ['required' => 'Please enter your email address.', 'valid_email' => 'Please enter a valid email address.'],
            'phone'              => [
                'required'    => 'Please enter your mobile number.',
                'min_length'  => 'Please enter a valid mobile number.',
                'regex_match' => 'Please enter a valid mobile number.',
            ],
            'postcode'           => ['required' => 'Please enter your postcode.'],
            'individual_tickets' => ['less_than_equal_to' => 'Please choose up to ' . $this->config->maxIndividual . ' individual tickets.'],
            'tables'             => ['less_than_equal_to' => 'Please choose up to ' . $this->config->maxTables . ' tables.'],
            'privacy_consent'    => ['required' => 'Please confirm you are happy for us to hold your details.'],
        ];

        $valid  = $this->validate($rules, $messages);
        $errors = $valid ? [] : $this->validator->getErrors();

        $postcode = $this->normalisePostcode((string) ($post['postcode'] ?? ''));
        if (! isset($errors['postcode']) && $postcode === null) {
            $errors['postcode'] = 'Please enter a valid UK postcode, e.g. HA2 8AX.';
        }

        $individual = (int) ($post['individual_tickets'] ?? 0);
        $tables     = (int) ($post['tables'] ?? 0);

        if (! isset($errors['individual_tickets']) && ! isset($errors['tables']) && $individual + $tables === 0) {
            $errors['tickets'] = 'Please choose at least one individual ticket or table.';
        }

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $seatsLeft = $this->seatsLeft();
        $quote     = $this->config->quote($individual, $tables);

        if ($seatsLeft !== null && $quote['people'] > $seatsLeft) {
            return $seatsLeft < 1
                ? redirect()->to($this->url())->with('error', $this->closedMessage())
                : redirect()->back()->withInput()->with('errors', ['tickets' => $this->seatsLeftMessage($seatsLeft)]);
        }

        $id = $this->bookings->insert([
            'booking_ref'        => $this->bookings->uniqueRef(
                $this->config->referenceFor($post['first_name'], $post['last_name'])
            ),
            'first_name'         => $post['first_name'],
            'last_name'          => $post['last_name'],
            'email'              => $post['email'],
            'phone'              => $post['phone'],
            'postcode'           => $postcode,
            'is_member'          => empty($post['is_member']) ? 0 : 1,
            'individual_tickets' => $individual,
            'tables'             => $tables,
            'price_band'         => $quote['band'],
            'individual_price'   => $quote['individual_price'],
            'table_price'        => $quote['table_price'],
            'amount'             => $quote['amount'],
            'privacy_consent'    => 1,
            'status'             => 'booked',
            'ip_address'         => $this->request->getIPAddress(),
        ], true);

        $booking = $this->bookings->find($id);

        $this->service->email($booking, 'mehfil_booking_confirmation',
            'Christmas Mehfil – please pay within ' . $this->config->payWithinHours . ' hours to confirm your booking');
        $this->service->notifyOrganisers($booking);

        // The confirmation page is tied to this browser session rather than to
        // a reference in the URL, so nobody can browse to someone else's booking.
        session()->set(self::SESSION_KEY, (int) $id);

        return redirect()->to($this->url('thank-you'));
    }

    public function thankYou()
    {
        $id      = (int) session()->get(self::SESSION_KEY);
        $booking = $id > 0 ? $this->bookings->find($id) : null;

        if (! $booking) {
            return redirect()->to($this->url());
        }

        return view('mehfil/thank_you', $this->pageData([
            'title'   => 'Your booking – Christmas Mehfil 2026',
            'noindex' => true,
            'booking' => $booking,
        ]));
    }

    /* =====================================================================
       Helpers
    ===================================================================== */

    /** "ha28ax" → "HA2 8AX", or null if it isn't a UK postcode. */
    private function normalisePostcode(string $raw): ?string
    {
        $compact = strtoupper(preg_replace('/\s+/', '', $raw));

        if (! preg_match('/^[A-Z]{1,2}[0-9][A-Z0-9]?[0-9][A-Z]{2}$/', $compact)) {
            return null;
        }

        return substr($compact, 0, -3) . ' ' . substr($compact, -3);
    }

    private function url(string $suffix = ''): string
    {
        return site_url($this->config->slug . ($suffix !== '' ? '/' . $suffix : ''));
    }

    /** Seats still free, or null when there is no cap. */
    private function seatsLeft(): ?int
    {
        if ($this->config->capacity <= 0) {
            return null;
        }

        return max(0, $this->config->capacity - $this->bookings->seatsTaken($this->config->tableSize));
    }

    private function isFull(): bool
    {
        $left = $this->seatsLeft();

        return $left !== null && $left < 1;
    }

    private function isOpen(): bool
    {
        return $this->config->isOpen() && ! $this->isFull();
    }

    private function seatsLeftMessage(int $left): string
    {
        $msg = 'Sorry, there ' . ($left === 1 ? 'is only 1 place' : 'are only ' . $left . ' places') . ' left';

        if ($left < $this->config->tableSize) {
            $msg .= ', so we can no longer take table bookings';
        }

        return $msg . '. Please choose fewer tickets.';
    }

    private function closedMessage(): string
    {
        $lead = $this->isFull()
            ? 'The Christmas Mehfil is sold out.'
            : 'Online booking for the Christmas Mehfil has now closed.';

        return $lead . ' To ask about tickets, please email ' . $this->config->contactEmail . '.';
    }

    private function pageData(array $extra = []): array
    {
        $prices = $this->config->currentPrices();

        return array_merge([
            'title'           => 'Christmas Mehfil 2026 – ' . $this->config->eventLabel(),
            'metaDescription' => 'Join LCNL for the Christmas Mehfil on ' . $this->config->eventLabel()
                . ' at the Dhamecha Lohana Centre: live band, vegetarian dinner and drinks, bingo and a charity raffle. '
                . 'Tickets £' . $prices['individual'] . ' per person or £' . $prices['table'] . ' for a table of ' . $this->config->tableSize . '.',
            'config'          => $this->config,
            'isOpen'          => $this->isOpen(),
            'isFull'          => $this->isFull(),
            'seatsLeft'       => $this->seatsLeft(),
            'closedMessage'   => $this->closedMessage(),
        ], $extra);
    }
}
