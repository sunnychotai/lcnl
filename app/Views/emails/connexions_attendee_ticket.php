<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<?php
/**
 * To each attendee (other than the booker) once the booking is paid: their
 * own confirmation to show at the door.
 *
 * @var array $booking
 * @var array $attendee
 * @var \Config\Connexions $config
 */
?>

<p>Dear <?= esc($attendee['first_name']) ?>,</p>

<p>
    <strong><?= esc($booking['first_name'] . ' ' . $booking['last_name']) ?></strong> has booked you a place at
    <strong>Connexions – Singles Mix &amp; Mingle</strong>, and it has been paid for. Thank you for booking!
</p>

<?= $this->include('emails/_connexions_event') ?>

<p style="margin-bottom:6px;"><strong>Your ticket</strong></p>
<table cellpadding="6" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e0e0e0; margin:0 0 20px;">
    <tr>
        <td style="width:35%;"><strong>Ticket</strong></td>
        <td><?= esc($config->genders[$attendee['gender']]['one']) ?></td>
    </tr>
    <tr>
        <td><strong>Name</strong></td>
        <td><?= esc($attendee['first_name'] . ' ' . $attendee['last_name']) ?></td>
    </tr>
    <tr>
        <td><strong>Email</strong></td>
        <td><?= esc($attendee['email']) ?></td>
    </tr>
    <tr>
        <td><strong>Mobile</strong></td>
        <td><?= esc($attendee['phone']) ?></td>
    </tr>
</table>

<p>
    This event is exclusively for <?= esc($config->eligibility) ?>. Your ticket includes a welcome drink and nibbles;
    other drinks can be bought at the venue. For any questions, please contact <?= esc($config->contactsLine()) ?>.
</p>

<p>We look forward to seeing you there!</p>

<?= $this->endSection() ?>
