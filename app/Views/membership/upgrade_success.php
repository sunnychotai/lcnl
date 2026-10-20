<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('partials/page_header', ['overlay' => 'steel', 'heading' => 'Thank you', 'subheading' => 'for supporting Lohana Community North London']) ?>

<div class="container py-4 py-md-5">
  <div class="join-page">

    <?= view('membership/_steps', ['step' => ($isLife || $paid) ? 4 : 3]) ?>

    <div class="join-card join-status">
      <?php if ($isLife): ?>
        <div class="join-status-icon is-success" aria-hidden="true"><i class="bi bi-trophy-fill"></i></div>
        <h2>You’re now a Life Member</h2>
        <div class="join-status-body">
          <p class="mb-0">Your payment has been received and your membership upgraded. We’ve emailed you a confirmation.</p>
        </div>

      <?php elseif ($paid): ?>
        <div class="join-status-icon is-success" aria-hidden="true"><i class="bi bi-check-lg"></i></div>
        <h2>Payment received</h2>
        <div class="join-status-body">
          <p class="mb-0">
            <?php if ($amountPaid !== null): ?>
              We’ve received your payment of <strong>£<?= esc(number_format($amountPaid, 2)) ?></strong>.
            <?php endif; ?>
            We’re upgrading your membership now. This usually takes a few seconds, and we’ll email you
            as soon as it’s done.
          </p>
        </div>

      <?php else: ?>
        <div class="join-status-icon is-warning" aria-hidden="true"><i class="bi bi-hourglass-split"></i></div>
        <h2>We’re confirming your payment</h2>
        <div class="join-status-body">
          <p class="mb-0">
            Stripe is still confirming your payment. You’ll receive an email once your Life Membership
            is active.
          </p>
        </div>
      <?php endif; ?>

      <a class="btn btn-brand btn-lg rounded-pill px-5" href="<?= base_url('account/dashboard') ?>">Go to my dashboard</a>

      <?php if (!$isLife && !$paid): ?>
        <p class="join-status-help">
          Haven’t heard from us within a day? Email <a href="mailto:membership@lcnl.org">membership@lcnl.org</a>.
        </p>
      <?php endif; ?>
    </div>

  </div>
</div>

<?= $this->endSection() ?>
