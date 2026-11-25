<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var \Config\Connexions $config
 * @var array $booking
 * @var array $attendees
 */
$errors = session('errors') ?? [];
$val    = static fn(string $key, $current) => old($key, $current);
$fields = ['first_name' => 'First name', 'last_name' => 'Surname', 'email' => 'Email', 'phone' => 'Mobile'];
?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-9">

      <a href="<?= base_url('admin/connexions') ?>" class="small text-decoration-none">
        <i class="bi bi-arrow-left"></i> All bookings
      </a>
      <h1 class="h3 fw-bold mt-2 mb-1">Booking <?= esc($booking['booking_ref']) ?></h1>
      <p class="text-muted mb-3">
        <?= esc($config->ticketsLabel((int) $booking['girl_tickets'], (int) $booking['boy_tickets'])) ?>
        &middot; &pound;<?= esc(number_format((float) $booking['amount'])) ?>
        &middot; <?= esc($booking['status']) ?><?= ! empty($booking['payment_received']) ? ' (' . esc($booking['payment_marked_by']) . ')' : '' ?>
        &middot; LCNL member: <?= ! empty($booking['is_member']) ? 'yes' : 'no' ?>
      </p>

      <?php if (! empty($errors)): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="<?= base_url('admin/connexions/update/' . $booking['id']) ?>" class="card border-0 shadow-sm">
        <?= csrf_field() ?>
        <div class="card-body">

          <h2 class="h6 fw-bold text-brand">Booked by</h2>
          <div class="row g-2 mb-4">
            <?php foreach ($fields as $f => $label): ?>
              <div class="col-sm-6 col-lg-3">
                <label for="<?= $f ?>" class="form-label small fw-semibold"><?= esc($label) ?></label>
                <input type="<?= $f === 'email' ? 'email' : 'text' ?>" class="form-control form-control-sm" id="<?= $f ?>"
                  name="<?= $f ?>" value="<?= esc($val($f, $booking[$f])) ?>" required>
              </div>
            <?php endforeach; ?>
          </div>

          <?php foreach ($attendees as $a): ?>
            <h2 class="h6 fw-bold text-brand"><?= esc($config->genders[$a['gender']]['one'] . ' ' . $a['position']) ?></h2>
            <div class="row g-2 mb-3">
              <?php foreach ($fields as $f => $label): ?>
                <?php $id = 'att_' . $a['id'] . '_' . $f; ?>
                <div class="col-sm-6 col-lg-3">
                  <label for="<?= $id ?>" class="form-label small fw-semibold"><?= esc($label) ?></label>
                  <input type="<?= $f === 'email' ? 'email' : 'text' ?>" class="form-control form-control-sm" id="<?= $id ?>"
                    name="attendees[<?= $a['id'] ?>][<?= $f ?>]"
                    value="<?= esc($val('attendees.' . $a['id'] . '.' . $f, $a[$f])) ?>" required>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endforeach; ?>

          <div class="mb-3 mt-4">
            <label for="admin_notes" class="form-label fw-semibold">Admin notes</label>
            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="4"
              maxlength="5000"><?= esc($val('admin_notes', $booking['admin_notes'] ?? '')) ?></textarea>
            <div class="form-text">Internal only &mdash; never shown to the booker.</div>
          </div>

          <p class="small text-muted">
            Tickets and the amount paid can&rsquo;t be changed here. Nobody is emailed when you save;
            use <strong>Resend emails</strong> on the bookings page to send corrected confirmations.
          </p>

          <button type="submit" class="btn btn-brand">Save</button>
          <a href="<?= base_url('admin/connexions') ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>

    </div>
  </div>
</div>

<?= $this->endSection() ?>
