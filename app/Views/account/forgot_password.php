<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2"><i class="bi bi-key-fill me-2"></i>Forgot Password</h1>
    <p class="lead fs-6 mb-0">Enter your email and we’ll send you a reset link</p>
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

          <form method="post" action="<?= route_to('member.forgot') ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">Email address</label>
              <input type="email" name="email" id="email" class="form-control" value="<?= old('email') ?>" required>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-brand rounded-pill">
                <i class="bi bi-envelope-at me-2"></i>Send Reset Link
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

