<?php
/**
 * The booker's details and tickets, shared by the Christmas Mehfil emails.
 *
 * @var array $booking
 * @var \Config\ChristmasMehfil $config
 */
$pound  = static fn($n): string => '£' . number_format((float) $n);
$people = $config->people((int) $booking['individual_tickets'], (int) $booking['tables']);

$orderRows = ['Reference' => $booking['booking_ref'], 'Name' => $booking['first_name'] . ' ' . $booking['last_name']];

if ((int) $booking['individual_tickets'] > 0) {
    $orderRows['Individual tickets'] = $booking['individual_tickets'] . ' × ' . $pound($booking['individual_price'])
        . ' = ' . $pound($booking['individual_tickets'] * $booking['individual_price']);
}
if ((int) $booking['tables'] > 0) {
    $orderRows['Tables of ' . $config->tableSize] = $booking['tables'] . ' × ' . $pound($booking['table_price'])
        . ' = ' . $pound($booking['tables'] * $booking['table_price']);
}

$orderRows['People'] = $people;
$orderRows['Total']  = $pound($booking['amount']) . ($booking['price_band'] === 'early' ? ' (early bird price)' : '');
?>
<p style="margin-bottom:6px;"><strong>Your booking</strong></p>
<table cellpadding="6" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e0e0e0; margin:0 0 20px;">
    <?php foreach ($orderRows as $label => $value): ?>
        <tr>
            <td style="width:40%;"><strong><?= esc($label) ?></strong></td>
            <td><?= esc($value) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
