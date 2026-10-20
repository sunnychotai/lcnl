<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('partials/page_header', ['overlay' => 'steel', 'heading' => 'Email confirmed', 'subheading' => 'Welcome to LCNL']) ?>

<div class="container py-4 py-md-5">
  <div class="join-page">

    <?= view('membership/_steps', ['step' => 3]) ?>

    <div class="join-card join-status">
      <div class="join-status-icon is-success" aria-hidden="true"><i class="bi bi-check-lg"></i></div>
      <h2>Your account is active</h2>

      <div class="join-status-body">
        <p class="mb-0">
          Log in to see your membership. From your dashboard you can become a
          <strong>Life Member</strong> for a one-off £75, by card or bank transfer.
        </p>
      </div>

      <a href="<?= base_url('membership/login') ?>" class="btn btn-brand btn-lg rounded-pill px-5">Log in</a>
    </div>

  </div>
</div>

<?= $this->endSection() ?>
