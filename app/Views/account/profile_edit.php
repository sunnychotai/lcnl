<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2"><i class="bi bi-person-lines-fill me-2"></i> My Profile</h1>
    <p class="lead fs-6 mb-0">Keep your details up to date</p>
  </div>
</section>

<div class="container py-4">
  <?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
      <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="form-card">
        <form method="post" action="<?= route_to('account.profile.update') ?>">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label class="form-label">Email (read-only)</label>
            <input type="email" class="form-control" value="<?= esc($m['email'] ?? '') ?>" disabled>
          </div>
          <div class="mb-3">
            <label class="form-label">Mobile (optional)</label>
            <input type="tel" name="mobile" value="<?= esc($m['mobile'] ?? '') ?>" class="form-control" inputmode="tel" placeholder="+447..." pattern="^\+?\d{7,15}$">
          </div>
          <div class="mb-3">
            <label class="form-label">Postcode (optional)</label>
            <input type="text" name="postcode" value="<?= esc($m['postcode'] ?? '') ?>" class="form-control" maxlength="12" autocomplete="postal-code">
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="consent" name="consent" value="1" <?= !empty($m['consent_at']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="consent">I consent to LCNL contacting me about membership and events.</label>
          </div>
          <button class="btn btn-brand rounded-pill px-4"><i class="bi bi-save me-1"></i> Save</button>
        </form>
      </div>
    </div>
  </div>


<hr class="my-5">

<h3 class="fw-bold mb-3"><i class="bi bi-people-fill me-2 text-brand"></i>My Household</h3>

<?php if ($family && !empty($household)): ?>
  <div class="lcnl-card shadow-sm border-0 mb-3">
    <div class="card-body">
      <h5 class="fw-bold mb-3"><?= esc($family['household_name'] ?? 'Your Household') ?></h5>
      <ul class="list-group list-group-flush">
        <?php foreach ($household as $fm): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span>
              <?= esc(($fm['first_name'] ?? '').' '.($fm['last_name'] ?? '')) ?>
              <small class="text-muted">(<?= ucfirst($fm['role']) ?>)</small>
            </span>
            <span class="badge bg-<?= $fm['status']==='active'?'success':($fm['status']==='pending'?'warning text-dark':'secondary') ?>">
              <?= ucfirst($fm['status']) ?>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
      <a href="<?= route_to('account.household') ?>" class="btn btn-sm btn-brand mt-3">
        <i class="bi bi-pencil-square me-1"></i> Manage Household
      </a>
    </div>
  </div>
<?php else: ?>
  <p class="text-muted">No household set up yet.</p>
  <a href="<?= route_to('account.household') ?>" class="btn btn-sm btn-outline-brand">
    <i class="bi bi-house-add me-1"></i> Create Household
  </a>
<?php endif; ?>
</div>

<?= $this->endSection() ?>
