<?php
/**
 * LCNL bank details table, shared by the registration confirmation and reminder emails.
 *
 * @var array $registration
 * @var \Config\ChopdaPujan $config
 */
$bankRows = [
    'Account name'   => $config->bank['accountName'],
    'Sort code'      => $config->bank['sortCode'],
    'Account number' => $config->bank['accountNumber'],
];
?>
<table cellpadding="8" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e0d0d5;
           background-color:#fdfbfa; margin:20px 0;">
    <tr style="background-color:#f5eaed;">
        <td colspan="2" style="font-weight:bold; font-size:15px; padding:10px 8px;">
            Bank transfer &ndash; &pound;<?= esc(number_format((float) $registration['amount'])) ?>
        </td>
    </tr>
    <?php foreach ($bankRows as $label => $value): ?>
        <tr>
            <td style="width:40%;"><strong><?= esc($label) ?></strong></td>
            <td><?= esc($value) ?></td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td><strong>Your reference</strong></td>
        <td style="color:#7a1d3c; font-weight:bold;"><?= esc($registration['payment_reference']) ?></td>
    </tr>
</table>
