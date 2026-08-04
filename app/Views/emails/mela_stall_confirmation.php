<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<?php /** @var \Config\MelaStalls $config */ ?>

<p>Dear <?= esc($booking['contact_name']) ?>,</p>

<p>
    Thank you for completing the stall holder booking form for the
    <strong><?= esc($config->eventName) ?></strong>, taking place on
    <strong><?= esc($config->eventLabel()) ?></strong> at
    <strong><?= esc($config->venue['name']) ?></strong>,
    <?= esc($config->venue['address1']) ?>, <?= esc($config->venue['address2']) ?>,
    <?= esc($config->venue['postcode']) ?> &ndash; <?= esc($config->eventTimes) ?>.
</p>

<p>
    Your booking reference is
    <strong style="color:#7a1d3c;"><?= esc($booking['booking_ref']) ?></strong>
    and your stall is registered as
    <strong><?= esc($booking['company_name']) ?></strong>.
</p>

<p>
    Please ensure you have made payment to the LCNL bank account in order to confirm your
    stall. If you have not paid in advance, we won&rsquo;t be able to confirm your stall.
</p>

<!-- Bank details: repeated here because this is the point at which they are needed -->
<table cellpadding="8" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e0d0d5;
           background-color:#fdfbfa; margin:20px 0;">
    <tr style="background-color:#f5eaed;">
        <td colspan="2" style="font-weight:bold; font-size:15px; padding:10px 8px;">
            Payment details &ndash; &pound;<?= esc($config->fee) ?>
        </td>
    </tr>
    <tr>
        <td style="width:40%;"><strong>Account name</strong></td>
        <td><?= esc($config->bank['accountName']) ?></td>
    </tr>
    <tr>
        <td><strong>Account number</strong></td>
        <td><?= esc($config->bank['accountNumber']) ?></td>
    </tr>
    <tr>
        <td><strong>Sort code</strong></td>
        <td><?= esc($config->bank['sortCode']) ?></td>
    </tr>
    <tr>
        <td><strong>Reference</strong></td>
        <td style="color:#7a1d3c; font-weight:bold;">
            <?= esc($config->paymentReference($booking['company_name'])) ?>
        </td>
    </tr>
</table>

<p style="margin-bottom:6px;"><strong>On the day</strong></p>
<ul style="margin-top:0;">
    <li>Set-up from <strong><?= esc($config->setUpFrom) ?></strong>.</li>
    <li>
        One vehicle may enter the main car park during set-up, but must be moved to the
        grass parking area by <strong><?= esc($config->carParkClear) ?></strong>.
    </li>
    <li>
        Your pitch is approximately <strong><?= esc($config->stallSize) ?></strong>.
        No equipment is provided &mdash; please bring your own table, gazebo, chairs and
        anything else you need.
    </li>
    <?php if (! empty($booking['is_food_stall'])): ?>
        <li>
            As a food stall, please bring a copy of your
            <strong>Food Hygiene Certificate</strong>; you may be asked to present it.
        </li>
    <?php endif; ?>
    <li>You may be asked to sign an additional disclaimer on the day.</li>
</ul>

<p>
    If you have any questions or want more information, you can call
    <?php foreach ($config->contacts as $i => $contact): ?>
        <?= $i > 0 ? ' or ' : '' ?><?= esc($contact['name']) ?> &ndash; <?= esc($contact['phone']) ?><?php endforeach; ?>.
</p>

<?= $this->endSection() ?>
