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
        <a href="<?= base_url('membership/resend-verification?email=' . urlencode(session('pending_email'))) ?>"
           class="btn btn-brand rounded-pill px-4">
          <i class="bi bi-arrow-repeat me-2"></i> Resend Verification Email
        </a>
      <?php endif; ?>

      <p class="text-muted small mt-4 mb-0">
        If you continue to have issues, contact us at
        <a href="mailto:membership@lcnl.org">membership@lcnl.org</a>.
      </p>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
