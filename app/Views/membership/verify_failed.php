<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">

      <?php if (!empty($disabled)): ?>
        <div class="alert alert-danger shadow-sm border-0 p-4 text-center">
          <i class="bi bi-x-circle-fill text-danger fs-1 mb-3"></i>
          <h2 class="fw-bold">This account is closed</h2>
          <p class="lead mb-0">
            Please contact <a href="mailto:membership@lcnl.org">membership@lcnl.org</a> if you think this is a mistake.
          </p>
        </div>
      <?php else: ?>
        <div class="alert alert-warning shadow-sm border-0 p-4 text-center">
          <i class="bi bi-hourglass-bottom text-warning fs-1 mb-3"></i>
          <h2 class="fw-bold">This link has expired</h2>
          <p class="lead mb-0">
            Verification links last 48 hours and can only be used once. Enter your email and we’ll send you a new one.
          </p>
        </div>

        <?php if ($err = session()->getFlashdata('error')): ?>
          <div class="alert alert-danger shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc($err) ?>
          </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 rounded-4">
          <div class="card-body p-4">
            <form method="post" action="<?= route_to('membership.resend') ?>">
              <?= csrf_field() ?>
              <label class="form-label fw-semibold" for="email">Email address</label>
              <div class="input-group mb-3">
                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                <input type="email" id="email" name="email" class="form-control" inputmode="email"
                  autocomplete="email" required>
              </div>
              <button type="submit" class="btn btn-brand rounded-pill px-4 w-100">
                <i class="bi bi-arrow-repeat me-2"></i> Send a new link
              </button>
            </form>

            <p class="text-muted small text-center mt-3 mb-0">
              Already verified? <a href="<?= base_url('membership/login') ?>">Log in</a>
              &middot; New to LCNL? <a href="<?= base_url('membership/register') ?>">Register</a>
            </p>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<?= $this->endSection() ?>
