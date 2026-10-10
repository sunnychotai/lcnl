<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/** @var \Config\Legacy50 $config */
$errors = session('errors') ?? [];
?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      <a href="<?= base_url('admin/legacy50') ?>" class="small text-decoration-none">
        <i class="bi bi-arrow-left"></i> All pledges
      </a>
      <h1 class="h3 fw-bold mt-2 mb-3">Pledge <?= esc($pledge['pledge_ref']) ?></h1>

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
            <dt class="col-sm-4">Donor</dt>
            <dd class="col-sm-8"><?= esc(trim($pledge['title'] . ' ' . $pledge['first_name'] . ' ' . $pledge['last_name'])) ?></dd>
            <dt class="col-sm-4">Email / mobile</dt>
            <dd class="col-sm-8"><?= esc($pledge['email']) ?> &middot; <?= esc($pledge['phone']) ?></dd>
            <dt class="col-sm-4">Payment reference</dt>
            <dd class="col-sm-8"><?= esc($pledge['payment_reference']) ?></dd>
            <dt class="col-sm-4">Status</dt>
            <dd class="col-sm-8"><?= esc($pledge['status']) ?></dd>
            <dt class="col-sm-4">Gift Aid</dt>
            <dd class="col-sm-8">
              <?php if ($pledge['gift_aid']): ?>
                Yes &mdash; <?= esc(implode(', ', array_filter([$pledge['house_name_number'], $pledge['street'], $pledge['town'], $pledge['postcode']]))) ?>
                <div class="text-muted">
                  Declared <?= esc(date('j M Y H:i', strtotime($pledge['gift_aid_declared_at']))) ?>
                  (wording <?= esc($pledge['gift_aid_version']) ?>)
                </div>
              <?php else: ?>
                No
              <?php endif; ?>
            </dd>
            <?php if (! empty($pledge['comments'])): ?>
              <dt class="col-sm-4">Donor comments</dt>
              <dd class="col-sm-8"><?= nl2br(esc($pledge['comments'])) ?></dd>
            <?php endif; ?>
          </dl>
        </div>
      </div>

      <form method="post" action="<?= base_url('admin/legacy50/update/' . $pledge['id']) ?>" class="card border-0 shadow-sm">
        <?= csrf_field() ?>
        <div class="card-body">
          <div class="mb-3">
            <label for="plaque_name" class="form-label fw-semibold">Plaque name</label>
            <input type="text" class="form-control" id="plaque_name" name="plaque_name"
              value="<?= esc(old('plaque_name', $pledge['plaque_name'])) ?>"
              maxlength="<?= esc($config->plaqueMaxLength, 'attr') ?>" required>
            <div class="form-text">Donor submitted: &ldquo;<?= esc($pledge['plaque_name_submitted']) ?>&rdquo;</div>
          </div>

          <div class="mb-3">
            <label for="admin_notes" class="form-label fw-semibold">Admin notes</label>
            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="4"
              maxlength="5000"><?= esc(old('admin_notes', $pledge['admin_notes'] ?? '')) ?></textarea>
            <div class="form-text">Internal only &mdash; never shown to the donor.</div>
          </div>

          <button type="submit" class="btn btn-brand">Save</button>
          <a href="<?= base_url('admin/legacy50') ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>

    </div>
  </div>
</div>

<?= $this->endSection() ?>
