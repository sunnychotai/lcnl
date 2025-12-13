<?= $this->extend('emails/layout') ?>
<?= $this->section('content') ?>

<p><strong>New Event Registration Received</strong></p>

<p>
  A new registration has been submitted for:
  <strong><?= esc($event_name) ?></strong>
</p>

<table cellpadding="8" cellspacing="0" width="100%"
  style="border-collapse:collapse; border:1px solid #e0d0d5;
              background-color:#fdfbfa; margin-bottom:20px;">
  <tr>
    <td width="35%" style="font-weight:bold;">Name</td>
    <td><?= esc($first_name) ?> <?= esc($last_name) ?></td>
  </tr>
  <tr>
    <td style="font-weight:bold;">Email</td>
    <td><?= esc($email) ?></td>
  </tr>
  <tr>
    <td style="font-weight:bold;">Phone</td>
    <td><?= esc($phone) ?></td>
  </tr>
  <tr>
    <td style="font-weight:bold;">Participants</td>
    <td><?= esc($num_participants) ?></td>
  </tr>
  <tr>
    <td style="font-weight:bold;">Notes</td>
    <td><?= esc($notes ?: 'N/A') ?></td>
  </tr>
  <tr>
    <td style="font-weight:bold;">Status</td>
    <td>
      <strong style="color:#7a1d3c;">
        <?= ucfirst(esc($status ?? 'submitted')) ?>
      </strong>
    </td>
  </tr>
</table>

<p>
  This registration has been logged automatically via the LCNL website.
</p>

<p style="margin-top:25px;">
  <strong>LCNL Website System</strong>
</p>

<?= $this->endSection() ?>