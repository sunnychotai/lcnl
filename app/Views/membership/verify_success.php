<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-5 text-center">
  <div class="alert alert-success shadow-sm border-0 p-4">
    <i class="bi bi-check-circle-fill text-success fs-1 mb-3"></i>
    <h2 class="fw-bold">Account Verified!</h2>
    <p class="lead">Your LCNL membership has been activated successfully.</p>
    <a href="<?= base_url('member/login') ?>" class="btn btn-brand mt-3">
      <i class="bi bi-box-arrow-in-right me-1"></i> Login Now
    </a>
  </div>
</div>

<?= $this->endSection() ?>
