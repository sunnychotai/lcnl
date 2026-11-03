<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var \Config\ChopdaPujan $config
 * @var array $registration
 * @var string|null $payUrl pay-by-card link, when card is on offer
 */
$paid     = ! empty($registration['payment_received']);
$isCard   = $registration['payment_method'] === 'card';
$fee      = '&pound;' . esc(number_format((float) $registration['amount']));
// A bank registrant still sees the details if bank transfer is later switched off.
$showBank = ! $paid && ($config->acceptsBank() || ! $isCard);
?>

<?= view('partials/page_header', [
  'overlay'    => 'crimsonsunset',
  'eyebrow'    => 'Chopda Pujan 2026',
  'heading'    => 'Thank you for registering',
  'subheading' => $config->eventLabel() . ' · ' . $config->venue['name'],
]) ?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      <?php if ($msg = session('error')): ?>
        <div class="alert alert-warning d-flex align-items-start gap-2" role="alert">
          <i class="bi bi-exclamation-triangle-fill mt-1"></i>
          <div><?= esc($msg) ?></div>
        </div>
      <?php endif; ?>

      <div class="lcnl-card border-0 shadow-sm mb-4">
        <div class="card-body p-4 text-center">
          <?php if ($paid): ?>
            <i class="bi bi-patch-check-fill text-success" style="font-size:3rem;" aria-hidden="true"></i>
            <h2 class="h4 fw-bold mt-3 mb-2">Your place is confirmed</h2>
            <p class="mb-0">
              Thank you, <?= esc($registration['first_name']) ?>. We have received your <?= $fee ?> nyochavar.
              A confirmation email is on its way to <strong><?= esc($registration['email']) ?></strong>.
            </p>
          <?php elseif ($isCard): ?>
            <i class="bi bi-credit-card text-brand" style="font-size:3rem;" aria-hidden="true"></i>
            <h2 class="h4 fw-bold mt-3 mb-2">Your payment has not been completed</h2>
            <p class="mb-3">
              Thank you, <?= esc($registration['first_name']) ?> &mdash; your registration is saved, but your place is
              not confirmed until the <?= $fee ?> nyochavar is paid.
            </p>
            <?php if ($payUrl): ?>
              <a href="<?= esc($payUrl, 'attr') ?>" class="btn btn-brand btn-lg">
                <i class="bi bi-lock-fill me-1"></i> Pay <?= $fee ?> by card
              </a>
            <?php endif; ?>
            <?php if ($showBank): ?>
              <p class="small text-muted mt-3 mb-0">
                <?= $payUrl ? 'Or pay' : 'Please pay' ?> by bank transfer using the details below.
              </p>
            <?php endif; ?>
          <?php else: ?>
            <i class="bi bi-journal-check text-brand" style="font-size:3rem;" aria-hidden="true"></i>
            <h2 class="h4 fw-bold mt-3 mb-2">Thank you, <?= esc($registration['first_name']) ?></h2>
            <p class="mb-0">
              Your registration has been received. <strong>Please now pay the <?= $fee ?> nyochavar</strong> to the
              account below, using your reference. Your place is confirmed once your payment arrives.
            </p>
            <?php if ($payUrl): ?>
              <p class="small mt-3 mb-0">
                Prefer to pay by card? <a href="<?= esc($payUrl, 'attr') ?>" class="fw-semibold">Pay <?= $fee ?> now</a>
                and your place is confirmed straight away.
              </p>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>

      <?php if ($showBank): ?>
        <h2 class="h5 fw-bold text-brand mb-3"><i class="bi bi-bank me-1"></i> <?= $isCard ? 'Pay by bank transfer' : 'Make your payment' ?></h2>
        <?= view('chopda/_bank', ['config' => $config, 'reference' => $registration['payment_reference']]) ?>

        <?php if (! $isCard): ?>
          <div class="alert alert-info small">
            <i class="bi bi-envelope-check me-1"></i>
            We have also emailed these details to <strong><?= esc($registration['email']) ?></strong>.
            If it doesn&rsquo;t arrive within a few minutes, please check your spam folder.
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <div class="lcnl-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
          <h2 class="h5 fw-bold text-brand mb-3">Your registration</h2>
          <dl class="row mb-0">
            <dt class="col-sm-4">Registration ref</dt>
            <dd class="col-sm-8"><span class="badge bg-brand"><?= esc($registration['registration_ref']) ?></span></dd>

            <dt class="col-sm-4">Name</dt>
            <dd class="col-sm-8"><?= esc($registration['first_name'] . ' ' . $registration['last_name']) ?></dd>

            <dt class="col-sm-4">Email</dt>
            <dd class="col-sm-8"><?= esc($registration['email']) ?></dd>

            <dt class="col-sm-4">Mobile</dt>
            <dd class="col-sm-8"><?= esc($registration['phone']) ?></dd>

            <dt class="col-sm-4">Attending</dt>
            <dd class="col-sm-8">
              <?= esc($registration['num_attendees']) ?> <?= (int) $registration['num_attendees'] === 1 ? 'person' : 'people' ?>
              <span class="d-block chopda-rule small">
                Only <?= esc($config->yajmansPerPooja) ?> Yajmans can sit in the pooja; others are welcome to watch.
              </span>
            </dd>

            <dt class="col-sm-4">Nyochavar</dt>
            <dd class="col-sm-8">
              <?= $fee ?> (1 pooja) &mdash;
              <?= $paid ? '<span class="text-success fw-semibold">paid' . ($isCard ? ' by card' : '') . '</span>' : 'not yet paid' ?>
            </dd>

            <?php if ($showBank): ?>
              <dt class="col-sm-4">Payment reference</dt>
              <dd class="col-sm-8 fw-semibold"><?= esc($registration['payment_reference']) ?></dd>
            <?php endif; ?>
          </dl>
        </div>
      </div>

      <div class="lcnl-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
          <h2 class="h5 fw-bold text-brand mb-3">On the day</h2>
          <ul class="mb-0">
            <li><strong><?= esc($config->eventLabel()) ?></strong> at <?= esc($config->venueLine()) ?></li>
            <li>Registration <?= esc($config->registrationTimes) ?>; pooja <?= esc($config->poojaTimes) ?></li>
            <li>All pooja samagri is provided &mdash; please bring your own <strong>book</strong> and <strong>swaroop of Lord Ganesh</strong></li>
          </ul>
        </div>
      </div>

      <?= $this->include('chopda/_contacts') ?>

      <p class="text-muted small mt-3">
        Spotted a mistake? Email <a href="mailto:info@lcnl.org">info@lcnl.org</a> quoting
        <?= esc($registration['registration_ref']) ?> and we will put it right.
      </p>

    </div>
  </div>
</div>

<?= $this->endSection() ?>
