<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2"><i class="bi bi-envelope-check-fill me-2"></i>Thanks!</h1>
    <p class="lead fs-6 mb-0">Your registration is pending activation by our admins.</p>
  </div>
</section>

<div class="container py-5">
  <div class="col-lg-8 mx-auto">
    <div class="constitution-box">
      <p class="mb-2">We’ve received your details<?= session('pending_email') ? ' for <strong>'.esc(session('pending_email')).'</strong>' : '' ?>.</p>
      <p class="mb-0">You’ll be notified once your account is activated. If you have questions, email
        <a href="mailto:membership@lcnl.org">membership@lcnl.org</a>.
      </p>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
