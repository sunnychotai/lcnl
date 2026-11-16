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
    We have received your payment of
    <strong>&pound;<?= esc(number_format((float) $booking['amount'])) ?></strong>, thank you.
    <strong>Your booking for the Christmas Mehfil is confirmed.</strong>
</p>

<table cellpadding="8" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #d4af37; background-color:#fffdf6; margin:20px 0;">
    <tr>
        <td style="width:35%;"><strong>Date</strong></td>
        <td><?= esc($config->eventLabel()) ?></td>
    </tr>
    <tr>
        <td><strong>Time</strong></td>
        <td><?= esc($config->times) ?></td>
    </tr>
    <tr>
        <td><strong>Venue</strong></td>
        <td><?= esc($config->venueLine()) ?></td>
    </tr>
</table>

<?= $this->include('emails/_mehfil_order') ?>

<ul style="padding-left:18px;">
    <li>Dress code: your most festive Christmas outfit or Christmas jumper. There are prizes for the best dressed!</li>
    <li>Please bring cash for the charity raffle (<?= esc(implode(', ', $config->raffle)) ?>).</li>
</ul>

<p>We look forward to welcoming you, your family and friends for a wonderful evening of music, laughter,
    festive fun and celebrations!</p>

<?= $this->endSection() ?>
