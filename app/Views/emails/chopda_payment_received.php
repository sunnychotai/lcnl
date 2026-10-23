<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var array $registration
 * @var \Config\ChopdaPujan $config
 */
?>

<p>Dear <?= esc($registration['first_name']) ?>,</p>

<p>
    We have received your nyochavar of
    <strong>&pound;<?= esc(number_format((float) $registration['amount'])) ?></strong> &ndash; thank you.
    <strong>Your place at Chopda Pujan is confirmed.</strong>
</p>

<table cellpadding="8" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #d4af37; background-color:#fffdf6; margin:20px 0;">
    <tr>
        <td style="width:35%;"><strong>Date</strong></td>
        <td><?= esc($config->eventLabel()) ?></td>
    </tr>
    <tr>
        <td><strong>Registration</strong></td>
        <td><?= esc($config->registrationTimes) ?></td>
    </tr>
    <tr>
        <td><strong>Pooja</strong></td>
        <td><?= esc($config->poojaTimes) ?></td>
    </tr>
    <tr>
        <td><strong>Venue</strong></td>
        <td><?= esc($config->venueLine()) ?></td>
    </tr>
    <tr>
        <td><strong>Your reference</strong></td>
        <td><?= esc($registration['registration_ref']) ?></td>
    </tr>
</table>

<ul style="padding-left:18px;">
    <li>Please arrive during registration so the pooja can start on time.</li>
    <li>All pooja samagri is provided &ndash; please bring your own <strong>book</strong> and
        <strong>swaroop of Lord Ganesh</strong>.</li>
    <li>Only <?= esc($config->yajmansPerPooja) ?> Yajmans can sit in 1 pooja; family and guests are
        welcome to attend and watch.</li>
</ul>

<p>We look forward to welcoming you.</p>

<?= $this->endSection() ?>
