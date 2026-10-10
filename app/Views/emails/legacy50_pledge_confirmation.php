<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var array $pledge
 * @var \Config\Legacy50 $config
 */
$fullName = trim(($pledge['title'] ? $pledge['title'] . ' ' : '') . $pledge['first_name'] . ' ' . $pledge['last_name']);
?>

<p>Dear <?= esc($pledge['first_name']) ?>,</p>

<p>
    Thank you for making your pledge to <strong>50 Years of LCNL &ndash; 100 Donors, One Lasting
    Legacy</strong>, helping us make the Dhamecha Lohana Centre debt-free.
</p>

<p>
    <strong>Please now make payment</strong> to the account below, using the reference you gave us
    so we can match your donation.
</p>

<?= $this->include('emails/_legacy50_bank') ?>

<p>
    Your &pound;<?= esc(number_format((float) $pledge['amount'])) ?> will be matched by LCNL, putting
    &pound;<?= esc(number_format((float) $pledge['amount'] + $config->match)) ?> towards clearing the loan.
    We will email you again once your donation has arrived.
</p>

<p style="margin-bottom:6px;"><strong>What you told us</strong></p>
<table cellpadding="6" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e0e0e0; margin:0 0 20px;">
    <tr>
        <td style="width:40%;"><strong>Pledge reference</strong></td>
        <td><?= esc($pledge['pledge_ref']) ?></td>
    </tr>
    <tr>
        <td><strong>Name</strong></td>
        <td><?= esc($fullName) ?></td>
    </tr>
    <tr>
        <td><strong>Email</strong></td>
        <td><?= esc($pledge['email']) ?></td>
    </tr>
    <tr>
        <td><strong>Mobile</strong></td>
        <td><?= esc($pledge['phone']) ?></td>
    </tr>
    <tr>
        <td><strong>Name on the plaque</strong></td>
        <td><strong><?= esc($pledge['plaque_name']) ?></strong></td>
    </tr>
    <tr>
        <td><strong>Payment reference</strong></td>
        <td><?= esc($pledge['payment_reference']) ?></td>
    </tr>
    <tr>
        <td><strong>Gift Aid</strong></td>
        <td>
            <?php if ($pledge['gift_aid']): ?>
                Yes &ndash; for <?= esc(implode(', ', array_filter([
                    $pledge['house_name_number'], $pledge['street'], $pledge['town'], $pledge['postcode'],
                ]))) ?>
            <?php else: ?>
                No
            <?php endif; ?>
        </td>
    </tr>
</table>

<?php if ($pledge['gift_aid']): ?>
    <p style="font-size:13px; color:#555;">
        <strong>Your Gift Aid declaration:</strong><br>
        <?= nl2br(esc($pledge['gift_aid_wording'])) ?><br><br>
        Please tell us if you change your name or address, or no longer pay enough income tax
        and/or capital gains tax.
    </p>
<?php endif; ?>

<p>
    If anything above is wrong, reply to <a href="mailto:info@lcnl.org">info@lcnl.org</a> quoting
    <?= esc($pledge['pledge_ref']) ?>.
</p>

<p>
    Together, let&rsquo;s celebrate 50 years of LCNL by building a debt-free future for the next
    50 years and beyond.
</p>

<?= $this->endSection() ?>
