<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-1">
      <i class="bi bi-check-circle-fill me-2"></i>Registration Received
    </h1>
    <p class="mb-0 opacity-75">LCNL Golf Event 2026 &mdash; Moor Park Golf Club</p>
  </div>
</section>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

      <!-- Success message -->
      <div class="alert alert-success shadow-sm mb-4">
        <h5 class="alert-heading">
          <i class="bi bi-envelope-check me-2"></i>Thank you for registering!
        </h5>
        <p class="mb-0">
          Your registration for the <strong>LCNL Golf Event 2026</strong> has been received.
          A confirmation email has been sent to each player's email address.
          Your place will be confirmed once payment has been received.
        </p>
      </div>

      <!-- Registration summary -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
          <h5 class="mb-0 fw-bold"><i class="bi bi-clipboard-check me-2"></i>Registration Summary</h5>
        </div>
        <div class="card-body p-4">

          <div class="mb-3">
            <span class="text-muted small">Reference Number</span>
            <div class="fw-bold fs-5 font-monospace text-brand"><?= esc($reg['registration_ref']) ?></div>
          </div>

          <div class="mb-3">
            <span class="text-muted small">Team Name</span>
            <div class="fw-semibold"><?= esc($reg['team_name']) ?></div>
          </div>

          <div class="mb-3">
            <span class="text-muted small">Date &amp; Time</span>
            <div><?= date('l j F Y \a\t g:i a', strtotime($reg['created_at'])) ?></div>
          </div>

          <div class="mb-3">
            <span class="text-muted small">Status</span>
            <div><span class="badge bg-warning text-dark">Submitted – Awaiting Payment</span></div>
          </div>

          <hr>

          <?php
          $players = [];
          if (!empty($reg['p1_first_name'])) $players[] = ['num' => 1, 'prefix' => 'p1'];
          if (!empty($reg['p2_first_name'])) $players[] = ['num' => 2, 'prefix' => 'p2'];
          if (!empty($reg['p3_first_name'])) $players[] = ['num' => 3, 'prefix' => 'p3'];
          ?>

          <?php foreach ($players as $idx => $p):
            $px = $p['prefix'];
          ?>
            <?php if ($idx > 0): ?><hr><?php endif; ?>
            <div class="mb-1">
              <span class="fw-semibold">Player <?= $p['num'] ?></span>
            </div>
            <dl class="row mb-0 small">
              <dt class="col-sm-4 text-muted">Name</dt>
              <dd class="col-sm-8"><?= esc($reg[$px . '_first_name'] . ' ' . $reg[$px . '_last_name']) ?></dd>

              <dt class="col-sm-4 text-muted">Email</dt>
              <dd class="col-sm-8"><?= esc($reg[$px . '_email']) ?></dd>

              <dt class="col-sm-4 text-muted">Phone</dt>
              <dd class="col-sm-8"><?= esc($reg[$px . '_phone']) ?></dd>

              <dt class="col-sm-4 text-muted">Handicap</dt>
              <dd class="col-sm-8"><?= esc($reg[$px . '_handicap']) ?></dd>

              <dt class="col-sm-4 text-muted">Meal</dt>
              <dd class="col-sm-8">
                <?= $reg[$px . '_meal'] === 'vegetarian' ? 'Vegetarian' : 'Non-Vegetarian' ?>
              </dd>

              <dt class="col-sm-4 text-muted">T-Shirt Size</dt>
              <dd class="col-sm-8"><?= esc($reg[$px . '_tshirt']) ?></dd>
            </dl>
          <?php endforeach; ?>

        </div>
      </div>

      <!-- Payment / Bank Details -->
      <div class="card border-0 shadow-sm mb-4 border-start border-4 border-warning">
        <div class="card-header bg-warning-subtle border-bottom py-3">
          <h5 class="mb-0 fw-bold">
            <i class="bi bi-bank me-2"></i>Payment Instructions
          </h5>
        </div>
        <div class="card-body p-4">

          <div class="alert alert-info small mb-4">
            <i class="bi bi-info-circle me-2"></i>
            Your registration is <strong>not yet confirmed</strong>. Please make payment using the
            bank details below and use your unique reference number so we can match your payment.
            Once payment is received, your place will be confirmed and you will be notified.
          </div>

          <dl class="row mb-0">
            <dt class="col-sm-4">Account Name</dt>
            <dd class="col-sm-8">Lohana Community North London</dd>

            <dt class="col-sm-4">Sort Code</dt>
            <dd class="col-sm-8 font-monospace">40-23-13</dd>

            <dt class="col-sm-4">Account Number</dt>
            <dd class="col-sm-8 font-monospace">21497995</dd>

            <dt class="col-sm-4">Payment Amount</dt>
            <dd class="col-sm-8">To be confirmed – pricing details will be announced shortly</dd>

            <dt class="col-sm-4 fw-bold text-brand">Your Reference</dt>
            <dd class="col-sm-8">
              <span class="fw-bold font-monospace fs-5 text-brand">
                <?= esc($reg['registration_ref']) ?>
              </span>
              <div class="form-text">You <strong>must</strong> include this reference with your payment.</div>
            </dd>
          </dl>

        </div>
      </div>

      <!-- Contact -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-2"><i class="bi bi-question-circle me-2"></i>Questions?</h6>
          <p class="mb-0 small">
            Please contact <strong>Dhiru Savani</strong> (Convenor) on <strong>07956 492825</strong>
            or any of the event organisers listed on the
            <a href="<?= site_url('golf') ?>">Golf Event page</a>.
          </p>
        </div>
      </div>

      <div class="text-center">
        <a href="<?= site_url('/') ?>" class="btn btn-outline-secondary rounded-pill px-4">
          <i class="bi bi-house me-2"></i>Return to Home
        </a>
      </div>

    </div>
  </div>
</div>

<?= $this->endSection() ?>
