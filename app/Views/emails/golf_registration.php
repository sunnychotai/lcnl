<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<p>Dear <?= esc($first_name) ?>,</p>

<p>
    Thank you for registering for the <strong>LCNL Golf Event 2026</strong>.
    Your registration has been received and is currently <strong>pending payment confirmation</strong>.
</p>

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
        <td style="font-weight:bold;">Status</td>
        <td><strong style="color:#b45309;">Submitted &ndash; Awaiting Payment</strong></td>
    </tr>
    <tr>
        <td style="font-weight:bold;">Reference</td>
        <td>
            <strong style="font-family:monospace; font-size:16px; color:#7a1d3c;">
                <?= esc($registration_ref) ?>
            </strong>
        </td>
    </tr>
</table>

<!-- Players registered -->
<p style="font-weight:bold; margin-top:20px;">Players Registered:</p>
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

<!-- Payment instructions -->
<table cellpadding="8" cellspacing="0" width="100%"
    style="border-collapse:collapse; border:2px solid #b45309;
           background-color:#fffbeb; margin:20px 0;">
    <tr style="background-color:#b45309;">
        <td colspan="2" style="font-weight:bold; font-size:15px; padding:10px 8px; color:#fff;">
            Payment Instructions
        </td>
    </tr>
    <tr>
        <td colspan="2" style="padding:8px; font-size:13px; color:#555;">
            Please make a bank transfer using the details below and include your
            <strong>reference number</strong> so we can match your payment.
            Your place will be confirmed once payment is received.
        </td>
    </tr>
    <tr style="background-color:#fef9ee;">
        <td width="35%" style="font-weight:bold;">Account Name</td>
        <td>Lohana Community North London</td>
    </tr>
    <tr>
        <td style="font-weight:bold;">Sort Code</td>
        <td style="font-family:monospace;">40-23-13</td>
    </tr>
    <tr style="background-color:#fef9ee;">
        <td style="font-weight:bold;">Account Number</td>
        <td style="font-family:monospace;">21497995</td>
    </tr>
    <tr style="background-color:#fef9ee;">
        <td style="font-weight:bold;">Amount</td>
        <td><strong>£145 per player</strong></td>
    </tr>
    <tr style="background-color:#fff8e1;">
        <td style="font-weight:bold; color:#7a1d3c;">Your Reference</td>
        <td>
            <strong style="font-family:monospace; font-size:16px; color:#7a1d3c;">
                <?= esc($registration_ref) ?>
            </strong>
            <br>
            <small style="color:#666;">You must include this reference with your payment.</small>
        </td>
    </tr>
</table>

<p>If you have any questions, please contact:</p>
<ul>
    <li><strong>Dhiru Savani</strong> (Convenor) &ndash; 07956 492825</li>
    <li><strong>Ashok Sodha</strong> &ndash; 07870 701507</li>
</ul>

<p style="margin-top:25px;">
    Kind regards,<br>
    <strong>LCNL Golf Event Committee</strong><br>
    <a href="https://www.lcnl.org" style="color:#7a1d3c;">www.lcnl.org</a>
</p>

<?= $this->endSection() ?>
