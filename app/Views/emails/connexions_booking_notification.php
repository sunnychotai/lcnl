<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<?php
/**
 * To the organisers when a booking has been paid.
 *
 * @var array $booking
 * @var array $attendees
 * @var \Config\Connexions $config
 */
$rows = [
    'Booked by'   => $booking['first_name'] . ' ' . $booking['last_name'],
    'Email'       => $booking['email'],
    'Mobile'      => $booking['phone'],
    'LCNL member' => ! empty($booking['is_member']) ? 'Yes' : 'No',
    'Tickets'     => $config->ticketsLabel((int) $booking['girl_tickets'], (int) $booking['boy_tickets']),
    'Paid'        => '£' . number_format((float) $booking['amount']) . ' by card (' . $booking['payment_marked_by'] . ')',
];
?>

<p><strong>A new Connexions booking has been paid.</strong></p>

<table cellpadding="8" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e0d0d5; background-color:#fdfbfa; margin:20px 0;">
    <tr style="background-color:#f5eaed;">
        <td colspan="2" style="font-weight:bold; font-size:15px; padding:10px 8px;">
            <?= esc($booking['booking_ref']) ?> &ndash; &pound;<?= esc(number_format((float) $booking['amount'])) ?>
        </td>
    </tr>
    <?php foreach ($rows as $label => $value): ?>
        <tr>
            <td style="width:38%;"><strong><?= esc($label) ?></strong></td>
            <td><?= esc($value) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?= $this->include('emails/_connexions_attendees') ?>

<p>
    <a href="<?= base_url('admin/connexions') ?>"
        style="background-color:#7a1d3c; color:#ffffff; padding:10px 18px;
              text-decoration:none; border-radius:4px; display:inline-block;">
        View all bookings
    </a>
</p>

<?= $this->endSection() ?>
