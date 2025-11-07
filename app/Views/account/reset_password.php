<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2"><i class="bi bi-shield-lock-fill me-2"></i>Reset Password</h1>
    <p class="lead fs-6 mb-0">Enter a new password for your LCNL account</p>
  </div>
</section>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="lcnl-card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">

          <?php if ($err = session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc($err) ?></div>
          <?php endif; ?>
          <?php if ($msg = session()->getFlashdata('message')): ?>
            <div class="alert alert-success"><?= esc($msg) ?></div>
          <?php endif; ?>

          <form method="post" action="<?= base_url('member/reset') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= esc($token) ?>">

            <div class="mb-3">
              <label for="password" class="form-label fw-semibold">New Password</label>
              <input type="password" name="password" id="password" class="form-control" minlength="8" required>
            </div>

            <div class="mb-3">
              <label for="password_confirm" class="form-label fw-semibold">Confirm Password</label>
              <input type="password" name="password_confirm" id="password_confirm" class="form-control" minlength="8"
                required>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-brand rounded-pill">
                <i class="bi bi-check2-circle me-2"></i>Update Password
              </button>
            </div>
          </form>

          <div class="mt-3 text-center">
            <a href="<?= base_url('membership/login') ?>" class="text-decoration-none">
              <i class="bi bi-arrow-left"></i> Back to Login
            </a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

