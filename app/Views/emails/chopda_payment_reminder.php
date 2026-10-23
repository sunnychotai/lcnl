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
    Thank you for registering for <strong>Chopda Pujan</strong> on
    <strong><?= esc($config->eventLabel()) ?></strong>.
</p>

<p>
    We have not yet been able to match your nyochavar on the LCNL bank statement, so your place is
    not yet confirmed. If you have not made the transfer, the details are below &ndash; please use the
    reference shown so we can match it.
</p>

<?= $this->include('emails/_chopda_bank') ?>

<p>
    If you have already paid, thank you &mdash; please reply to
    <a href="mailto:info@lcnl.org">info@lcnl.org</a> with the date and reference you used, quoting
    <?= esc($registration['registration_ref']) ?>, and we will look into it.
</p>

<?= $this->endSection() ?>
