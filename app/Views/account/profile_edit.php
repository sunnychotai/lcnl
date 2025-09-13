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
</div>

<?= $this->endSection() ?>
