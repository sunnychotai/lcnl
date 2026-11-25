<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<?php
/**
 * To the booker when their Stripe Checkout expired unpaid.
 *
 * @var array  $booking
 * @var string $payUrl
 * @var \Config\Connexions $config
 */
?>

<p>Dear <?= esc($booking['first_name']) ?>,</p>

<p>
    You started booking <?= esc($config->ticketsLabel((int) $booking['girl_tickets'], (int) $booking['boy_tickets'])) ?>
    for <strong>Connexions – Singles Mix &amp; Mingle</strong> on <?= esc($config->eventLabel()) ?>,
    but <strong>the card payment was not completed</strong>, so your booking is not confirmed and you have not been charged.
</p>

<p>
    Places are limited and are only held while you pay. If you would still like to come, please complete your
    booking by <strong><?= esc($config->closingLabel()) ?></strong> while places last:
</p>

<p style="text-align:center; margin:24px 0;">
    <a href="<?= esc($payUrl) ?>"
        style="background-color:#7a1d3c; color:#ffffff; padding:12px 24px; font-weight:bold;
              text-decoration:none; border-radius:4px; display:inline-block;">
        Pay &pound;<?= esc(number_format((float) $booking['amount'])) ?> by card
    </a>
    <br><span style="color:#666; font-size:13px;">Secure payment by Stripe &ndash; your booking is confirmed straight away.</span>
</p>
<p style="color:#666; font-size:12px; text-align:center; word-break:break-all;">
    If the button does not work, open this link: <?= esc($payUrl) ?>
</p>

<p>
    If you don&rsquo;t want to go ahead, you don&rsquo;t need to do anything. Reference: <?= esc($booking['booking_ref']) ?>.
</p>

<?= $this->endSection() ?>
