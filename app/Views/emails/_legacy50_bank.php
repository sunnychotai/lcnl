<?php
/**
 * LCF bank details table, shared by the pledge confirmation and reminder emails.
 *
 * @var array $pledge
 * @var \Config\Legacy50 $config
 */
$bankRows = [
    'Account name'   => $config->bank['accountName'],
    'Account type'   => $config->bank['accountType'],
    'Account number' => $config->bank['accountNumber'],
    'Sort code'      => $config->bank['sortCode'],
];
?>
<table cellpadding="8" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e0d0d5;
           background-color:#fdfbfa; margin:20px 0;">
    <tr style="background-color:#f5eaed;">
        <td colspan="2" style="font-weight:bold; font-size:15px; padding:10px 8px;">
            Bank transfer &ndash; &pound;<?= esc(number_format((float) $pledge['amount'])) ?>
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
        <td style="color:#7a1d3c; font-weight:bold;"><?= esc($pledge['payment_reference']) ?></td>
    </tr>
    <tr style="background-color:#f5eaed;">
        <td colspan="2" style="font-weight:bold; padding:8px;">Paying from outside the UK?</td>
    </tr>
    <tr>
        <td><strong>IBAN</strong></td>
        <td><?= esc($config->bank['iban']) ?></td>
    </tr>
    <tr>
        <td><strong>SWIFT BIC</strong></td>
        <td><?= esc($config->bank['bic']) ?></td>
    </tr>
</table>
