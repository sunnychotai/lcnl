<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-1"><i class="bi bi-x-circle me-2"></i>Payment Cancelled</h1>
    <p class="mb-0 opacity-75">You have not been charged</p>
  </div>
</section>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4 p-md-5 text-center">
          <p class="lead mb-2">No problem. You can upgrade to Life Membership whenever you’re ready.</p>
          <p class="text-muted mb-4">
            Your dashboard also has our bank details if you’d rather pay by bank transfer.
          </p>
          <a class="btn btn-brand rounded-pill px-4" href="<?= base_url('account/dashboard') ?>">
            <i class="bi bi-speedometer2 me-2"></i> Back to my dashboard
          </a>
          <p class="text-muted small mt-4 mb-0">
            Had a problem paying? Email <a href="mailto:membership@lcnl.org">membership@lcnl.org</a>.
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
