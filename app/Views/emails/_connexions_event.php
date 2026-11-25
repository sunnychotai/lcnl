<?php
/**
 * Event details and the door rules, shared by the Connexions confirmation emails.
 *
 * @var array $booking
 * @var \Config\Connexions $config
 */
?>
<table cellpadding="8" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e8b4a0; background-color:#fff7f3; margin:20px 0;">
    <tr>
        <td style="width:35%;"><strong>Event</strong></td>
        <td><?= esc($config->eventName) ?></td>
    </tr>
    <tr>
        <td><strong>Date</strong></td>
        <td><?= esc($config->eventLabel()) ?></td>
    </tr>
    <tr>
        <td><strong>Time</strong></td>
        <td><?= esc($config->times) ?> (registration from <?= esc($config->registrationFrom) ?>)</td>
    </tr>
    <tr>
        <td><strong>Venue</strong></td>
        <td><?= esc($config->venueLine()) ?></td>
    </tr>
    <tr>
        <td><strong>Booking reference</strong></td>
        <td><strong><?= esc($booking['booking_ref']) ?></strong></td>
    </tr>
</table>

<table cellpadding="12" cellspacing="0" width="100%"
    style="border-collapse:collapse; border-left:4px solid #b42318; background-color:#fdecea; color:#7a1414; margin:0 0 20px;">
    <tr>
        <td>
            <strong>Please bring this confirmation email with you on the day</strong> (on your phone is fine)
            and show it on arrival.<br><br>
            <strong>There are no refunds.</strong> Tickets are non-refundable and non-exchangeable.
            If any details are wrong you may be refused admission, so if anything below is incorrect please email
            <a href="mailto:<?= esc($config->contactEmail, 'attr') ?>" style="color:#7a1414;"><?= esc($config->contactEmail) ?></a>
            quoting <?= esc($booking['booking_ref']) ?> before the event.
        </td>
    </tr>
</table>
