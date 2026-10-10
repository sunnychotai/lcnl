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
    We have received your donation of
    <strong>&pound;<?= esc(number_format((float) $pledge['amount'])) ?></strong>
    &ndash; thank you!
</p>

<p>
    With LCNL&rsquo;s matching contribution, that is
    <strong>&pound;<?= esc(number_format((float) $pledge['amount'] + $config->match)) ?></strong>
    towards clearing the loan on the Dhamecha Lohana Centre<?= $pledge['gift_aid'] ? ', and LCF will claim Gift Aid on your donation as you asked' : '' ?>.
</p>

<p>
    Once the loan is fully repaid, your name will appear on the commemorative plaque at the Centre as:
</p>

<p style="text-align:center; font-size:18px; font-weight:bold; color:#7a1d3c;
          border:1px solid #d4af37; background:#fffdf6; padding:14px; margin:16px 0;">
    <?= esc($pledge['plaque_name']) ?>
</p>

<p>
    If that is not quite right, please let us know at
    <a href="mailto:info@lcnl.org">info@lcnl.org</a> quoting <?= esc($pledge['pledge_ref']) ?>.
</p>

<p>
    Your contribution will not only help secure the future of our Centre but will also become a
    lasting part of LCNL&rsquo;s history. Thank you for helping us celebrate 50 years.
</p>

<?= $this->endSection() ?>
