<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Chopda Pujan 2026 registrations.
 *
 * One registration books one pooja at a fixed nyochavar, paid by card through
 * Stripe or by bank transfer to LCNL. A place is confirmed once the card payment
 * completes, or once an admin has matched the transfer.
 *
 * Overridable from .env with the `chopda.*` prefix.
 */
class ChopdaPujan extends BaseConfig
{
    /** Public URL segment. */
    public string $slug = 'chopda-pujan';

    public string $eventName = 'LCNL Chopda Pujan 2026';

    /** Event day (Europe/London). The home banner is shown until the end of it. */
    public string $eventDate = '2026-11-08';

    /** Registrations are accepted until the end of this day (Europe/London). */
    public string $closingDate = '2026-11-07';

    /** Nyochavar per pooja, in pounds. One registration is one pooja. */
    public int $fee = 35;

    /** How many Yajmans can sit for one pooja. */
    public int $yajmansPerPooja = 2;

    /** Upper bound for the attendee count on one registration (breakfast numbers). */
    public int $maxAttendees = 15;

    /** Maximum poojas (registrations), or 0 for no limit. Cancelled ones do not count. */
    public int $capacity = 100;

    /** Who is told when someone registers. Comma-separated in .env. */
    public array $notifyEmails = ['info@lcnl.org'];

    public string $registrationTimes = '9:00am – 10:00am (with breakfast)';
    public string $poojaTimes        = '10:00am – 11:30am';
    public string $conductedBy       = 'Shashtri Piyushbhai Mehta';

    public array $venue = [
        'name'     => 'J.V. Gokal Hall, Dhamecha Lohana Centre',
        'address1' => 'Brember Road',
        'address2' => 'Harrow',
        'postcode' => 'HA2 8AX',
    ];

    public array $contacts = [
        ['name' => 'Madhuben', 'phone' => '07500 701 318'],
        ['name' => 'Vishal',   'phone' => '07732 010 955'],
    ];

    /** The flier, under public/. A WebP of each width sits beside the JPEG. */
    public string $flier = 'assets/img/chopda-pujan/flier-2026';

    /**
     * Chopda stickers to print for the front of the book, label => path under
     * public/. A blank path shows the button as "coming soon" until the PDF is
     * uploaded.
     */
    public array $documents = [
        'English'  => 'assets/documents/chopda-pujan/sticker-2026-english.pdf',
        'Gujarati' => 'assets/documents/chopda-pujan/sticker-2026-gujarati.pdf',
    ];

    /** Samagri for the pooja: what manorathis bring, and what LCNL provides. */
    public array $samagri = [
        'bring' => [
            '1 swaroop of Lord Ganesh',
            'Laxmi coins (any coins will do instead)',
            '1 book (chopda) for the pujan',
            'Some sweets for distribution',
        ],
        'provided' => [
            'Abil, gulal, kankoo and chandan, on a plate or in a small bowl',
            'Dry fruits for the pooja, on a plate or in a small bowl',
            'Kesar water in a small bowl',
            'Some cinnamon, cloves and cardamom, on a plate or in a small bowl',
            '3 sopari: 1 for the kalash, and 1 each for Riddhi and Siddhi, placed near the Ganeshji swaroop',
            '1 nariyal per pooja plate',
            '1 kalash and a bottle of water',
            '7 nagarwel paan per pooja plate',
            'Panchamrut (milk, yogurt, honey, sugar and ghee) in a small plastic tub',
            'Nada chede: 1 half a foot long for the kalash, and 7 small ones for Ganeshji, Riddhi, Siddhi, the Laxmi coin, the red pen and the Yajman’s wrist (right wrist for gents, left for ladies)',
            'A few fresh flower petals on a pooja plate',
            '1 janoi per plate',
            'A tea light or homemade divo, and a matchbox, for the aarti',
            'A cloth napkin or tissues to dry your hands',
            'Some rice for the pooja',
            'Agarbatti',
            'Red pen',
            'Mishri or sugar',
            'Some warm water to bathe the Ganeshji swaroop',
            'A large plate with small containers for all the poojapo; a steel bowl, spoon and plate for bathing the swaroop; a steel plate to keep him on for the pooja after his bath; and a steel plate for the aarti',
        ],
    ];

    /**
     * Sthapna decoration colour per deity, each with a ghee divo and flowers.
     * The colour also styles the card (`.chopda-sthapna-item.is-red`).
     */
    public array $sthapna = [
        'Ganeshji'      => 'red',
        'Laxmi Narayan' => 'yellow',
    ];

    /** YouTube embed URL for the livestream; the section is hidden while blank. */
    public string $livestreamUrl = '';

    /**
     * Payment methods offered on the form. Card also needs the Stripe keys and
     * stripe.chopdaPriceId; while it is unavailable, bank transfer is offered
     * whatever this says, so the form always has a way to pay.
     */
    public bool $cardPayments = true;
    public bool $bankTransfer = true;

    /** Appended to LCNL's statement descriptor on the payer's card statement. */
    public string $statementDescriptorSuffix = 'CHOPDA';

    /** Payments go to LCNL's own account. */
    public array $bank = [
        'accountName'   => 'Lohana Community North London',
        'accountNumber' => '21497995',
        'sortCode'      => '40-23-13',
    ];

    /** UK Faster Payments references are limited to 18 characters. */
    public int $paymentReferenceMaxLength = 18;

    public string $paymentReferencePrefix = 'CP26';

    public function __construct()
    {
        parent::__construct();

        $this->closingDate   = (string) env('chopda.closingDate', $this->closingDate);
        $this->capacity      = (int) env('chopda.capacity', $this->capacity);
        $this->fee           = (int) env('chopda.fee', $this->fee);
        $this->livestreamUrl = (string) env('chopda.livestreamUrl', $this->livestreamUrl);
        $this->cardPayments  = (bool) env('chopda.cardPayments', $this->cardPayments);
        $this->bankTransfer  = (bool) env('chopda.bankTransfer', $this->bankTransfer);

        $raw = (string) env('chopda.notifyEmails', '');
        if (trim($raw) !== '') {
            $this->notifyEmails = array_values(array_filter(
                array_map('trim', explode(',', $raw)),
                static fn(string $e): bool => filter_var($e, FILTER_VALIDATE_EMAIL) !== false
            ));
        }
    }

    public function acceptsCard(): bool
    {
        $stripe = config('Stripe');

        return $this->cardPayments && $stripe->secretKey !== '' && $stripe->chopdaPriceId !== '';
    }

    public function acceptsBank(): bool
    {
        return $this->bankTransfer || ! $this->acceptsCard();
    }

    /** The methods on offer, card first: 'card' and/or 'bank'. */
    public function paymentMethods(): array
    {
        return array_keys(array_filter(['card' => $this->acceptsCard(), 'bank' => $this->acceptsBank()]));
    }

    /** The registrant's pay-by-card link, or null while card is not on offer. */
    public function payUrl(array $registration): ?string
    {
        return $this->acceptsCard() && ! empty($registration['pay_token'])
            ? site_url($this->slug . '/pay/' . $registration['pay_token'])
            : null;
    }

    public function isOpen(?\DateTimeInterface $now = null): bool
    {
        return $this->now($now) <= $this->endOf($this->closingDate);
    }

    /** True until the end of the event day; drives the home banner. */
    public function isUpcoming(?\DateTimeInterface $now = null): bool
    {
        return $this->now($now) <= $this->endOf($this->eventDate);
    }

    public function eventLabel(): string
    {
        return $this->dateLabel($this->eventDate);
    }

    public function closingLabel(): string
    {
        return $this->dateLabel($this->closingDate);
    }

    public function venueLine(): string
    {
        return implode(', ', array_filter($this->venue));
    }

    private function now(?\DateTimeInterface $now): \DateTimeInterface
    {
        return $now ?? new \DateTimeImmutable('now', new \DateTimeZone('Europe/London'));
    }

    private function endOf(string $date): \DateTimeImmutable
    {
        return new \DateTimeImmutable($date . ' 23:59:59', new \DateTimeZone('Europe/London'));
    }

    private function dateLabel(string $date): string
    {
        return (new \DateTimeImmutable($date, new \DateTimeZone('Europe/London')))->format('l j F Y');
    }
}
