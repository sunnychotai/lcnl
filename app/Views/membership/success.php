<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">
      <i class="bi bi-envelope-check-fill me-2"></i> Thanks for Registering!
    </h1>
    <p class="lead fs-6 mb-0">
      Please confirm your email to activate your account.
    </p>
  </div>
</section>

<div class="container py-5">
  <div class="col-lg-8 mx-auto">
    <?php if ($msg = session()->getFlashdata('message')): ?>
      <div class="alert alert-success shadow-sm" role="status">
        <i class="bi bi-check-circle-fill me-2"></i><?= esc($msg) ?>
      </div>
    <?php endif; ?>
    <?php if ($err = session()->getFlashdata('error')): ?>
      <div class="alert alert-danger shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc($err) ?>
      </div>
    <?php endif; ?>

    <div class="constitution-box">
      <p class="mb-3">
        We’ve received your details<?= session('pending_email') ? ' for <strong>'.esc(session('pending_email')).'</strong>' : '' ?>.
      </p>

      <p class="mb-3">
        An email has been sent with a link to verify your account.  
        Click the link in that email to complete your registration.
      </p>

      <p class="mb-4">
        Didn’t get the email? Please check your spam/junk folder, or request another verification email below.
      </p>

      <?php if (session('pending_email')): ?>
        <form method="post" action="<?= route_to('membership.resend') ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="email" value="<?= esc(session('pending_email'), 'attr') ?>">
          <button type="submit" class="btn btn-brand rounded-pill px-4">
            <i class="bi bi-arrow-repeat me-2"></i> Resend Verification Email
          </button>
        </form>
      <?php endif; ?>

      <p class="text-muted small mt-4 mb-0">
        If you continue to have issues, contact us at
        <a href="mailto:membership@lcnl.org">membership@lcnl.org</a>.
      </p>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
