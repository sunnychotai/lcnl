<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<?php
/** @var \Config\MelaStalls $config */
$categoryLabel = $config->categories[$booking['category']] ?? $booking['category'];

if ($booking['category'] === 'other' && ! empty($booking['category_other'])) {
    $categoryLabel .= ': ' . $booking['category_other'];
}
?>

<p><strong>A new stall has been booked for the Golden Jubilee Mela.</strong></p>

<table cellpadding="8" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e0d0d5;
           background-color:#fdfbfa; margin:20px 0;">
    <tr style="background-color:#f5eaed;">
        <td colspan="2" style="font-weight:bold; font-size:15px; padding:10px 8px;">
            <?= esc($booking['company_name']) ?> &ndash; <?= esc($booking['booking_ref']) ?>
        </td>
    </tr>
    <tr>
        <td style="width:38%;"><strong>Stall type</strong></td>
        <td><?= esc($categoryLabel) ?></td>
    </tr>
    <tr>
        <td><strong>Food stall</strong></td>
        <td><?= ! empty($booking['is_food_stall']) ? 'Yes' : 'No' ?></td>
    </tr>
    <tr>
        <td><strong>Items sold</strong></td>
        <td><?= nl2br(esc($booking['items_description'])) ?></td>
    </tr>
    <tr>
        <td><strong>Contact name</strong></td>
        <td><?= esc($booking['contact_name']) ?></td>
    </tr>
    <tr>
        <td><strong>Phone</strong></td>
        <td><?= esc($booking['contact_phone']) ?></td>
    </tr>
    <tr>
        <td><strong>Email</strong></td>
        <td><?= esc($booking['contact_email']) ?></td>
    </tr>
    <?php if (! empty($booking['vehicle_reg'])): ?>
        <tr>
            <td><strong>Vehicle</strong></td>
            <td><?= esc($booking['vehicle_reg']) ?></td>
        </tr>
    <?php endif; ?>
    <tr>
        <td><strong>Says fee is paid</strong></td>
        <td><?= ! empty($booking['confirmed_payment']) ? 'Yes (needs checking on the statement)' : 'No' ?></td>
    </tr>
    <tr>
        <td><strong>Payment reference to look for</strong></td>
        <td><?= esc($config->paymentReference($booking['company_name'])) ?></td>
    </tr>
    <tr>
        <td><strong>Documents uploaded</strong></td>
        <td>
            <?= (int) $documentCount ?>
            <?php if (! empty($booking['is_food_stall']) && (int) $documentCount === 0): ?>
                &ndash; <strong style="color:#b3261e;">food stall with no certificate</strong>
            <?php endif; ?>
        </td>
    </tr>
    <?php if (! empty($booking['comments'])): ?>
        <tr>
            <td><strong>Comments</strong></td>
            <td><?= nl2br(esc($booking['comments'])) ?></td>
        </tr>
    <?php endif; ?>
</table>

<p>
    <a href="<?= base_url('admin/content/mela-stalls') ?>"
        style="background-color:#7a1d3c; color:#ffffff; padding:10px 18px;
              text-decoration:none; border-radius:4px; display:inline-block;">
        View all stall bookings
    </a>
</p>

<p style="color:#666; font-size:13px;">
    Uploaded documents can be viewed from the admin screen. Remember to tick
    &ldquo;payment received&rdquo; once you have matched the transfer on the bank statement.
</p>

<?= $this->endSection() ?>
