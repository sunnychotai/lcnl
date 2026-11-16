<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<?php
/**
 * Sent when an admin cancels a booking and leaves "email the booker" ticked.
 *
 * @var array $booking
 * @var \Config\ChristmasMehfil $config
 */
?>

<p>Dear <?= esc($booking['first_name']) ?>,</p>

<p>
    Your booking <strong><?= esc($booking['booking_ref']) ?></strong> for the LCNL Christmas Mehfil on
    <?= esc($config->eventLabel()) ?> has been <strong>cancelled</strong>, as we did not receive your payment
    within <?= esc($config->payWithinHours) ?> hours.
</p>

<p>
    If you have paid, or this is a mistake, please reply to
    <a href="mailto:<?= esc($config->contactEmail, 'attr') ?>"><?= esc($config->contactEmail) ?></a>
    quoting <?= esc($booking['booking_ref']) ?> and we will put it right.
</p>

<?php if ($config->isOpen()): ?>
<p>
    If you would still like to come, you are welcome to
    <a href="<?= esc(site_url($config->slug), 'attr') ?>">book again</a> while tickets are available.
</p>
<?php endif; ?>

<?= $this->endSection() ?>
