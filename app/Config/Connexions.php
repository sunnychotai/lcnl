<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Connexions – Singles Mix & Mingle 2026 ticket bookings.
 *
 * A booking is up to five girl and five boy tickets at a flat price, with
 * every attendee named. Payment is by card only, through Stripe Checkout.
 * Places are held while the booker is paying and released if the Checkout
 * expires unpaid. Girls and boys have separate capacities.
 *
 * Overridable from .env with the `connexions.*` prefix.
 */
class Connexions extends BaseConfig
{
    /** Public URL segment. */
    public string $slug = 'connexions';

    public string $eventName = 'Connexions – Singles Mix & Mingle';

    /** The event's own page on the site (events/66). */
    public int $eventId = 66;

    /** Event day (Europe/London). The home banner is shown until the end of it. */
    public string $eventDate = '2026-12-16';

    public string $times = '7:30pm – 11pm';

    public string $registrationFrom = '7:30pm';

    /** Bookings are accepted until the end of this day (Europe/London). */
    public string $closingDate = '2026-12-14';

    /** Price of one ticket, in pounds. Must match the Stripe price (stripe.connexionsPriceId). */
    public int $ticketPrice = 20;

    /** Most girl tickets, and most boy tickets, on one booking. */
    public int $maxPerGender = 5;

    /**
     * Places for girls and for boys, or 0 for no limit. Paid bookings and
     * bookings still in Checkout count; cancelled and abandoned ones do not.
     * .env: connexions.capacityGirls, connexions.capacityBoys.
     *
     * @var array{girl: int, boy: int}
     */
    public array $capacity = ['girl' => 50, 'boy' => 50];

    /** How long places are held while the booker pays. Stripe needs at least 30 minutes. */
    public int $holdMinutes = 60;

    /** Who the event is for; the booker must tick to confirm it. */
    public string $eligibility = 'Gujarati singles aged 25–40';

    /** Labels for the two ticket types. */
    public array $genders = [
        'girl' => ['one' => 'Girl', 'many' => 'Girls'],
        'boy'  => ['one' => 'Boy', 'many' => 'Boys'],
    ];

    /** Who is told when a booking is paid. Comma-separated in .env. */
    public array $notifyEmails = ['info@lcnl.org'];

    public string $contactEmail = 'info@lcnl.org';

    public array $contacts = [
        ['name' => 'Sheetal Barai', 'phone' => '07412 101501'],
        ['name' => 'Meena Jasani',  'phone' => '07900 005719'],
    ];

    public array $venue = [
        'name'     => 'Esquires Northwood',
        'address1' => '55 Green Lane',
        'address2' => 'Northwood',
        'postcode' => 'HA6 3AE',
    ];

    /** The flier, under public/. 480 and 854px WebPs sit beside the JPEG. */
    public string $flier = 'assets/img/connexions/flier-2026';

    /** Appended to LCNL's statement descriptor on the payer's card statement. */
    public string $statementDescriptorSuffix = 'CONNEXIONS';

    public string $referencePrefix = 'CNX26';

    public function __construct()
    {
        parent::__construct();

        $this->closingDate      = (string) env('connexions.closingDate', $this->closingDate);
        $this->capacity['girl'] = (int) env('connexions.capacityGirls', $this->capacity['girl']);
        $this->capacity['boy']  = (int) env('connexions.capacityBoys', $this->capacity['boy']);

        $raw = (string) env('connexions.notifyEmails', '');
        if (trim($raw) !== '') {
            $this->notifyEmails = array_values(array_filter(
                array_map('trim', explode(',', $raw)),
                static fn(string $e): bool => filter_var($e, FILTER_VALIDATE_EMAIL) !== false
            ));
        }
    }

    /** Card payment needs the Stripe key and the ticket price. Without them the form stays shut. */
    public function acceptsCard(): bool
    {
        $stripe = config('Stripe');

        return $stripe->secretKey !== '' && $stripe->connexionsPriceId !== '';
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

    /** Whole days until the event, in London; 0 on the day. */
    public function daysToGo(?\DateTimeInterface $now = null): int
    {
        $today = new \DateTimeImmutable($this->now($now)->format('Y-m-d'), new \DateTimeZone('Europe/London'));

        return (int) $today->diff(new \DateTimeImmutable($this->eventDate, new \DateTimeZone('Europe/London')))->format('%r%a');
    }

    /** "Wednesday 16 December 2026" */
    public function eventLabel(): string
    {
        return $this->dateLabel($this->eventDate, 'l j F Y');
    }

    /** "Monday 14 December" */
    public function closingLabel(): string
    {
        return $this->dateLabel($this->closingDate, 'l j F');
    }

    public function venueLine(): string
    {
        return implode(', ', array_filter($this->venue));
    }

    public function contactsLine(): string
    {
        return implode(' or ', array_map(
            static fn(array $c): string => $c['name'] . ' on ' . $c['phone'],
            $this->contacts
        ));
    }

    /* =====================================================================
       Tickets
    ===================================================================== */

    /** "2 girls and 1 boy" */
    public function ticketsLabel(int $girls, int $boys): string
    {
        $parts = [];

        foreach (['girl' => $girls, 'boy' => $boys] as $gender => $n) {
            if ($n > 0) {
                $parts[] = $n . ' ' . strtolower($this->genders[$gender][$n === 1 ? 'one' : 'many']);
            }
        }

        return implode(' and ', $parts);
    }

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
