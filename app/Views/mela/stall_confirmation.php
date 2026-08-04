<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php /** @var \Config\MelaStalls $config */ ?>

<section class="hero-lcnl-watermark hero-overlay-moss d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">Stall booking received</h1>
    <p class="lead fs-5 mb-0"><?= esc($config->eventName) ?></p>
  </div>
</section>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      <div class="lcnl-card border-0 shadow-sm mb-4">
        <div class="card-body p-4 text-center">
          <i class="bi bi-check-circle-fill text-success" style="font-size:3rem;"></i>
          <h2 class="h4 fw-bold mt-3 mb-1">Thank you, <?= esc($booking['contact_name']) ?></h2>
          <p class="text-muted mb-3">
            We have received your booking for <strong><?= esc($booking['company_name']) ?></strong>.
          </p>
          <p class="mb-0">
            Your booking reference is
            <span class="badge bg-brand fs-6"><?= esc($booking['booking_ref']) ?></span>
          </p>
        </div>
      </div>

      <div class="lcnl-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
          <h2 class="h5 fw-bold text-brand mb-3">What happens next</h2>

          <p>
            A confirmation has been emailed to
            <strong><?= esc($booking['contact_email']) ?></strong>. If it does not arrive within
            a few minutes, please check your spam folder.
          </p>

          <div class="alert alert-warning mb-3">
            <strong>Your stall is confirmed only once payment has been received.</strong>
            If you have not yet paid the &pound;<?= esc($config->fee) ?> fee, please do so now
            using the details below.
          </div>

          <div class="p-3 rounded border bg-light mb-3">
            <dl class="row mb-0">
              <dt class="col-sm-4">Account name</dt>
              <dd class="col-sm-8"><?= esc($config->bank['accountName']) ?></dd>
              <dt class="col-sm-4">Account number</dt>
              <dd class="col-sm-8"><?= esc($config->bank['accountNumber']) ?></dd>
              <dt class="col-sm-4">Sort code</dt>
              <dd class="col-sm-8"><?= esc($config->bank['sortCode']) ?></dd>
              <dt class="col-sm-4">Reference</dt>
              <dd class="col-sm-8 fw-bold text-brand">
                <?= esc($config->paymentReference($booking['company_name'])) ?>
              </dd>
            </dl>
          </div>

          <h3 class="h6 fw-bold text-brand">On the day</h3>
          <ul class="mb-0">
            <li><strong><?= esc($config->eventLabel()) ?></strong>, <?= esc($config->eventTimes) ?></li>
            <li><?= esc(implode(', ', $config->venueLines())) ?></li>
            <li>Set-up from <strong><?= esc($config->setUpFrom) ?></strong></li>
            <li>Vehicles out of the main car park by <strong><?= esc($config->carParkClear) ?></strong></li>
            <li>Stall size approximately <?= esc($config->stallSize) ?> &mdash; bring your own equipment</li>
            <?php if (! empty($booking['is_food_stall'])): ?>
              <li>Bring a copy of your <strong>Food Hygiene Certificate</strong></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>

      <?= $this->include('mela/_contacts') ?>

    </div>
  </div>
</div>

<?= $this->endSection() ?>
