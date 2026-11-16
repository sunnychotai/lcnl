<?php

namespace App\Services;

use App\Models\EmailQueueModel;
use App\Models\MehfilBookingModel;
use Config\ChristmasMehfil;

/**
 * Christmas Mehfil booking emails and payment state, shared by the public and
 * admin controllers. Emails go through the queue like every other email.
 */
class MehfilBookingService
{
    protected ChristmasMehfil $config;
    protected MehfilBookingModel $bookings;
    protected EmailQueueModel $emails;

    public function __construct()
    {
        $this->config   = config('ChristmasMehfil');
        $this->bookings = new MehfilBookingModel();
        $this->emails   = new EmailQueueModel();
    }

    /**
     * Record that the transfer is on LCNL's statement. The first time a
     * booking is marked paid, the booker is told it is confirmed. Returns
     * whether that email was queued.
     */
    public function markPaid(array $booking, string $markedBy): bool
    {
        $now     = date('Y-m-d H:i:s');
        $builder = $this->bookings->builder();

        // Conditional on still being unpaid, so a double click cannot send two emails.
        $builder->where('id', $booking['id'])
            ->where('payment_received', 0)
            ->update([
                'payment_received'    => 1,
                'payment_received_at' => $now,
                'payment_marked_by'   => $markedBy,
                'status'              => 'paid',
                'updated_at'          => $now,
            ]);

        if ($builder->db()->affectedRows() !== 1 || ! empty($booking['confirmed_sent_at'])) {
            return false;
        }

        $this->email($booking, 'mehfil_payment_received', 'Christmas Mehfil – your booking is confirmed');
        $this->bookings->update($booking['id'], ['confirmed_sent_at' => $now]);

        return true;
    }

    /** Queue an email to the booker; the view name doubles as the email type. */
    public function email(array $booking, string $view, string $subject): void
    {
        $html = view('emails/' . $view, ['booking' => $booking, 'config' => $this->config]);

        $this->emails->enqueue([
            'to_email'   => $booking['email'],
            'to_name'    => $booking['first_name'] . ' ' . $booking['last_name'],
            'subject'    => $subject,
            'type'       => $view,
            'related_id' => $booking['id'],
            'body_html'  => $html,
            'body_text'  => html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'priority'   => 1,
        ]);
    }

    public function notifyOrganisers(array $booking): void
    {
        if ($this->config->notifyEmails === []) {
            log_message('warning',
                'Mehfil: booking {ref} saved but mehfil.notifyEmails is empty, so nobody was notified.',
                ['ref' => $booking['booking_ref']]
            );

            return;
        }

        $html = view('emails/mehfil_booking_notification', ['booking' => $booking, 'config' => $this->config]);

        foreach ($this->config->notifyEmails as $email) {
            $this->emails->enqueue([
                'to_email'   => $email,
                'to_name'    => 'LCNL',
                'subject'    => 'New Christmas Mehfil booking – ' . $booking['first_name'] . ' ' . $booking['last_name']
                    . ' (£' . number_format((float) $booking['amount']) . ')',
                'type'       => 'mehfil_booking_notification',
                'related_id' => $booking['id'],
                'body_html'  => $html,
                'body_text'  => html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'priority'   => 1,
            ]);
        }
    }

    /** "2 individual tickets and 1 table of 10" */
    public static function ticketsLabel(array $booking, int $tableSize): string
    {
        $parts = [];
        $ind   = (int) $booking['individual_tickets'];
        $tab   = (int) $booking['tables'];

        if ($ind > 0) {
            $parts[] = $ind . ' individual ticket' . ($ind === 1 ? '' : 's');
        }
        if ($tab > 0) {
            $parts[] = $tab . ' table' . ($tab === 1 ? '' : 's') . ' of ' . $tableSize;
        }

        return implode(' and ', $parts);
    }
}
