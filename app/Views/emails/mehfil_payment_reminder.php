<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var array $booking
 * @var \Config\ChristmasMehfil $config
 */
?>

<p>Dear <?= esc($booking['first_name']) ?>,</p>

<p>
    Thank you for booking for the <strong>LCNL Christmas Mehfil</strong> on
    <strong><?= esc($config->eventLabel()) ?></strong>.
</p>

<p>
    <strong>We have not yet received your payment, so your booking is not confirmed.</strong>
    Please transfer &pound;<?= esc(number_format((float) $booking['amount'])) ?> as soon as possible,
    using your reference so we can match it. If we do not receive it, your order will be cancelled.
</p>

<?= $this->include('emails/_mehfil_bank') ?>

<p>
    If you have already paid, thank you. Please reply to
    <a href="mailto:<?= esc($config->contactEmail, 'attr') ?>"><?= esc($config->contactEmail) ?></a> with the date and
    reference you used, quoting <?= esc($booking['booking_ref']) ?>, and we will look into it.
</p>

<?= $this->endSection() ?>
