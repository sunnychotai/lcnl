<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var array $pledge
 * @var \Config\Legacy50 $config
 */
?>

<p>Dear <?= esc($pledge['first_name']) ?>,</p>

<p>
    Thank you again for pledging to <strong>50 Years of LCNL &ndash; 100 Donors, One Lasting
    Legacy</strong> on <?= esc(date('j F Y', strtotime($pledge['created_at']))) ?>.
</p>

<p>
    We have not yet been able to match your donation on the Lohana Charitable Foundation bank
    statement. If you have not made the transfer yet, the details are below. Please use the
    reference shown so we can match it.
</p>

<?= $this->include('emails/_legacy50_bank') ?>

<p>
    If you have already paid, thank you &mdash; please reply to
    <a href="mailto:info@lcnl.org">info@lcnl.org</a> with the date and reference you used, quoting
    <?= esc($pledge['pledge_ref']) ?>, and we will look into it.
</p>

<p>We are aiming to clear the loan by <strong><?= esc($config->targetLabel()) ?></strong>.</p>

<?= $this->endSection() ?>
