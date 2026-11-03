<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<?php
/**
 * Sent at registration for a bank transfer, or when a card payment was started
 * but not completed.
 *
 * @var array $registration
 * @var \Config\ChopdaPujan $config
 */
$isCard   = $registration['payment_method'] === 'card';
$showBank = $config->acceptsBank() || ! $isCard;
?>

<p>Dear <?= esc($registration['first_name']) ?>,</p>

<p>
    Thank you for registering for <strong>Chopda Pujan</strong> on
    <strong><?= esc($config->eventLabel()) ?></strong> at the
    <strong><?= esc($config->venueLine()) ?></strong>.
</p>

<?php if ($isCard): ?>
<p>
    <strong>Your card payment was not completed, so your registration is pending.</strong> Please pay the
    &pound;<?= esc(number_format((float) $registration['amount'])) ?> nyochavar by
    <strong><?= esc($config->closingLabel()) ?></strong> to confirm your place.
</p>

<?= $this->include('emails/_chopda_pay_button') ?>

<?php if ($showBank): ?>
<p>Or, if you prefer, pay by bank transfer using the reference below so we can match it.</p>
<?php endif; ?>
<?php else: ?>
<p>
    <strong>Your registration is pending until we receive your payment.</strong> Please transfer the
    &pound;<?= esc(number_format((float) $registration['amount'])) ?> nyochavar by
    <strong><?= esc($config->closingLabel()) ?></strong>, using the reference below so we can match it.
</p>
<?php endif; ?>

<?php if ($showBank): ?>
<?= $this->include('emails/_chopda_bank') ?>
<?php endif; ?>

<?php if (! $isCard && $config->acceptsCard()): ?>
<p>Prefer to pay by card? Use the button below instead &ndash; there is no need to do both.</p>
<?= $this->include('emails/_chopda_pay_button') ?>
<?php endif; ?>

<p>We will email you again once your payment has arrived to confirm your place.</p>

<p style="margin-bottom:6px;"><strong>Your registration</strong></p>
<table cellpadding="6" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e0e0e0; margin:0 0 20px;">
    <tr>
        <td style="width:40%;"><strong>Registration ref</strong></td>
        <td><?= esc($registration['registration_ref']) ?></td>
    </tr>
    <tr>
        <td><strong>Name</strong></td>
        <td><?= esc($registration['first_name'] . ' ' . $registration['last_name']) ?></td>
    </tr>
    <tr>
        <td><strong>Mobile</strong></td>
        <td><?= esc($registration['phone']) ?></td>
    </tr>
    <tr>
        <td><strong>Attending</strong></td>
        <td><?= esc($registration['num_attendees']) ?></td>
    </tr>
    <tr>
        <td><strong>Nyochavar</strong></td>
        <td>&pound;<?= esc(number_format((float) $registration['amount'])) ?> (1 pooja)</td>
    </tr>
</table>

<p style="margin-bottom:6px;"><strong>On the day</strong></p>
<ul style="margin-top:0; padding-left:18px;">
    <li>Registration <?= esc($config->registrationTimes) ?>; pooja <?= esc($config->poojaTimes) ?>.</li>
    <li>All pooja samagri is provided &ndash; please bring your own <strong>book</strong> and
        <strong>swaroop of Lord Ganesh</strong>.</li>
    <li style="color:#b42318;"><strong>Only <?= esc($config->yajmansPerPooja) ?> Yajmans can sit in 1 pooja.</strong>
        Others are welcome to attend but won&rsquo;t be able to participate in the pooja.</li>
    <li>The pooja will be conducted by <?= esc($config->conductedBy) ?>.</li>
</ul>

<p>
    For any questions, please call
    <?php foreach ($config->contacts as $i => $contact): ?>
        <?= $i > 0 ? ' or ' : '' ?><?= esc($contact['name']) ?> on <?= esc($contact['phone']) ?><?php endforeach; ?>,
    or reply to <a href="mailto:info@lcnl.org">info@lcnl.org</a> quoting <?= esc($registration['registration_ref']) ?>.
</p>

<?= $this->endSection() ?>
