<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * "50 Years of LCNL – 100 Donors, One Lasting Legacy" pledge campaign.
 *
 * Donors pledge £500 by bank transfer to the Lohana Charitable Foundation to
 * clear the remaining loan on the Dhamecha Lohana Centre; LCNL matches each
 * pledge. Progress is deliberately never shown publicly — only in the admin
 * screen.
 *
 * Overridable from .env with the `legacy50.*` prefix.
 */
class Legacy50 extends BaseConfig
{
    /** Public URL segment. */
    public string $slug = 'legacy50';

    public string $campaignName = '50 Years of LCNL – 100 Donors, One Lasting Legacy';

    /** Pledges are accepted until the end of this day (Europe/London). */
    public string $closingDate = '2026-12-01';

    /** The date the campaign is aiming for, shown in the copy. */
    public string $targetDate = '2026-12-01';

    /** Each pledge, in pounds. Donors give exactly this — no multiples. */
    public int $donation = 500;

    /** LCNL's matching contribution per pledge. */
    public int $match = 500;

    public int $targetDonors = 100;

    public int $targetAmount = 100000;

    /**
     * Maximum pledges, or 0 for no limit. Cancelled pledges do not count.
     * Left unlimited so the form never hints at how close the target is.
     */
    public int $capacity = 0;

    /** Who is told when a pledge comes in. Comma-separated in .env. */
    public array $notifyEmails = ['info@lcnl.org'];

    /** The plaque is engraved, so keep names short enough to fit a line. */
    public int $plaqueMaxLength = 40;

    /** UK Faster Payments references are limited to 18 characters. */
    public int $paymentReferenceMaxLength = 18;

    public string $paymentReferencePrefix = 'LCNL50';

    /** Donations go to LCF, not to LCNL's own account. */
    public array $bank = [
        'accountName'   => 'LOHANA CHARITABLE FOUNDATION LTD',
        'accountType'   => 'Business',
        'accountNumber' => '18161893',
        'sortCode'      => '23-05-80',
        'iban'          => 'GB61MYMB23058018161893',
        'bic'           => 'MYMBGB2L',
    ];

    public string $charityName = 'Lohana Charitable Foundation (LCF)';

    public array $titles = ['Mr', 'Mrs', 'Miss', 'Ms', 'Dr'];

    /**
     * Gift Aid declaration text, stored verbatim against every pledge that
     * ticks it. HMRC can ask to see the declaration a claim relies on, so if
     * this wording ever changes, bump the version too.
     */
    public string $giftAidVersion = 'LCF-GA-2026-10';

    public array $giftAidStatements = [
        'Please treat all donations I make or have made to the Lohana Charitable Foundation (LCF) '
            . 'for the past 4 years as Gift Aid donations until further notice.',
        'I confirm I am a UK Taxpayer and I understand that if I pay less income tax and/or capital '
            . 'gains tax than the amount of gift aid claimed on all my donations in that tax year, it '
            . 'is my responsibility to pay any difference.',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->closingDate = (string) env('legacy50.closingDate', $this->closingDate);
        $this->capacity    = (int) env('legacy50.capacity', $this->capacity);

        $raw = (string) env('legacy50.notifyEmails', '');
        if (trim($raw) !== '') {
            $this->notifyEmails = array_values(array_filter(
                array_map('trim', explode(',', $raw)),
                static fn(string $e): bool => filter_var($e, FILTER_VALIDATE_EMAIL) !== false
            ));
        }
    }

    public function isOpen(?\DateTimeInterface $now = null): bool
    {
        $tz    = new \DateTimeZone('Europe/London');
        $now ??= new \DateTimeImmutable('now', $tz);

        return $now <= new \DateTimeImmutable($this->closingDate . ' 23:59:59', $tz);
    }

    public function closingLabel(): string
    {
        return $this->dateLabel($this->closingDate);
    }

    public function targetLabel(): string
    {
        return $this->dateLabel($this->targetDate);
    }

    /** The full Gift Aid declaration as stored against a pledge. */
    public function giftAidWording(): string
    {
        return implode("\n\n", $this->giftAidStatements);
    }

    private function dateLabel(string $date): string
    {
        return (new \DateTimeImmutable($date, new \DateTimeZone('Europe/London')))->format('l j F Y');
    }
}
