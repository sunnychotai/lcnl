<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<p>Dear <?= esc($first_name) ?>,</p>

<p>
    Thank you for registering for the <strong>LCNL Golf Event 2026</strong> —
    we really appreciate your interest and support.
</p>

<p>
    Unfortunately, the event is now <strong>fully booked</strong> and we are unable
    to offer your team a place at this time.
</p>

<!-- Waiting list banner -->
<table cellpadding="0" cellspacing="0" width="100%" style="margin:20px 0;">
    <tr>
        <td style="background-color:#7a1d3c; border-radius:6px; padding:14px 20px; text-align:center;">
            <span style="color:#ffffff; font-size:18px; font-weight:bold;">
                You have been added to the waiting list
            </span>
        </td>
    </tr>
</table>

<p>
    We have placed your registration on the <strong>waiting list</strong>, and should
    any places become available due to cancellations, we will contact you straight away.
</p>

<!-- Registration summary -->
<table cellpadding="8" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e0d0d5;
           background-color:#fdfbfa; margin:20px 0;">
    <tr style="background-color:#f5eaed;">
        <td colspan="2" style="font-weight:bold; font-size:15px; padding:10px 8px;">
            Your Registration
        </td>
    </tr>
    <tr>
        <td width="35%" style="font-weight:bold;">Event</td>
        <td>LCNL Golf Event 2026</td>
    </tr>
    <tr style="background-color:#fdf6f7;">
        <td style="font-weight:bold;">Team Name</td>
        <td><?= esc($team_name) ?></td>
    </tr>
    <tr>
        <td style="font-weight:bold;">Reference</td>
        <td>
            <strong style="font-family:monospace; font-size:16px; color:#7a1d3c;">
                <?= esc($registration_ref) ?>
            </strong>
        </td>
    </tr>
    <tr style="background-color:#fdf6f7;">
        <td style="font-weight:bold;">Status</td>
        <td><strong style="color:#7a1d3c;">Waiting List</strong></td>
    </tr>
</table>

<p>
    Please note: if you have not yet made payment, there is no need to do so unless
    we confirm a place for your team. If you have already paid, we will be in touch
    to arrange a full refund or hold your place on the waiting list — whichever you prefer.
</p>

<p>If you have any questions, please contact:</p>
<ul>
    <li><strong>Dhiru Savani</strong> (Convenor) &ndash; 07956 492825</li>
    <li><strong>Ashok Sodha</strong> &ndash; 07870 701507</li>
</ul>

<p style="margin-top:25px;">
    Thank you again for your support, and we sincerely hope to see you on the course.<br><br>
    Kind regards,<br>
    <strong>LCNL Golf Event Committee</strong><br>
    <a href="https://www.lcnl.org" style="color:#7a1d3c;">www.lcnl.org</a>
</p>

<?= $this->endSection() ?>
