<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<?php
/**
 * Sent as soon as someone books: the bank details and their reference.
 *
 * @var array $booking
 * @var \Config\ChristmasMehfil $config
 */
?>

<p>Dear <?= esc($booking['first_name']) ?>,</p>

<p>
    Thank you for booking for the <strong>LCNL Christmas Mehfil</strong> on
    <strong><?= esc($config->eventLabel()) ?></strong>, <?= esc($config->times) ?>, at the
    <strong><?= esc($config->venueLine()) ?></strong>.
</p>

<p>
    Please transfer <strong>&pound;<?= esc(number_format((float) $booking['amount'])) ?></strong> to the LCNL
    account below, using your reference <strong style="white-space:nowrap;"><?= esc($booking['booking_ref']) ?></strong> so we can match
    your payment to your booking.
</p>

<?= $this->include('emails/_mehfil_bank') ?>

<p style="background-color:#fdecea; border-left:4px solid #b42318; padding:10px 12px; color:#7a1414;">
    <strong>Payment must be made within <?= esc($config->payWithinHours) ?> hours to confirm your booking
    (by <?= esc($config->payByLabel($booking['created_at'])) ?>). If payment isn&rsquo;t made, your order will
    be cancelled.</strong>
</p>

<p>Your booking is only confirmed once your payment has been received. We will email you again when it arrives.</p>

<?= $this->include('emails/_mehfil_order') ?>

<p style="margin-bottom:6px;"><strong>On the night</strong></p>
<ul style="margin-top:0; padding-left:18px;">
    <li>Live music by RKB Entertainment; vegetarian dinner by Desi Delite and drinks included.</li>
    <li>Dress code: your most festive Christmas outfit or Christmas jumper. There are prizes for the best dressed!</li>
    <li>Please bring cash for the charity raffle (<?= esc(implode(', ', $config->raffle)) ?>).</li>
</ul>

<p>
    For any questions, please reply to <a href="mailto:<?= esc($config->contactEmail, 'attr') ?>"><?= esc($config->contactEmail) ?></a>
    quoting <?= esc($booking['booking_ref']) ?>.
</p>

<?= $this->endSection() ?>
