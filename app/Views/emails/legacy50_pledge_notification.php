<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var array $pledge
 * @var \Config\Legacy50 $config
 */
$rows = [
    'Name'              => trim(($pledge['title'] ? $pledge['title'] . ' ' : '') . $pledge['first_name'] . ' ' . $pledge['last_name']),
    'Email'             => $pledge['email'],
    'Mobile'            => $pledge['phone'],
    'Plaque name'       => $pledge['plaque_name'],
    'Payment reference' => $pledge['payment_reference'],
    'Already paid?'     => $pledge['payment_timing'] === 'paid'
        ? 'Says yes (check the LCF statement)'
        : 'Not yet – will pay within 7 days',
    'Gift Aid'          => $pledge['gift_aid']
        ? 'Yes – ' . implode(', ', array_filter([$pledge['house_name_number'], $pledge['street'], $pledge['town'], $pledge['postcode']]))
        : 'No',
];
?>

<p><strong>A new Legacy 50 pledge has been made.</strong></p>

<table cellpadding="8" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e0d0d5;
           background-color:#fdfbfa; margin:20px 0;">
    <tr style="background-color:#f5eaed;">
        <td colspan="2" style="font-weight:bold; font-size:15px; padding:10px 8px;">
            <?= esc($pledge['pledge_ref']) ?> &ndash; &pound;<?= esc(number_format((float) $pledge['amount'])) ?>
        </td>
    </tr>
    <?php foreach ($rows as $label => $value): ?>
        <tr>
            <td style="width:38%;"><strong><?= esc($label) ?></strong></td>
            <td><?= esc($value) ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if (! empty($pledge['comments'])): ?>
        <tr>
            <td><strong>Comments</strong></td>
            <td><?= nl2br(esc($pledge['comments'])) ?></td>
        </tr>
    <?php endif; ?>
</table>

<p>
    <a href="<?= base_url('admin/legacy50') ?>"
        style="background-color:#7a1d3c; color:#ffffff; padding:10px 18px;
              text-decoration:none; border-radius:4px; display:inline-block;">
        View all pledges
    </a>
</p>

<p style="color:#666; font-size:13px;">
    Mark the pledge as paid once the transfer appears on the LCF bank statement &mdash; that sends
    the donor their thank-you email.
</p>

<?= $this->endSection() ?>
