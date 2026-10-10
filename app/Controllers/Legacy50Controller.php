<?php

namespace App\Controllers;

use App\Models\EmailQueueModel;
use App\Models\LegacyPledgeModel;
use Config\Legacy50;

/**
 * "50 Years of LCNL – 100 Donors, One Lasting Legacy" pledge form.
 *
 * A pledge is a promise to transfer £500 to LCF; nothing is charged here.
 * Payment is verified separately against the LCF bank statement in the admin
 * screen. Campaign progress is never shown publicly.
 */
class Legacy50Controller extends BaseController
{
    /** Session key holding the pledge the current visitor has just made. */
    private const SESSION_KEY = 'legacy50_pledge_id';

    protected Legacy50 $config;
    protected LegacyPledgeModel $pledges;
    protected EmailQueueModel $emails;

    public function __construct()
    {
        $this->config  = config('Legacy50');
        $this->pledges = new LegacyPledgeModel();
        $this->emails  = new EmailQueueModel();
    }

    public function index()
    {
        return view('legacy50/index', $this->pageData());
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
            log_message('info', 'Legacy50: honeypot triggered from {ip}', ['ip' => $this->request->getIPAddress()]);

            return redirect()->to($this->url())->with('error',
                'Thank you — if your pledge was received you will get a confirmation email shortly.');
        }

        // Nobody fills this form in under three seconds.
        $startedAt = (int) ($post['form_time'] ?? 0);
        if ($startedAt === 0 || (time() - $startedAt) < 3) {
            return redirect()->back()->withInput()
                ->with('errors', ['Submission rejected. Please try again.']);
        }

        if (service('throttler')->check('legacy50-' . md5($this->request->getIPAddress()), 3, MINUTE) === false) {
            return redirect()->back()->withInput()
                ->with('errors', ['Too many submissions. Please wait a minute and try again.']);
        }

        // Gift Aid needs both statements; ticking only one is a mistake to
        // point out, not something to quietly accept or ignore.
        $treat    = ! empty($post['gift_aid_treat']);
        $taxpayer = ! empty($post['gift_aid_taxpayer']);
        $giftAid  = $treat && $taxpayer;

        $rules = [
            'title'              => 'permit_empty|in_list[' . implode(',', $this->config->titles) . ']',
            'first_name'         => 'required|trim|max_length[100]',
            'last_name'          => 'required|trim|max_length[100]',
            'email'              => 'required|trim|valid_email|max_length[255]',
            'phone'              => 'required|trim|min_length[7]|max_length[30]',
            'plaque_name'        => 'required|trim|max_length[' . $this->config->plaqueMaxLength . ']',
            'payment_reference'  => 'required|trim|max_length[' . $this->config->paymentReferenceMaxLength . ']',
            'payment_timing'     => 'required|in_list[paid,will_pay]',
            'confirmed_donation' => 'required',
            'privacy_consent'    => 'required',
            'comments'           => 'permit_empty|trim|max_length[2000]',
        ];

        $messages = [
            'first_name'         => ['required' => 'Please enter your first name.'],
            'last_name'          => ['required' => 'Please enter your last name.'],
            'plaque_name'        => [
                'required'   => 'Please tell us the name to appear on the plaque.',
                'max_length' => 'The plaque name must be ' . $this->config->plaqueMaxLength . ' characters or fewer.',
            ],
            'payment_reference'  => [
                'required'   => 'Please tell us the reference you will use for your bank transfer.',
                'max_length' => 'Bank references can be at most ' . $this->config->paymentReferenceMaxLength . ' characters.',
            ],
            'payment_timing'     => ['required' => 'Please tell us whether you have already made the transfer.'],
            'confirmed_donation' => ['required' => 'Please confirm your £' . $this->config->donation . ' donation.'],
            'privacy_consent'    => ['required' => 'Please confirm you are happy for us to hold your details.'],
        ];

        if ($giftAid) {
            $rules += [
                'house_name_number' => 'required|trim|max_length[100]',
                'street'            => 'permit_empty|trim|max_length[150]',
                'town'              => 'required|trim|max_length[100]',
                'postcode'          => 'required|trim|max_length[10]',
            ];
            $messages += [
                'house_name_number' => ['required' => 'Gift Aid needs your house name or number.'],
                'town'              => ['required' => 'Gift Aid needs your town or city.'],
                'postcode'          => ['required' => 'Gift Aid needs your postcode.'],
            ];
        }

        $valid  = $this->validate($rules, $messages);
        $errors = $valid ? [] : $this->validator->getErrors();

        if ($treat !== $taxpayer) {
            $errors['gift_aid'] = 'To add Gift Aid please tick both Gift Aid boxes, or untick both if you are not eligible.';
        }

        $reference = trim(preg_replace('/\s+/', ' ', (string) ($post['payment_reference'] ?? '')));
        if (! isset($errors['payment_reference']) && ! preg_match('/^[A-Za-z0-9 \-]+$/', $reference)) {
            $errors['payment_reference'] = 'Please use only letters, numbers, spaces and hyphens in your payment reference.';
        }

        $postcode = $giftAid ? $this->normalisePostcode((string) ($post['postcode'] ?? '')) : null;
        if ($giftAid && ! isset($errors['postcode']) && $postcode === null) {
            $errors['postcode'] = 'Please enter a valid UK postcode.';
        }

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        if ($this->isFull()) {
            return redirect()->to($this->url())->with('error', $this->closedMessage());
        }

        $plaqueName = trim(preg_replace('/\s+/u', ' ', $post['plaque_name']));

        $id = $this->pledges->insert([
            'pledge_ref'            => $this->pledges->generateRef(),
            'title'                 => ($post['title'] ?? '') !== '' ? $post['title'] : null,
            'first_name'            => $post['first_name'],
            'last_name'             => $post['last_name'],
            'email'                 => $post['email'],
            'phone'                 => $post['phone'],
            'house_name_number'     => $giftAid ? $post['house_name_number'] : null,
            'street'                => $giftAid ? (($post['street'] ?? '') ?: null) : null,
            'town'                  => $giftAid ? $post['town'] : null,
            'postcode'              => $postcode,
            'plaque_name'           => $plaqueName,
            'plaque_name_submitted' => $plaqueName,
            'gift_aid'              => $giftAid ? 1 : 0,
            'gift_aid_declared_at'  => $giftAid ? date('Y-m-d H:i:s') : null,
            'gift_aid_version'      => $giftAid ? $this->config->giftAidVersion : null,
            'gift_aid_wording'      => $giftAid ? $this->config->giftAidWording() : null,
            'payment_reference'     => strtoupper($reference),
            'payment_timing'        => $post['payment_timing'],
            'amount'                => $this->config->donation,
            'confirmed_donation'    => 1,
            'privacy_consent'       => 1,
            'comments'              => ($post['comments'] ?? '') ?: null,
            'status'                => 'pledged',
            'ip_address'            => $this->request->getIPAddress(),
        ], true);

        $pledge = $this->pledges->find($id);

        $this->sendConfirmation($pledge);
        $this->notifyOrganisers($pledge);

        // The thank-you page is tied to this browser session rather than to a
        // reference in the URL, so nobody can browse to someone else's pledge.
        session()->set(self::SESSION_KEY, (int) $id);

        return redirect()->to($this->url('thank-you'));
    }

    public function thankYou()
    {
        $id     = (int) session()->get(self::SESSION_KEY);
        $pledge = $id > 0 ? $this->pledges->find($id) : null;

        if (! $pledge) {
            return redirect()->to($this->url());
        }

        return view('legacy50/thank_you', $this->pageData([
            'title'   => 'Thank you for your pledge – 50 Years of LCNL',
            'noindex' => true,
            'pledge'  => $pledge,
        ]));
    }

    /* =====================================================================
       Email
    ===================================================================== */

    private function sendConfirmation(array $pledge): void
    {
        $html = view('emails/legacy50_pledge_confirmation', [
            'pledge' => $pledge,
            'config' => $this->config,
        ]);

        $this->emails->enqueue([
            'to_email'   => $pledge['email'],
            'to_name'    => $pledge['first_name'] . ' ' . $pledge['last_name'],
            'subject'    => 'Thank you for your pledge – 50 Years of LCNL',
            'type'       => 'legacy50_pledge_confirmation',
            'related_id' => $pledge['id'],
            'body_html'  => $html,
            'body_text'  => html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'priority'   => 1,
        ]);
    }

    private function notifyOrganisers(array $pledge): void
    {
        if ($this->config->notifyEmails === []) {
            log_message('warning',
                'Legacy50: pledge {ref} saved but legacy50.notifyEmails is empty, so nobody was notified.',
                ['ref' => $pledge['pledge_ref']]
            );

            return;
        }

        $html = view('emails/legacy50_pledge_notification', [
            'pledge' => $pledge,
            'config' => $this->config,
        ]);

        foreach ($this->config->notifyEmails as $email) {
            $this->emails->enqueue([
                'to_email'   => $email,
                'to_name'    => 'LCNL',
                'subject'    => 'New Legacy 50 pledge – ' . $pledge['first_name'] . ' ' . $pledge['last_name'],
                'type'       => 'legacy50_pledge_notification',
                'related_id' => $pledge['id'],
                'body_html'  => $html,
                'body_text'  => html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'priority'   => 1,
            ]);
        }
    }

    /* =====================================================================
       Helpers
    ===================================================================== */

    /** "ha26ng" → "HA2 6NG"; null if it is not a UK postcode. */
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

    private function isOpen(): bool
    {
        return $this->config->isOpen() && ! $this->isFull();
    }

    private function isFull(): bool
    {
        return $this->config->capacity > 0
            && $this->pledges->countActive() >= $this->config->capacity;
    }

    private function closedMessage(): string
    {
        return 'Pledges for this campaign have now closed. Thank you to everyone who took part. '
            . 'If you would still like to give, please contact info@lcnl.org.';
    }

    private function pageData(array $extra = []): array
    {
        return array_merge([
            'title'           => '50 Years of LCNL – 100 Donors, One Lasting Legacy',
            'metaDescription' => 'Help make the Dhamecha Lohana Centre debt-free. Pledge £500, matched by LCNL, '
                . 'and have your name on a commemorative plaque at the Centre.',
            'config'          => $this->config,
            'isOpen'          => $this->isOpen(),
            'closedMessage'   => $this->closedMessage(),
        ], $extra);
    }
}
