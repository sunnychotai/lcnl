<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero -->
<section class="hero-lcnl-watermark hero-overlay-gold d-flex align-items-center justify-content-center">
  <div class="container text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-1">
      <i class="bi bi-check-circle-fill me-2"></i>Thank You
    </h1>
    <p class="opacity-75 mb-0">Your registration has been received</p>
  </div>
</section>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
      <div class="card shadow-lg border-0">
        <div class="card-body p-4 p-md-5">

          <p>
            Dear <strong><?= esc(session()->getFlashdata('first_name') ?? 'Member') ?></strong>,
          </p>

          <p>
            Thank you for registering for
            <strong><?= esc(session()->getFlashdata('event_name')) ?></strong>.
          </p>

          <p>
            Your registration has been successfully submitted.
            You will receive further details shortly.
          </p>

          <p class="mb-0">
            Kind regards,<br>
            <strong>LCNL Committee</strong>
          </p>

        </div>

        <div class="card-footer bg-light text-center">
          <a href="<?= base_url('/') ?>" class="btn btn-accent rounded-pill px-4">
            <i class="bi bi-house-door me-1"></i>Return Home
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>