<?php

namespace App\Services;

use App\Libraries\StripeService;
use App\Models\ConnexionsAttendeeModel;
use App\Models\ConnexionsBookingModel;
use App\Models\EmailQueueModel;
use Config\Connexions;
use Config\Services;
use Stripe\Checkout\Session;

/**
 * Connexions bookings: holding places, opening Stripe Checkout, marking a
 * booking paid exactly once (from the webhook, the payer's return from
 * Checkout or an admin), and the emails that follow.
 *
 * Once paid, the booker gets every detail of the booking, each attendee gets
 * their own confirmation to show at the door, and the organisers are told.
 */
class ConnexionsBookingService
{
    /** metadata.purpose on our Checkout Sessions; the webhook routes on it. */
    public const PURPOSE = 'connexions';

    protected Connexions $config;
    protected StripeService $stripe;
    protected ConnexionsBookingModel $bookings;
    protected ConnexionsAttendeeModel $attendees;
    protected EmailQueueModel $emails;

    public function __construct(?StripeService $stripe = null)
    {
        $this->config    = config('Connexions');
        $this->stripe    = $stripe ?? Services::stripe();
        $this->bookings  = new ConnexionsBookingModel();
        $this->attendees = new ConnexionsAttendeeModel();
        $this->emails    = new EmailQueueModel();
    }

    /* =====================================================================
       Places
    ===================================================================== */

    /**
     * Places still free, per gender; null means no limit.
     *
     * @return array{girl: int|null, boy: int|null}
     */
    public function placesLeft(?int $exceptId = null): array
    {
        $taken = $this->bookings->placesTaken($exceptId);
        $left  = [];

        foreach (['girl', 'boy'] as $g) {
            $cap      = (int) ($this->config->capacity[$g] ?? 0);
            $left[$g] = $cap > 0 ? max(0, $cap - $taken[$g]) : null;
        }

        return $left;
    }

    public function isFull(): bool
    {
        $left = $this->placesLeft();

        return $left['girl'] === 0 && $left['boy'] === 0;
    }

    /**
     * Why these tickets cannot be had, or null if they can. $left comes from
     * placesLeft().
     */
    public function shortfall(int $girls, int $boys, array $left): ?string
    {
        $problems = [];

        foreach (['girl' => $girls, 'boy' => $boys] as $g => $wanted) {
            if ($wanted > 0 && $left[$g] !== null && $wanted > $left[$g]) {
                $label      = strtolower($this->config->genders[$g]['many']);
                $problems[] = $left[$g] === 0
                    ? 'tickets for ' . $label . ' are sold out'
                    : 'there ' . ($left[$g] === 1 ? 'is only 1 place' : 'are only ' . $left[$g] . ' places') . ' left for ' . $label;
            }
        }

        return $problems === [] ? null : 'Sorry, ' . implode(' and ', $problems) . '.';
    }

    /**
     * A booking that was just saved: did someone else take the last places in
     * the moment between our check and our insert? Bookings made earlier win.
     */
    public function overbooked(array $booking): bool
    {
        $taken = $this->bookings->placesTaken(null, (int) $booking['id']);

        foreach (['girl', 'boy'] as $g) {
            $cap = (int) ($this->config->capacity[$g] ?? 0);
            if ($cap > 0 && (int) $booking[$g . '_tickets'] > 0 && $taken[$g] > $cap) {
                return true;
            }
        }

        return false;
    }

    /* =====================================================================
       Stripe Checkout
    ===================================================================== */

    /**
     * Where to send the payer: an open Checkout for this booking, or a new
     * one. Null when it turns out to be paid already. Throws OverflowException
     * if the booking's hold has run out and its places have gone since, and
     * anything else if Stripe fails.
     */
    public function checkoutUrl(array $booking): ?string
    {
        if (! empty($booking['payment_received'])) {
            return null;
        }

        // Reuse the open session, so two tabs or a double click never become
        // two payments.
        if (! empty($booking['stripe_checkout_session_id'])) {
            try {
                $existing = $this->stripe->retrieveCheckoutSession($booking['stripe_checkout_session_id']);

                if ($existing->payment_status === 'paid') {
                    $this->fulfil($existing);

                    return null;
                }

                if ($existing->status === 'open' && ! empty($existing->url)) {
                    return $existing->url;
                }
            } catch (\Throwable $e) {
                // Gone or from the other mode (test/live): start afresh.
                log_message('warning', 'Connexions: could not reuse checkout {id}: {msg}', [
                    'id' => $booking['stripe_checkout_session_id'], 'msg' => $e->getMessage(),
                ]);
            }
        }

        // The hold has run out: only carry on if the places are still free.
        $held = ! empty($booking['hold_until']) && $booking['hold_until'] > date('Y-m-d H:i:s');
        if (! $held) {
            $problem = $this->shortfall((int) $booking['girl_tickets'], (int) $booking['boy_tickets'],
                $this->placesLeft((int) $booking['id']));

            if ($problem !== null) {
                throw new \OverflowException($problem);
            }
        }

        $base      = site_url($this->config->slug);
        $expiresAt = time() + $this->config->holdMinutes * MINUTE;
        $metadata  = [
            'purpose'     => self::PURPOSE,
            'booking_id'  => (string) $booking['id'],
            'booking_ref' => $booking['booking_ref'],
        ];

        $session = $this->stripe->createCheckoutSession([
            'mode'                => 'payment',
            'line_items'          => [[
                'price'    => config('Stripe')->connexionsPriceId,
                'quantity' => (int) $booking['girl_tickets'] + (int) $booking['boy_tickets'],
            ]],
            'customer_email'      => $booking['email'],
            'client_reference_id' => $booking['booking_ref'],
            'metadata'            => $metadata,
            'payment_intent_data' => [
                'description'                 => $this->config->eventName . ' – ' . $booking['booking_ref'],
                'metadata'                    => $metadata,
                'statement_descriptor_suffix' => $this->config->statementDescriptorSuffix,
            ],
            // The places are held for as long as the Checkout is open. If it
            // expires unpaid, the webhook emails the booker a link to try again.
            'expires_at'  => $expiresAt,
            'success_url' => $base . '/paid?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => $base . '/thank-you',
        ]);

        $this->bookings->update($booking['id'], [
            'stripe_checkout_session_id' => $session->id,
            'hold_until'                 => date('Y-m-d H:i:s', $expiresAt),
        ]);

        return $session->url;
    }

    /** The payer is back from Checkout: settle it now rather than wait for the webhook. */
    public function fulfilById(string $sessionId): ?array
    {
        return $this->fulfil($this->stripe->retrieveCheckoutSession($sessionId));
    }

    /**
     * Record a completed Checkout Session against its booking. Safe to call
     * any number of times. Returns the booking, or null if the session is not
     * one of ours.
     */
    public function fulfil(Session $session): ?array
    {
        $booking = $this->bookingFor($session);

        if (! $booking || $session->payment_status !== 'paid' || ! empty($booking['payment_received'])) {
            return $booking;
        }

        $stripeIds = [
            'stripe_checkout_session_id' => $session->id,
            'stripe_payment_intent_id'   => is_string($session->payment_intent)
                ? $session->payment_intent
                : ($session->payment_intent->id ?? null),
        ];

        $expected = (int) round((float) $booking['amount'] * 100);
        $problem  = match (true) {
            (int) $session->amount_total !== $expected || strtolower((string) $session->currency) !== 'gbp'
                => 'Card payment of ' . strtoupper((string) $session->currency) . ' ' . number_format($session->amount_total / 100, 2)
                    . ' does not match the £' . number_format($expected / 100, 2) . ' due. Check the price in Stripe.',
            $booking['status'] === 'cancelled'
                => 'Paid by card after the booking was cancelled. Refund in Stripe, or reinstate and mark paid.',
            default => null,
        };

        if ($problem !== null) {
            log_message('error', 'Connexions: {ref}: {problem}', ['ref' => $booking['booking_ref'], 'problem' => $problem]);

            $this->bookings->update($booking['id'], $stripeIds + [
                'admin_notes' => trim(($booking['admin_notes'] ?? '') . "\n" . date('j M H:i') . ' – ' . $problem),
            ]);

            return $this->bookings->find($booking['id']);
        }

        $this->markPaid($booking, 'Stripe', $stripeIds);

        return $this->bookings->find($booking['id']);
    }

    /**
     * An unpaid Checkout expired, so its places are free again. Email the
     * booker, once, a link to try again while places last.
     */
    public function expired(Session $session): void
    {
        $booking = $this->bookingFor($session);

        if (! $booking
            || ! empty($booking['payment_received'])
            || $booking['status'] !== 'pending'
            || $booking['stripe_checkout_session_id'] !== $session->id) {
            return;
        }

        $alreadySent = $this->emails
            ->where('type', 'connexions_payment_incomplete')
            ->where('related_id', $booking['id'])
            ->countAllResults() > 0;

        if (! $alreadySent) {
            $this->emailBooker($booking, 'connexions_payment_incomplete',
                'Connexions – your booking was not completed');
        }
    }

    /**
     * Mark a booking paid. The first time ever, the booker, every attendee and
     * the organisers are emailed. Returns whether those emails were queued.
     */
    public function markPaid(array $booking, string $markedBy, array $extra = []): bool
    {
        $now     = date('Y-m-d H:i:s');
        $builder = $this->bookings->builder();

        // Conditional on still being unpaid, so the webhook and the payer's
        // return racing each other cannot both get through.
        $builder->where('id', $booking['id'])
            ->where('payment_received', 0)
            ->update($extra + [
                'payment_received'    => 1,
                'payment_received_at' => $now,
                'payment_marked_by'   => $markedBy,
                'status'              => 'paid',
                'updated_at'          => $now,
            ]);

        if ($builder->db()->affectedRows() !== 1 || ! empty($booking['confirmed_sent_at'])) {
            return false;
        }

        $booking = $this->bookings->find($booking['id']);

        $this->sendConfirmations($booking);
        $this->notifyOrganisers($booking);
        $this->bookings->update($booking['id'], ['confirmed_sent_at' => $now]);

        return true;
    }

    /* =====================================================================
       Emails
    ===================================================================== */

    /**
     * The booker's confirmation (every detail they entered) and one per
     * attendee, each naming only that attendee. An attendee who shares the
     * booker's email is covered by the booker's. Returns how many were queued.
     */
    public function sendConfirmations(array $booking): int
    {
        $attendees = $this->attendees->forBooking((int) $booking['id']);

        $this->emailBooker($booking, 'connexions_booking_confirmed',
            'Connexions – thank you for booking (' . $booking['booking_ref'] . ')', ['attendees' => $attendees]);
        $sent = 1;

        $bookerEmail = strtolower(trim($booking['email']));

        foreach ($attendees as $attendee) {
            if (strtolower(trim($attendee['email'])) === $bookerEmail) {
                continue;
            }

            $html = view('emails/connexions_attendee_ticket', [
                'booking'  => $booking,
                'attendee' => $attendee,
                'config'   => $this->config,
            ]);

            $this->enqueue($attendee['email'], $attendee['first_name'] . ' ' . $attendee['last_name'],
                'Connexions – your place is booked (' . $booking['booking_ref'] . ')',
                'connexions_attendee_ticket', (int) $booking['id'], $html);
            $sent++;
        }

        return $sent;
    }

    /** Queue an email to the booker; the view name doubles as the email type. */
    public function emailBooker(array $booking, string $view, string $subject, array $data = []): void
    {
        $html = view('emails/' . $view, $data + [
            'booking' => $booking,
            'config'  => $this->config,
            'payUrl'  => site_url($this->config->slug . '/pay/' . $booking['pay_token']),
        ]);

        $this->enqueue($booking['email'], $booking['first_name'] . ' ' . $booking['last_name'],
            $subject, $view, (int) $booking['id'], $html);
    }

    public function notifyOrganisers(array $booking): void
    {
        if ($this->config->notifyEmails === []) {
            log_message('warning',
                'Connexions: booking {ref} paid but connexions.notifyEmails is empty, so nobody was notified.',
                ['ref' => $booking['booking_ref']]
            );

            return;
        }

        $html = view('emails/connexions_booking_notification', [
            'booking'   => $booking,
            'attendees' => $this->attendees->forBooking((int) $booking['id']),
            'config'    => $this->config,
        ]);

        foreach ($this->config->notifyEmails as $email) {
            $this->enqueue($email, 'LCNL',
                'New Connexions booking – ' . $booking['first_name'] . ' ' . $booking['last_name']
                    . ' (' . $this->config->ticketsLabel((int) $booking['girl_tickets'], (int) $booking['boy_tickets']) . ')',
                'connexions_booking_notification', (int) $booking['id'], $html);
        }
    }

    private function enqueue(string $to, string $name, string $subject, string $type, int $bookingId, string $html): void
    {
        $this->emails->enqueue([
            'to_email'   => $to,
            'to_name'    => $name,
            'subject'    => $subject,
            'type'       => $type,
            'related_id' => $bookingId,
            'body_html'  => $html,
            'body_text'  => html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'priority'   => 1,
        ]);
    }

    private function bookingFor(Session $session): ?array
    {
        if (($session->metadata->purpose ?? '') !== self::PURPOSE) {
            return null;
        }

        $booking = $this->bookings->find((int) ($session->metadata->booking_id ?? 0));

        if (! $booking) {
            log_message('error', 'Connexions: checkout {id} names booking {booking}, which does not exist.', [
                'id' => $session->id, 'booking' => $session->metadata->booking_id ?? '?',
            ]);
        }

        return $booking;
    }
}
