<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2"><i class="bi bi-box-arrow-in-right me-2"></i>Member Login</h1>
    <p class="lead fs-6 mb-0">Access your LCNL account</p>
  </div>
</section>

<div class="container py-5">
  <?php if ($msg = session()->getFlashdata('message')): ?>
    <div class="alert alert-success"><?= esc($msg) ?></div>
  <?php endif; ?>
  <?php if ($err = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($err) ?></div>
  <?php endif; ?>

  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="form-card">
        <h3 class="fw-bold mb-3">Sign in</h3>
        <form method="post" action="<?= base_url('member/attempt') ?>">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="<?= old('email') ?>" class="form-control"
                   inputmode="email" autocomplete="email" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" autocomplete="current-password" required>
          </div>
          <button class="btn btn-brand btn-lg rounded-pill px-4 w-100">
            <i class="bi bi-door-open-fill me-2"></i> Login
          </button>
        </form>
        <p class="text-muted small mt-3 mb-0">
          New to LCNL? <a href="<?= base_url('membership/register') ?>">Create your account</a>.
        </p>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
