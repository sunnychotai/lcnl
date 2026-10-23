<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var array $registration
 * @var \Config\ChopdaPujan $config
 */
$rows = [
    'Name'              => $registration['first_name'] . ' ' . $registration['last_name'],
    'Email'             => $registration['email'],
    'Mobile'            => $registration['phone'],
    'Attending'         => $registration['num_attendees'],
    'Payment reference' => $registration['payment_reference'],
    'Payment'           => match (true) {
        $registration['payment_method'] === 'card' => 'By card – the admin page shows it as received once Stripe confirms it',
        $registration['payment_timing'] === 'paid' => 'Bank transfer – says already paid (check the LCNL statement)',
        default                                    => 'Bank transfer – will pay by ' . $config->closingLabel(),
    },
];
?>

<p><strong>A new Chopda Pujan registration has been made.</strong></p>

<table cellpadding="8" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e0d0d5;
           background-color:#fdfbfa; margin:20px 0;">
    <tr style="background-color:#f5eaed;">
        <td colspan="2" style="font-weight:bold; font-size:15px; padding:10px 8px;">
            <?= esc($registration['registration_ref']) ?> &ndash; &pound;<?= esc(number_format((float) $registration['amount'])) ?>
        </td>
    </tr>
    <?php foreach ($rows as $label => $value): ?>
        <tr>
            <td style="width:38%;"><strong><?= esc($label) ?></strong></td>
            <td><?= esc($value) ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if (! empty($registration['comments'])): ?>
        <tr>
            <td><strong>Comments</strong></td>
            <td><?= nl2br(esc($registration['comments'])) ?></td>
        </tr>
    <?php endif; ?>
</table>

<p>
    <a href="<?= base_url('admin/chopda-pujan') ?>"
        style="background-color:#7a1d3c; color:#ffffff; padding:10px 18px;
              text-decoration:none; border-radius:4px; display:inline-block;">
        View all registrations
    </a>
</p>

<p style="color:#666; font-size:13px;">
    Card payments are marked paid automatically. For a bank transfer, mark the registration as paid
    once it appears on the LCNL bank statement &mdash; that sends the registrant an email confirming
    their place.
</p>

<?= $this->endSection() ?>
