<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">
      <i class="bi bi-exclamation-triangle-fill me-2"></i> Reset Link Invalid
    </h1>
    <p class="lead fs-6 mb-0">Your password reset link is invalid or has expired.</p>
  </div>
</section>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="lcnl-card shadow-sm border-0 rounded-4">
        <div class="card-body p-4 text-center">

          <p class="mb-3 text-muted">
            For security, reset links are only valid for <strong>2 hours</strong>.
          </p>

          <p class="mb-4">
            Don’t worry — you can request a new link and continue resetting your password.
          </p>

          <a href="<?= route_to('member.forgot') ?>" class="btn btn-brand rounded-pill">
            <i class="bi bi-envelope-at me-2"></i>Request New Reset Link
          </a>

          <div class="mt-3">
            <a href="<?= base_url('member/login') ?>" class="text-decoration-none">
              <i class="bi bi-arrow-left"></i> Back to Login
            </a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
