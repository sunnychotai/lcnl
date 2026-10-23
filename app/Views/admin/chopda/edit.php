<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var \Config\ChopdaPujan $config
 * @var array $registration
 */
$errors = session('errors') ?? [];
?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      <a href="<?= base_url('admin/chopda-pujan') ?>" class="small text-decoration-none">
        <i class="bi bi-arrow-left"></i> All registrations
      </a>
      <h1 class="h3 fw-bold mt-2 mb-3">Registration <?= esc($registration['registration_ref']) ?></h1>

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
            <dd class="col-sm-8"><?= esc($registration['first_name'] . ' ' . $registration['last_name']) ?></dd>
            <dt class="col-sm-4">Email / mobile</dt>
            <dd class="col-sm-8"><?= esc($registration['email']) ?> &middot; <?= esc($registration['phone']) ?></dd>
            <dt class="col-sm-4">Payment reference</dt>
            <dd class="col-sm-8"><?= esc($registration['payment_reference']) ?></dd>
            <dt class="col-sm-4">Status</dt>
            <dd class="col-sm-8"><?= esc($registration['status']) ?></dd>
            <?php if (! empty($registration['comments'])): ?>
              <dt class="col-sm-4">Their comments</dt>
              <dd class="col-sm-8"><?= nl2br(esc($registration['comments'])) ?></dd>
            <?php endif; ?>
          </dl>
        </div>
      </div>

      <form method="post" action="<?= base_url('admin/chopda-pujan/update/' . $registration['id']) ?>" class="card border-0 shadow-sm">
        <?= csrf_field() ?>
        <div class="card-body">
          <div class="mb-3">
            <label for="num_attendees" class="form-label fw-semibold">Attending</label>
            <input type="number" class="form-control" style="max-width:8rem;" id="num_attendees" name="num_attendees"
              value="<?= esc(old('num_attendees', $registration['num_attendees'])) ?>" min="1" max="99" required>
            <div class="form-text">Everyone coming on this registration, Yajmans included.</div>
          </div>

          <div class="mb-3">
            <label for="admin_notes" class="form-label fw-semibold">Admin notes</label>
            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="4"
              maxlength="5000"><?= esc(old('admin_notes', $registration['admin_notes'] ?? '')) ?></textarea>
            <div class="form-text">Internal only &mdash; never shown to the registrant.</div>
          </div>

          <button type="submit" class="btn btn-brand">Save</button>
          <a href="<?= base_url('admin/chopda-pujan') ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>

    </div>
  </div>
</div>

<?= $this->endSection() ?>
