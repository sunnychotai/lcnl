<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('partials/page_header', ['overlay' => 'steel', 'heading' => 'Payment cancelled', 'subheading' => 'You have not been charged']) ?>

<div class="container py-4 py-md-5">
  <div class="join-page">

    <?= view('membership/_steps', ['step' => 3]) ?>

    <div class="join-card join-status">
      <div class="join-status-icon" aria-hidden="true"><i class="bi bi-arrow-counterclockwise"></i></div>
      <h2>No problem</h2>
      <div class="join-status-body">
        <p class="mb-0">
          You can become a Life Member whenever you’re ready. Your dashboard also has our bank details
          if you’d rather pay by bank transfer.
        </p>
      </div>

      <a class="btn btn-brand btn-lg rounded-pill px-5" href="<?= base_url('account/dashboard') ?>">Back to my dashboard</a>

      <p class="join-status-help">
        Had a problem paying? Email <a href="mailto:membership@lcnl.org">membership@lcnl.org</a>.
      </p>
    </div>

  </div>
</div>

<?= $this->endSection() ?>
