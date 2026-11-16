<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var \Config\ChristmasMehfil $config
 * @var array $booking
 */
$errors = session('errors') ?? [];
$pound  = static fn($n): string => '£' . number_format((float) $n);
?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      <a href="<?= base_url('admin/christmas-mehfil') ?>" class="small text-decoration-none">
        <i class="bi bi-arrow-left"></i> All bookings
      </a>
      <h1 class="h3 fw-bold mt-2 mb-3">Booking <?= esc($booking['booking_ref']) ?></h1>

      <?php if (! empty($errors)): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <dl class="row mb-0 small">
            <dt class="col-sm-4">Name</dt>
            <dd class="col-sm-8"><?= esc($booking['first_name'] . ' ' . $booking['last_name']) ?>
              <?= ! empty($booking['is_member']) ? '(LCNL member)' : '' ?></dd>
            <dt class="col-sm-4">Email / mobile</dt>
            <dd class="col-sm-8"><?= esc($booking['email']) ?> &middot; <?= esc($booking['phone']) ?></dd>
            <dt class="col-sm-4">Postcode</dt>
            <dd class="col-sm-8"><?= esc($booking['postcode']) ?></dd>
            <dt class="col-sm-4">Prices</dt>
            <dd class="col-sm-8">
              <?= $booking['price_band'] === 'early' ? 'Early bird' : 'Standard' ?>:
              <?= esc($pound($booking['individual_price'])) ?> per person, <?= esc($pound($booking['table_price'])) ?> per table
            </dd>
            <dt class="col-sm-4">Total</dt>
            <dd class="col-sm-8"><?= esc($pound($booking['amount'])) ?></dd>
            <dt class="col-sm-4">Status</dt>
            <dd class="col-sm-8"><?= esc($booking['status']) ?><?= ! empty($booking['payment_received']) ? ' (payment received)' : '' ?></dd>
          </dl>
        </div>
      </div>

      <form method="post" action="<?= base_url('admin/christmas-mehfil/update/' . $booking['id']) ?>" class="card border-0 shadow-sm">
        <?= csrf_field() ?>
        <div class="card-body">
          <div class="row g-3 mb-3">
            <div class="col-sm-6">
              <label for="individual_tickets" class="form-label fw-semibold">Individual tickets</label>
              <input type="number" class="form-control" style="max-width:8rem;" id="individual_tickets" name="individual_tickets"
                value="<?= esc(old('individual_tickets', $booking['individual_tickets'])) ?>" min="0" max="99" required>
            </div>
            <div class="col-sm-6">
              <label for="tables" class="form-label fw-semibold">Tables of <?= esc($config->tableSize) ?></label>
              <input type="number" class="form-control" style="max-width:8rem;" id="tables" name="tables"
                value="<?= esc(old('tables', $booking['tables'])) ?>" min="0" max="20" required>
            </div>
            <div class="col-12 form-text mt-1">
              The total is worked out again at this booking&rsquo;s prices. The booker is not emailed about the change.
            </div>
          </div>

          <div class="mb-3">
            <label for="admin_notes" class="form-label fw-semibold">Admin notes</label>
            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="4"
              maxlength="5000"><?= esc(old('admin_notes', $booking['admin_notes'] ?? '')) ?></textarea>
            <div class="form-text">Internal only &mdash; never shown to the booker.</div>
          </div>

          <button type="submit" class="btn btn-brand">Save</button>
          <a href="<?= base_url('admin/christmas-mehfil') ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>

    </div>
  </div>
</div>

<?= $this->endSection() ?>
