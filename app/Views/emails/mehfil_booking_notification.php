<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var array $booking
 * @var \Config\ChristmasMehfil $config
 */

$rows = [
    'Name'        => $booking['first_name'] . ' ' . $booking['last_name'],
    'Email'       => $booking['email'],
    'Mobile'      => $booking['phone'],
    'Postcode'    => $booking['postcode'],
    'LCNL member' => ! empty($booking['is_member']) ? 'Yes' : 'No',
    'Tickets'     => \App\Services\MehfilBookingService::ticketsLabel($booking, $config->tableSize),
    'People'      => $config->people((int) $booking['individual_tickets'], (int) $booking['tables']),
    'Price'       => $booking['price_band'] === 'early' ? 'Early bird' : 'Standard',
    'Pay by'      => $config->payByLabel($booking['created_at']),
];
?>

<p><strong>A new Christmas Mehfil booking has been made.</strong></p>

<table cellpadding="8" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e0d0d5;
           background-color:#fdfbfa; margin:20px 0;">
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

<p>
    <a href="<?= base_url('admin/christmas-mehfil') ?>"
        style="background-color:#7a1d3c; color:#ffffff; padding:10px 18px;
              text-decoration:none; border-radius:4px; display:inline-block;">
        View all bookings
    </a>
</p>

<p style="color:#666; font-size:13px;">
    Mark the booking as paid once the transfer appears on the LCNL bank statement under the reference above.
    That sends the booker an email confirming their booking. Bookings still unpaid after
    <?= esc($config->payWithinHours) ?> hours are highlighted on the admin page, ready to cancel.
</p>

<?= $this->endSection() ?>
