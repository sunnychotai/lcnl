<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<?php
/**
 * To the booker once their card payment has gone through: every answer from
 * the form, the event details and the door rules.
 *
 * @var array $booking
 * @var array $attendees
 * @var \Config\Connexions $config
 */
$pound = static fn($n): string => '£' . number_format((float) $n);
$rows  = [
    'First name'  => $booking['first_name'],
    'Surname'     => $booking['last_name'],
    'Email'       => $booking['email'],
    'Mobile'      => $booking['phone'],
    'LCNL member' => ! empty($booking['is_member']) ? 'Yes' : 'No',
    'Eligibility' => 'Confirmed: everyone booked for is a Gujarati single aged 25–40',
    'Girl tickets' => (int) $booking['girl_tickets'],
    'Boy tickets'  => (int) $booking['boy_tickets'],
    'Total paid'   => $pound($booking['amount']) . ' (' . ((int) $booking['girl_tickets'] + (int) $booking['boy_tickets'])
        . ' × ' . $pound($booking['ticket_price']) . ')',
];
?>

<p>Dear <?= esc($booking['first_name']) ?>,</p>

<p>
    <strong>Thank you for booking for Connexions – Singles Mix &amp; Mingle.</strong>
    We have received your payment and your booking is confirmed.
</p>

<?= $this->include('emails/_connexions_event') ?>

<p style="margin-bottom:6px;"><strong>Your details</strong></p>
<table cellpadding="6" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e0e0e0; margin:0 0 20px;">
    <?php foreach ($rows as $label => $value): ?>
        <tr>
            <td style="width:35%;"><strong><?= esc($label) ?></strong></td>
            <td><?= esc($value) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<p style="margin-bottom:6px;"><strong>Attendees</strong></p>
<?= $this->include('emails/_connexions_attendees') ?>

<p>
    Each attendee has also been emailed their own confirmation, which they need to bring on the day.
</p>

<p>
    Your ticket includes a welcome drink and nibbles; other drinks can be bought at the venue.
    For any questions, please contact <?= esc($config->contactsLine()) ?>.
</p>

<p>We look forward to seeing you there!</p>

<?= $this->endSection() ?>
