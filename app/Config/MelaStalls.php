<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Stall holder bookings for the Golden Jubilee Mela (Event 3).
 *
 * Everything here is overridable from .env with the `mela.stalls.*` prefix, so
 * the deadline, fee or notification list can change without a deploy.
 */
class MelaStalls extends BaseConfig
{
    /**
     * Unlisted URL segment for the public booking page.
     *
     * The page is linked from nowhere, excluded from the sitemap and sent with
     * noindex, so it is only reachable by someone given the address. Note this
     * is obscurity, not authentication: anyone the link is forwarded to can
     * submit. Change this value to invalidate every link already handed out.
     */
    public string $slug = 'mela-stalls-2026-b7f3a9c2';

    /** Bookings close at the end of this day (Europe/London). */
    public string $closingDate = '2026-08-26';

    /** Event day, used for display and to stop bookings after the fact. */
    public string $eventDate = '2026-08-31';

    /** Stall fee in pounds. */
    public int $fee = 75;

    /**
     * Maximum stalls, or 0 for no limit.
     *
     * Cancelled bookings do not count towards it.
     */
    public int $capacity = 0;

    /**
     * Who is told when a stall is booked.
     *
     * Set `mela.stalls.notifyEmails` in .env as a comma-separated list. If it is
     * empty nobody is notified — the booking is still recorded and visible in
     * the admin screen, so no submission is ever lost, but chase this up.
     */
    public array $notifyEmails = [];

    /** Event details, repeated on the form and in both emails. */
    public array $venue = [
        'name'     => 'RCT Centre',
        'address1' => 'Bridleway off Headstone Lane',
        'address2' => 'Harrow',
        'postcode' => 'HA2 6NG',
    ];

    public string $eventName    = 'LCNL Golden Jubilee 50th Anniversary – Event 3: Mela';
    public string $eventTimes   = '12:00pm to 6:00pm';
    public string $setUpFrom    = '10:30am';
    public string $carParkClear = '11:30am';
    public string $stallSize    = '2m x 2m';

    /**
     * Bank details. Repeated in the confirmation email because that is the
     * point at which a stall holder needs them, and the booking is not
     * confirmed until payment arrives.
     */
    public array $bank = [
        'accountName'   => 'Lohana Community North London',
        'accountNumber' => '21497995',
        'sortCode'      => '40-23-13',
    ];

    /** Payment reference prefix; the company name is appended. */
    public string $paymentReferencePrefix = 'MelaStall';

    /** Organisers shown to stall holders for queries. */
    public array $contacts = [
        ['name' => 'Madhu Popat',   'phone' => '07500 701 318'],
        ['name' => 'Sheetal Barai', 'phone' => '07412 101 501'],
    ];

    /**
     * Stall categories, used to group stalls when allocating pitches.
     * Selecting "other" reveals a free-text box.
     */
    public array $categories = [
        'food'     => 'Food and drink',
        'clothing' => 'Clothing and textiles',
        'jewellery' => 'Jewellery and accessories',
        'crafts'   => 'Crafts and gifts',
        'homeware' => 'Homeware',
        'beauty'   => 'Health and beauty',
        'services' => 'Services (e.g. financial, legal, travel)',
        'charity'  => 'Charity or community group',
        'other'    => 'Other (please specify)',
    ];

    /** Upload rules for hygiene certificates and other supporting documents. */
    public int $maxDocuments = 5;

    /** Per-file limit in kilobytes. */
    public int $maxDocumentSizeKb = 8192;

    public array $allowedDocumentExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'heic', 'webp'];

    public array $allowedDocumentMimes = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/heic',
        'image/heif',
        'image/webp',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->slug        = (string) env('mela.stalls.slug', $this->slug);
        $this->closingDate = (string) env('mela.stalls.closingDate', $this->closingDate);
        $this->fee         = (int) env('mela.stalls.fee', $this->fee);
        $this->capacity    = (int) env('mela.stalls.capacity', $this->capacity);

        // Comma-separated in .env: mela.stalls.notifyEmails = a@x.org, b@y.org
        $raw = (string) env('mela.stalls.notifyEmails', '');
        if (trim($raw) !== '') {
            $this->notifyEmails = array_values(array_filter(
                array_map('trim', explode(',', $raw)),
                static fn(string $e): bool => filter_var($e, FILTER_VALIDATE_EMAIL) !== false
            ));
        }
    }

    /**
     * Where uploaded documents are stored.
     *
     * Deliberately outside the web root: these are hygiene certificates
     * carrying names, addresses and signatures. They are served only through
     * an admin-authenticated route.
     */
    public function documentPath(): string
    {
        return WRITEPATH . 'uploads/mela-stalls/';
    }

    /** Bookings are open until the end of the closing day. */
    public function isOpen(?\DateTimeInterface $now = null): bool
    {
        $tz    = new \DateTimeZone('Europe/London');
        $now ??= new \DateTimeImmutable('now', $tz);

        $closesAt = new \DateTimeImmutable($this->closingDate . ' 23:59:59', $tz);

        return $now <= $closesAt;
    }

    public function closingLabel(): string
    {
        return (new \DateTimeImmutable($this->closingDate, new \DateTimeZone('Europe/London')))
            ->format('l j F Y');
    }

    public function eventLabel(): string
    {
        return (new \DateTimeImmutable($this->eventDate, new \DateTimeZone('Europe/London')))
            ->format('l j F Y');
    }

    public function venueLines(): array
    {
        return array_values(array_filter([
            $this->venue['name'],
            $this->venue['address1'],
            $this->venue['address2'],
            $this->venue['postcode'],
        ]));
    }

    /** "MelaStall – Acme Foods" */
    public function paymentReference(string $companyName): string
    {
        $clean = trim(preg_replace('/\s+/u', ' ', $companyName));

        return $this->paymentReferencePrefix . ' – ' . ($clean !== '' ? $clean : 'Your Company Name');
    }
}
