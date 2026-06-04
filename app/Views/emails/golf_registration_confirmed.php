<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<p>Dear <?= esc($first_name) ?>,</p>

<p>
    We are delighted to confirm that your registration for the
    <strong>LCNL Golf Event 2026</strong> is now <strong style="color:#166534;">confirmed</strong>.
    Your payment has been received — we look forward to seeing you on the day!
</p>

<!-- Confirmation banner -->
<table cellpadding="0" cellspacing="0" width="100%" style="margin:20px 0;">
    <tr>
        <td style="background-color:#166534; border-radius:6px; padding:14px 20px; text-align:center;">
            <span style="color:#ffffff; font-size:18px; font-weight:bold;">
                &#10003; Registration Confirmed &amp; Payment Received
            </span>
        </td>
    </tr>
</table>

<!-- Event summary -->
<table cellpadding="8" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e0d0d5;
           background-color:#fdfbfa; margin:20px 0;">
    <tr style="background-color:#f5eaed;">
        <td colspan="2" style="font-weight:bold; font-size:15px; padding:10px 8px;">
            Event Details
        </td>
    </tr>
    <tr>
        <td width="35%" style="font-weight:bold;">Event</td>
        <td>LCNL Golf Event 2026</td>
    </tr>
    <tr style="background-color:#fdf6f7;">
        <td style="font-weight:bold;">Date</td>
        <td>Wednesday 29th July 2026</td>
    </tr>
    <tr>
        <td style="font-weight:bold;">Venue</td>
        <td>Moor Park Golf Club, Rickmansworth, WD3 1QN</td>
    </tr>
    <tr style="background-color:#fdf6f7;">
        <td style="font-weight:bold;">Schedule</td>
        <td>Registration &amp; Breakfast: 11:00am &bull; Shotgun Start: 1:00pm &bull; BBQ: 6:30pm</td>
    </tr>
    <tr>
        <td style="font-weight:bold;">Team Name</td>
        <td><?= esc($team_name) ?></td>
    </tr>
    <tr style="background-color:#fdf6f7;">
        <td style="font-weight:bold;">Reference</td>
        <td>
            <strong style="font-family:monospace; font-size:16px; color:#7a1d3c;">
                <?= esc($registration_ref) ?>
            </strong>
        </td>
    </tr>
    <tr>
        <td style="font-weight:bold;">Status</td>
        <td><strong style="color:#166534;">&#10003; Confirmed</strong></td>
    </tr>
</table>

<!-- Confirmed players -->
<p style="font-weight:bold; margin-top:20px;">Confirmed Players:</p>
<table cellpadding="6" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #e0d0d5; margin-bottom:20px;">
    <tr style="background-color:#7a1d3c; color:#fff;">
        <th style="padding:8px; text-align:left;">#</th>
        <th style="padding:8px; text-align:left;">Name</th>
        <th style="padding:8px; text-align:left;">Handicap</th>
        <th style="padding:8px; text-align:left;">Meal</th>
    </tr>
    <?php foreach ($all_players as $i => $player): ?>
    <tr style="background-color:<?= $i % 2 === 0 ? '#fdfbfa' : '#f5eaed' ?>;">
        <td style="padding:8px;"><?= $i + 1 ?></td>
        <td style="padding:8px;"><?= esc($player['full_name']) ?></td>
        <td style="padding:8px;"><?= esc($player['handicap']) ?></td>
        <td style="padding:8px;">
            <?= $player['meal'] === 'vegetarian' ? 'Vegetarian' : 'Non-Vegetarian' ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- What to expect -->
<table cellpadding="8" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:1px solid #bbf7d0;
           background-color:#f0fdf4; margin:20px 0; border-radius:6px;">
    <tr>
        <td style="font-weight:bold; font-size:14px; padding:10px 8px; color:#166534;">
            What to expect on the day
        </td>
    </tr>
    <tr>
        <td style="padding:8px; font-size:13px;">
            <ul style="margin:0; padding-left:18px;">
                <li style="margin-bottom:6px;">Please arrive by <strong>11:00am</strong> for registration and breakfast.</li>
                <li style="margin-bottom:6px;">Shotgun start at <strong>1:00pm</strong> — please be ready at your assigned hole.</li>
                <li style="margin-bottom:6px;">Please follow the Moor Park Golf Club dress code and etiquette requirements.</li>
                <li>BBQ and evening gathering from <strong>6:30pm</strong>.</li>
            </ul>
        </td>
    </tr>
</table>

<p>If you have any questions, please contact:</p>
<ul>
    <li><strong>Dhiru Savani</strong> (Convenor) &ndash; 07956 492825</li>
    <li><strong>Vinod Thakrar</strong> &ndash; 07960 541216</li>
</ul>

<p style="margin-top:25px;">
    We look forward to seeing you on the course!<br><br>
    Kind regards,<br>
    <strong>LCNL Golf Event Committee</strong><br>
    <a href="https://www.lcnl.org" style="color:#7a1d3c;">www.lcnl.org</a>
</p>

<?= $this->endSection() ?>
