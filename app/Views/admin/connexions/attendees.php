<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var \Config\Connexions $config
 * @var array $rows
 * @var int   $girls
 * @var int   $boys
 */
?>

<div class="container py-4">

  <div class="cnx-door-noprint mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <a href="<?= base_url('admin/connexions') ?>" class="small text-decoration-none">
      <i class="bi bi-arrow-left"></i> All bookings
    </a>
    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
      <i class="bi bi-printer me-1"></i> Print
    </button>
  </div>

  <h1 class="h3 fw-bold mb-1">Connexions door list</h1>
  <p class="text-muted mb-3">
    <?= esc($config->eventLabel()) ?> &middot; <?= esc($config->venueLine()) ?><br>
    <strong><?= count($rows) ?> attendees</strong> (<?= $girls ?> girls, <?= $boys ?> boys) on paid bookings, by surname.
    Printed <?= esc(date('j M Y H:i')) ?>.
  </p>

  <?php if (empty($rows)): ?>
    <p class="text-muted">No paid bookings yet.</p>
  <?php else: ?>
    <table class="table table-sm table-bordered align-middle">
      <thead>
        <tr>
          <th scope="col" style="width:2.5rem;"><span class="visually-hidden">Arrived</span>&#10003;</th>
          <th scope="col">Surname</th>
          <th scope="col">First name</th>
          <th scope="col">Ticket</th>
          <th scope="col">Reference</th>
          <th scope="col">Mobile</th>
          <th scope="col">Booked by</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td></td>
            <td class="fw-semibold"><?= esc($r['last_name']) ?></td>
            <td><?= esc($r['first_name']) ?></td>
            <td><?= esc($config->genders[$r['gender']]['one']) ?></td>
            <td><code><?= esc($r['booking']['booking_ref']) ?></code></td>
            <td class="text-nowrap"><?= esc($r['phone']) ?></td>
            <td class="small"><?= esc($r['booking']['first_name'] . ' ' . $r['booking']['last_name']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

</div>

<?= $this->endSection() ?>
