<?php
/**
 * LCNL bank details table for the Christmas Mehfil booking and reminder emails.
 *
 * @var array $booking
 * @var \Config\ChristmasMehfil $config
 */
$bankRows = [
    'Account name'   => $config->bank['accountName'],
    'Sort code'      => $config->bank['sortCode'],
    'Account number' => $config->bank['accountNumber'],
    'Amount'         => '£' . number_format((float) $booking['amount']),
];
?>
<table cellpadding="8" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e0d0d5;
           background-color:#fdfbfa; margin:20px 0;">
    <tr style="background-color:#f5eaed;">
        <td colspan="2" style="font-weight:bold; font-size:15px; padding:10px 8px;">
            Bank transfer &ndash; &pound;<?= esc(number_format((float) $booking['amount'])) ?>
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
        <td style="color:#7a1d3c; font-weight:bold; font-size:16px;"><?= esc($booking['booking_ref']) ?></td>
    </tr>
</table>
