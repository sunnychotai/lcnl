<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<p><strong>Life Membership Confirmed</strong></p>

<p>
    We are pleased to confirm that your
    <strong>LCNL Life Membership</strong>
    has been successfully activated.
</p>

<table cellpadding="8" cellspacing="0" width="100%" style="border-collapse:collapse; border:1px solid #e0d0d5;
              background-color:#fdfbfa; margin-bottom:20px;">
    <tr>
        <td width="35%" style="font-weight:bold;">Member Name</td>
        <td><?= esc($first_name) ?> <?= esc($last_name) ?></td>
    </tr>
    <tr>
        <td style="font-weight:bold;">Email</td>
        <td><?= esc($email) ?></td>
    </tr>
    <tr>
        <td style="font-weight:bold;">Membership Type</td>
        <td><strong>LIFE</strong></td>
    </tr>
    <tr>
        <td style="font-weight:bold;">Payment Reference</td>
        <td><?= esc($payment_reference) ?></td>
    </tr>
    <tr>
        <td style="font-weight:bold;">Status</td>
        <td>
            <strong style="color:#7a1d3c;">
                Active
            </strong>
        </td>
    </tr>
</table>

<p>
    Thank you for your generous support of the
    <strong>Lohana Community of North London</strong>.
    Your Life Membership helps us continue serving the community
    and supporting future generations.
</p>

<p>
    You can log in at any time to view your membership details:
    <br>
    <a href="<?= esc($dashboard_url) ?>">
        <?= esc($dashboard_url) ?>
    </a>
</p>

<p style="margin-top:25px;">
    <strong>Lohana Community North London</strong><br>
    LCNL Website System
</p>

<?= $this->endSection() ?>

