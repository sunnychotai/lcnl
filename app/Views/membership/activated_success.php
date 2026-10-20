<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('partials/page_header', ['overlay' => 'steel', 'heading' => 'Account activated', 'subheading' => 'Welcome to LCNL']) ?>

<div class="container py-4 py-md-5">
  <div class="join-page">
    <div class="join-card join-status">
      <div class="join-status-icon is-success" aria-hidden="true"><i class="bi bi-check-lg"></i></div>
      <h2>Your password is set</h2>
      <div class="join-status-body">
        <p class="mb-0">Your account is now active. Check your details and add your family from your dashboard.</p>
      </div>
      <a href="<?= base_url('account/dashboard') ?>" class="btn btn-brand btn-lg rounded-pill px-5">Go to my dashboard</a>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
