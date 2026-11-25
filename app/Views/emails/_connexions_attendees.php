<?php
/**
 * Every attendee on a booking, for the booker's and organisers' emails.
 *
 * @var array $attendees
 * @var \Config\Connexions $config
 */
?>
<?php foreach ($attendees as $a): ?>
<table cellpadding="6" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e0e0e0; margin:0 0 12px;">
    <tr style="background-color:#f7efec;">
        <td colspan="2"><strong><?= esc($config->genders[$a['gender']]['one'] . ' ' . $a['position']) ?> attendee details</strong></td>
    </tr>
    <tr>
        <td style="width:35%;">First name</td>
        <td><?= esc($a['first_name']) ?></td>
    </tr>
    <tr>
        <td>Surname</td>
        <td><?= esc($a['last_name']) ?></td>
    </tr>
    <tr>
        <td>Email</td>
        <td><?= esc($a['email']) ?></td>
    </tr>
    <tr>
        <td>Mobile</td>
        <td><?= esc($a['phone']) ?></td>
    </tr>
</table>
<?php endforeach; ?>
