<?php

namespace App\Services;

use App\Libraries\StripeService;
use App\Models\ChopdaRegistrationModel;
use App\Models\EmailQueueModel;
use Config\ChopdaPujan;
use Config\Services;
use Stripe\Checkout\Session;

/**
 * Chopda Pujan payments: opening Stripe Checkout for a registration, and
 * marking a registration paid exactly once, whether that comes from the Stripe
 * webhook, the payer's return from Checkout or an admin matching a transfer.
 */
class ChopdaPaymentService
{
    /** metadata.purpose on our Checkout Sessions; the webhook routes on it. */
    public const PURPOSE = 'chopda_pujan';

    protected ChopdaPujan $config;
    protected StripeService $stripe;
    protected ChopdaRegistrationModel $registrations;
    protected EmailQueueModel $emails;

    public function __construct(?StripeService $stripe = null)
    {
        $this->config        = config('ChopdaPujan');
        $this->stripe        = $stripe ?? Services::stripe();
        $this->registrations = new ChopdaRegistrationModel();
        $this->emails        = new EmailQueueModel();
    }

    /**
     * Where to send the payer: an open Checkout for this registration, or a new
     * one. Null when it turns out to be paid already. Throws if Stripe fails.
     */
    public function checkoutUrl(array $registration): ?string
    {
        if (! empty($registration['payment_received'])) {
            return null;
        }

        // Reuse the open session, so two tabs or a double click never become
        // two payments.
        if (! empty($registration['stripe_checkout_session_id'])) {
            try {
                $existing = $this->stripe->retrieveCheckoutSession($registration['stripe_checkout_session_id']);

                if ($existing->payment_status === 'paid') {
                    $this->fulfil($existing);

                    return null;
                }

                if ($existing->status === 'open' && ! empty($existing->url)) {
                    return $existing->url;
                }
            } catch (\Throwable $e) {
                // Gone or from the other mode (test/live): start afresh.
                log_message('warning', 'Chopda: could not reuse checkout {id}: {msg}', [
                    'id' => $registration['stripe_checkout_session_id'], 'msg' => $e->getMessage(),
                ]);
            }
        }

        $base     = site_url($this->config->slug);
        $metadata = [
            'purpose'          => self::PURPOSE,
            'registration_id'  => (string) $registration['id'],
            'registration_ref' => $registration['registration_ref'],
        ];

        $session = $this->stripe->createCheckoutSession([
            'mode'                => 'payment',
            'line_items'          => [['price' => config('Stripe')->chopdaPriceId, 'quantity' => 1]],
            'customer_email'      => $registration['email'],
            'client_reference_id' => $registration['registration_ref'],
            'metadata'            => $metadata,
            'payment_intent_data' => [
                'description'                 => $this->config->eventName . ' – ' . $registration['registration_ref'],
                'metadata'                    => $metadata,
                'statement_descriptor_suffix' => $this->config->statementDescriptorSuffix,
            ],
            // An abandoned payment expires after an hour, and the webhook then
            // emails the registrant a link to finish it.
            'expires_at'  => time() + HOUR,
            'success_url' => $base . '/paid?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => $base . '/thank-you',
        ]);

        $this->registrations->update($registration['id'], ['stripe_checkout_session_id' => $session->id]);

        return $session->url;
    }

    /** The payer is back from Checkout: settle it now rather than wait for the webhook. */
    public function fulfilById(string $sessionId): ?array
    {
        return $this->fulfil($this->stripe->retrieveCheckoutSession($sessionId));
    }

    /**
     * Record a completed Checkout Session against its registration. Safe to
     * call any number of times. Returns the registration, or null if the
     * session is not one of ours.
     */
    public function fulfil(Session $session): ?array
    {
        $registration = $this->registrationFor($session);

        if (! $registration || $session->payment_status !== 'paid' || ! empty($registration['payment_received'])) {
            return $registration;
        }

        $stripeIds = [
            'payment_method'             => 'card',
            'stripe_checkout_session_id' => $session->id,
            'stripe_payment_intent_id'   => is_string($session->payment_intent)
                ? $session->payment_intent
                : ($session->payment_intent->id ?? null),
        ];

        $expected = (int) round((float) $registration['amount'] * 100);
        $problem  = match (true) {
            (int) $session->amount_total !== $expected || strtolower((string) $session->currency) !== 'gbp'
                => 'Card payment of ' . strtoupper((string) $session->currency) . ' ' . number_format($session->amount_total / 100, 2)
                    . ' does not match the £' . number_format($expected / 100, 2) . ' due. Check the price in Stripe.',
            $registration['status'] === 'cancelled'
                => 'Paid by card after the registration was cancelled. Refund in Stripe, or reinstate and mark paid.',
            default => null,
        };

        if ($problem !== null) {
            log_message('error', 'Chopda: {ref}: {problem}', ['ref' => $registration['registration_ref'], 'problem' => $problem]);

            $this->registrations->update($registration['id'], $stripeIds + [
                'admin_notes' => trim(($registration['admin_notes'] ?? '') . "\n" . date('j M H:i') . ' – ' . $problem),
            ]);

            return $this->registrations->find($registration['id']);
        }

        $this->markPaid($registration, 'Stripe', $stripeIds);

        return $this->registrations->find($registration['id']);
    }

    /**
     * An unpaid Checkout expired: send the registrant the "please complete your
     * payment" email with a link back, once.
     */
    public function expired(Session $session): void
    {
        $registration = $this->registrationFor($session);

        if (! $registration
            || ! empty($registration['payment_received'])
            || $registration['status'] === 'cancelled'
            || $registration['stripe_checkout_session_id'] !== $session->id) {
            return;
        }

        $alreadySent = $this->emails
            ->where('type', 'chopda_registration_confirmation')
            ->where('related_id', $registration['id'])
            ->countAllResults() > 0;

        if (! $alreadySent) {
            $this->email($registration, 'chopda_registration_confirmation', 'Chopda Pujan 2026 – please complete your payment');
        }
    }

    /**
     * Mark a registration paid. The first time ever, the registrant is told
     * their place is confirmed. Returns whether that email was queued.
     */
    public function markPaid(array $registration, string $markedBy, array $extra = []): bool
    {
        $now     = date('Y-m-d H:i:s');
        $builder = $this->registrations->builder();

        // Conditional on still being unpaid, so the webhook and the payer's
        // return racing each other cannot both get through.
        $builder->where('id', $registration['id'])
            ->where('payment_received', 0)
            ->update($extra + [
                'payment_received'    => 1,
                'payment_received_at' => $now,
                'payment_marked_by'   => $markedBy,
                'status'              => 'paid',
                'updated_at'          => $now,
            ]);

        if ($builder->db()->affectedRows() !== 1 || ! empty($registration['confirmed_sent_at'])) {
            return false;
        }

        $this->email($extra + $registration, 'chopda_payment_received', 'Chopda Pujan – your place is confirmed');
        $this->registrations->update($registration['id'], ['confirmed_sent_at' => $now]);

        return true;
    }

    /** Queue an email to the registrant; the view name doubles as the email type. */
    public function email(array $registration, string $view, string $subject): void
    {
        $html = view('emails/' . $view, ['registration' => $registration, 'config' => $this->config]);

        $this->emails->enqueue([
            'to_email'   => $registration['email'],
            'to_name'    => $registration['first_name'] . ' ' . $registration['last_name'],
            'subject'    => $subject,
            'type'       => $view,
            'related_id' => $registration['id'],
            'body_html'  => $html,
            'body_text'  => html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'priority'   => 1,
        ]);
    }

    private function registrationFor(Session $session): ?array
    {
        if (($session->metadata->purpose ?? '') !== self::PURPOSE) {
            return null;
        }

        $registration = $this->registrations->find((int) ($session->metadata->registration_id ?? 0));

        if (! $registration) {
            log_message('error', 'Chopda: checkout {id} names registration {reg}, which does not exist.', [
                'id' => $session->id, 'reg' => $session->metadata->registration_id ?? '?',
            ]);
        }

        return $registration;
    }
}
