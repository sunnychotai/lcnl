<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-5 text-center">
  <div class="alert alert-danger shadow-sm border-0 p-4">
    <i class="bi bi-x-circle-fill text-danger fs-1 mb-3"></i>
    <h2 class="fw-bold">Verification Failed</h2>
    <p class="lead">This verification link is invalid or has expired.</p>
    <a href="<?= base_url('member/login') ?>" class="btn btn-secondary mt-3">
      <i class="bi bi-box-arrow-in-right me-1"></i> Login
    </a>
    <a href="<?= base_url('membership/register') ?>" class="btn btn-brand mt-3">
      <i class="bi bi-person-plus me-1"></i> Register Again
    </a>
  </div>
</div>

<?= $this->endSection() ?>
