<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-1"><i class="bi bi-trophy-fill me-2"></i>Thank You</h1>
    <p class="mb-0 opacity-75">for supporting Lohana Community North London</p>
  </div>
</section>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4 p-md-5 text-center">

          <?php if ($isLife): ?>
            <i class="bi bi-patch-check-fill text-success" style="font-size: 3rem;"></i>
            <h2 class="h3 fw-bold mt-2 mb-2">You’re now a Life Member</h2>
            <p class="text-muted mb-4">
              Your payment has been received and your membership has been upgraded.
              We’ve emailed you a confirmation.
            </p>

          <?php elseif ($paid): ?>
            <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
            <h2 class="h3 fw-bold mt-2 mb-2">Payment received</h2>
            <p class="text-muted mb-4">
              <?php if ($amountPaid !== null): ?>
                We’ve received your payment of <strong>£<?= esc(number_format($amountPaid, 2)) ?></strong>.
              <?php endif; ?>
              We’re upgrading your membership now. This usually takes a few seconds, and we’ll email you
              as soon as it’s done.
            </p>

          <?php else: ?>
            <i class="bi bi-hourglass-split text-accent" style="font-size: 3rem;"></i>
            <h2 class="h3 fw-bold mt-2 mb-2">We’re confirming your payment</h2>
            <p class="text-muted mb-4">
              Stripe is still confirming your payment. You’ll receive an email once your Life Membership
              is active. If you don’t hear from us within a day, contact
              <a href="mailto:membership@lcnl.org">membership@lcnl.org</a>.
            </p>
          <?php endif; ?>

          <a class="btn btn-brand rounded-pill px-4" href="<?= base_url('account/dashboard') ?>">
            <i class="bi bi-speedometer2 me-2"></i> Go to my dashboard
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
