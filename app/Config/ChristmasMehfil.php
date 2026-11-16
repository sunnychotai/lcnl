<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * LCNL Christmas Mehfil 2026 ticket bookings.
 *
 * A booking is any mix of individual tickets and tables of ten, paid by bank
 * transfer to LCNL within 48 hours. Prices drop to the early bird rate until
 * $earlyBirdEnds; the price is fixed when the booking is made. A booking is
 * confirmed once an admin has matched the transfer, and an admin cancels
 * bookings that go unpaid (nothing is cancelled automatically).
 *
 * Overridable from .env with the `mehfil.*` prefix.
 */
class ChristmasMehfil extends BaseConfig
{
    /** Public URL segment. */
    public string $slug = 'christmas-mehfil';

    public string $eventName = 'LCNL Christmas Mehfil 2026';

    /** Event day (Europe/London). The home banner is shown until the end of it. */
    public string $eventDate = '2026-12-13';

    public string $times = '5pm – 10:30pm';

    /** Bookings are accepted until the end of this day (Europe/London). */
    public string $closingDate = '2026-12-10';

    /** Early bird prices apply until the end of this day (Europe/London). */
    public string $earlyBirdEnds = '2026-12-05';

    /** Prices in pounds, by band. A table seats $tableSize. */
    public array $prices = [
        'early'    => ['individual' => 30, 'table' => 270],
        'standard' => ['individual' => 35, 'table' => 300],
    ];

    public int $tableSize = 10;

    /** Dropdown limits on one booking. */
    public int $maxIndividual = 9;
    public int $maxTables     = 5;

    /**
     * Maximum people (seats) across all bookings, or 0 for no limit. A table
     * counts as $tableSize. Cancelled bookings do not count.
     */
    public int $capacity = 300;

    /** Hours a booker has to pay before an admin may cancel the booking. */
    public int $payWithinHours = 48;

    /** Who is told when someone books. Comma-separated in .env. */
    public array $notifyEmails = ['info@lcnl.org'];

    public string $contactEmail = 'info@lcnl.org';

    public array $venue = [
        'name'     => 'Dhamecha Lohana Centre',
        'address1' => 'Brember Road',
        'address2' => 'South Harrow',
        'postcode' => 'HA2 8AX',
    ];

    /** The flier, under public/. A WebP of each width sits beside the JPEG. */
    public string $flier = 'assets/img/christmas-mehfil/flier-2026';

    /** Raffle ticket bundles sold on the night, cash only. */
    public array $raffle = [
        '£5 for 5 tickets',
        '£10 for 20 tickets',
    ];

    /** Payments go to LCNL's own account (the same one as Chopda Pujan). */
    public array $bank = [
        'accountName'   => 'Lohana Community North London',
        'accountNumber' => '21497995',
        'sortCode'      => '40-23-13',
    ];

    /** Reference prefix: XMAS-RONPAW is "Ronak Pawar". */
    public string $referencePrefix = 'XMAS-';

    public function __construct()
    {
        parent::__construct();

        $this->closingDate   = (string) env('mehfil.closingDate', $this->closingDate);
        $this->earlyBirdEnds = (string) env('mehfil.earlyBirdEnds', $this->earlyBirdEnds);
        $this->capacity      = (int) env('mehfil.capacity', $this->capacity);

        $raw = (string) env('mehfil.notifyEmails', '');
        if (trim($raw) !== '') {
            $this->notifyEmails = array_values(array_filter(
                array_map('trim', explode(',', $raw)),
                static fn(string $e): bool => filter_var($e, FILTER_VALIDATE_EMAIL) !== false
            ));
        }
    }

    /* =====================================================================
       Dates
    ===================================================================== */

    public function isOpen(?\DateTimeInterface $now = null): bool
    {
        return $this->now($now) <= $this->endOf($this->closingDate);
    }

    /** True until the end of the event day; drives the home banner. */
    public function isUpcoming(?\DateTimeInterface $now = null): bool
    {
        return $this->now($now) <= $this->endOf($this->eventDate);
    }

    public function isEarlyBird(?\DateTimeInterface $now = null): bool
    {
        return $this->now($now) <= $this->endOf($this->earlyBirdEnds);
    }

    /** Whole days until the event, in London; 0 on the day. */
    public function daysToGo(?\DateTimeInterface $now = null): int
    {
        $today = new \DateTimeImmutable($this->now($now)->format('Y-m-d'), new \DateTimeZone('Europe/London'));

        return (int) $today->diff(new \DateTimeImmutable($this->eventDate, new \DateTimeZone('Europe/London')))->format('%r%a');
    }

    /** "Sunday 13 December 2026" */
    public function eventLabel(): string
    {
        return $this->dateLabel($this->eventDate, 'l j F Y');
    }

    /** "Thursday 10 December" */
    public function closingLabel(): string
    {
        return $this->dateLabel($this->closingDate, 'l j F');
    }

    /** "Saturday 5 December": the last early bird day. */
    public function earlyBirdLabel(): string
    {
        return $this->dateLabel($this->earlyBirdEnds, 'l j F');
    }

    /** "6 December": the first full-price day. */
    public function standardFromLabel(): string
    {
        return $this->endOf($this->earlyBirdEnds)->modify('+1 second')->format('j F');
    }

    /**
     * When a booking made at $createdAt must be paid by, in London time.
     * created_at is stored in the app timezone (UTC).
     */
    public function payBy(string $createdAt): \DateTimeImmutable
    {
        return (new \DateTimeImmutable($createdAt, new \DateTimeZone(config('App')->appTimezone)))
            ->setTimezone(new \DateTimeZone('Europe/London'))
            ->modify('+' . $this->payWithinHours . ' hours');
    }

    /** "5:30pm on Wednesday 18 November" */
    public function payByLabel(string $createdAt): string
    {
        $t = $this->payBy($createdAt);

        return $t->format($t->format('i') === '00' ? 'ga' : 'g:ia') . ' on ' . $t->format('l j F');
    }

    public function isOverdue(array $booking, ?\DateTimeInterface $now = null): bool
    {
        return $booking['status'] === 'booked'
            && empty($booking['payment_received'])
            && $this->now($now) > $this->payBy($booking['created_at']);
    }

    public function venueLine(): string
    {
        return implode(', ', array_filter($this->venue));
    }

    /* =====================================================================
       Prices
    ===================================================================== */

    /** 'early' or 'standard'. */
    public function band(?\DateTimeInterface $now = null): string
    {
        return $this->isEarlyBird($now) ? 'early' : 'standard';
    }

    /** ['individual' => 30, 'table' => 270] for the band in force now. */
    public function currentPrices(?\DateTimeInterface $now = null): array
    {
        return $this->prices[$this->band($now)];
    }

    /**
     * Price a booking at today's rate. The server always works the total out
     * itself; the form's running total is only a preview.
     *
     * @return array{band: string, individual_price: int, table_price: int, amount: int, people: int}
     */
    public function quote(int $individual, int $tables, ?\DateTimeInterface $now = null): array
    {
        $band   = $this->band($now);
        $prices = $this->prices[$band];

        return [
            'band'             => $band,
            'individual_price' => $prices['individual'],
            'table_price'      => $prices['table'],
            'amount'           => $individual * $prices['individual'] + $tables * $prices['table'],
            'people'           => $this->people($individual, $tables),
        ];
    }

    public function people(int $individual, int $tables): int
    {
        return $individual + $tables * $this->tableSize;
    }

    /* =====================================================================
       Payment reference
    ===================================================================== */

    /**
     * XMAS + the first three letters of the first name and of the surname, in
     * capitals: Ronak Pawar is XMAS-RONPAW. Accents are dropped and anything
     * that isn't a letter is skipped (O'Brien gives OBR). The model adds a
     * number when the reference is already taken.
     */
    public function referenceFor(string $firstName, string $lastName): string
    {
        $part = static function (string $name): string {
            $ascii = function_exists('iconv') ? (string) @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name) : $name;

            return substr(strtoupper(preg_replace('/[^A-Za-z]/', '', $ascii)), 0, 3);
        };

        $letters = $part($firstName) . $part($lastName);

        return $this->referencePrefix . ($letters !== '' ? $letters : 'GUEST');
    }

    /* =====================================================================
       Helpers
    ===================================================================== */

    private function now(?\DateTimeInterface $now): \DateTimeInterface
    {
        return $now ?? new \DateTimeImmutable('now', new \DateTimeZone('Europe/London'));
    }

    private function endOf(string $date): \DateTimeImmutable
    {
        return new \DateTimeImmutable($date . ' 23:59:59', new \DateTimeZone('Europe/London'));
    }

    private function dateLabel(string $date, string $format): string
    {
        return (new \DateTimeImmutable($date, new \DateTimeZone('Europe/London')))->format($format);
    }
}
