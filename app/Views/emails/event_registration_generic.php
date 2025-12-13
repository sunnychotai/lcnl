<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<p>Dear <?= esc($first_name) ?>,</p>

<p>
    Thank you for registering for the following LCNL event:
</p>

<table cellpadding="8" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e0d0d5;
              background-color:#fdfbfa; margin:20px 0;">
    <tr>
        <td width="35%" style="font-weight:bold;">Event</td>
        <td><?= esc($event_name) ?></td>
    </tr>
    <tr>
        <td style="font-weight:bold;">Participants</td>
        <td><?= esc($num_participants) ?></td>
    </tr>
    <tr>
        <td style="font-weight:bold;">Status</td>
        <td>
            <strong style="color:#7a1d3c;">Submitted</strong>
        </td>
    </tr>
</table>

<p>
    Your registration has been successfully submitted.
    Further details (including payment information, if applicable)
    will be shared shortly.
</p>

<p style="margin-top:25px;">
    Kind regards,<br>
    <strong>LCNL Committee</strong>
</p>

<?= $this->endSection() ?>