<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var \Config\ChristmasMehfil $config
 * @var array $booking
 */
$paid     = ! empty($booking['payment_received']);
$cancelled = $booking['status'] === 'cancelled';
$amount    = '&pound;' . esc(number_format((float) $booking['amount']));
$people    = $config->people((int) $booking['individual_tickets'], (int) $booking['tables']);
$pound     = static fn($n): string => '&pound;' . number_format((float) $n);
?>

<?= view('partials/page_header', [
  'overlay'    => 'darkred',
  'eyebrow'    => 'Christmas Mehfil 2026',
  'heading'    => $paid ? 'Your booking is confirmed' : 'Thank you for booking',
  'subheading' => $config->eventLabel() . ' · ' . $config->venue['name'],
]) ?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      <div class="lcnl-card border-0 shadow-sm mb-4">
        <div class="card-body p-4 text-center">
          <?php if ($cancelled): ?>
            <i class="bi bi-x-circle text-secondary" style="font-size:3rem;" aria-hidden="true"></i>
            <h2 class="h4 fw-bold mt-3 mb-2">This booking has been cancelled</h2>
            <p class="mb-0">
              If you think this is a mistake, please email
              <a href="mailto:<?= esc($config->contactEmail, 'attr') ?>"><?= esc($config->contactEmail) ?></a>
              quoting <?= esc($booking['booking_ref']) ?>.
            </p>
          <?php elseif ($paid): ?>
            <i class="bi bi-patch-check-fill text-success" style="font-size:3rem;" aria-hidden="true"></i>
            <h2 class="h4 fw-bold mt-3 mb-2">See you on <?= esc($config->eventLabel()) ?></h2>
            <p class="mb-0">
              Thank you, <?= esc($booking['first_name']) ?>. We have received your payment of <?= $amount ?>.
            </p>
          <?php else: ?>
            <i class="bi bi-ticket-perforated text-brand" style="font-size:3rem;" aria-hidden="true"></i>
            <h2 class="h4 fw-bold mt-3 mb-2">Thank you, <?= esc($booking['first_name']) ?></h2>
            <p class="mb-0">
              We have received your booking. <strong>Please now pay <?= $amount ?></strong> to the LCNL account below,
              using your reference <strong class="text-nowrap"><?= esc($booking['booking_ref']) ?></strong>.
            </p>
          <?php endif; ?>
        </div>
      </div>

      <?php if (! $paid && ! $cancelled): ?>
        <div class="xmas-important mb-4" role="alert">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <div>
            <strong>Payment must be made within <?= esc($config->payWithinHours) ?> hours to confirm your booking
              (by <?= esc($config->payByLabel($booking['created_at'])) ?>).
              If payment isn&rsquo;t made, your order will be cancelled.</strong>
          </div>
        </div>

        <h2 class="h5 fw-bold text-brand mb-3"><i class="bi bi-bank me-1"></i> Make your payment</h2>
        <?= view('mehfil/_bank', ['config' => $config, 'booking' => $booking]) ?>

        <div class="alert alert-info small">
          <i class="bi bi-envelope-check me-1"></i>
          We have also emailed these details to <strong><?= esc($booking['email']) ?></strong>.
          If it doesn&rsquo;t arrive within a few minutes, please check your spam folder.
          We will email you again once your payment has arrived to confirm your booking.
        </div>
      <?php endif; ?>

      <div class="lcnl-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
          <h2 class="h5 fw-bold text-brand mb-3">Your booking</h2>
          <dl class="row mb-0">
            <dt class="col-sm-4">Reference</dt>
            <dd class="col-sm-8"><span class="badge bg-brand fs-6"><?= esc($booking['booking_ref']) ?></span></dd>

            <dt class="col-sm-4">Name</dt>
            <dd class="col-sm-8"><?= esc($booking['first_name'] . ' ' . $booking['last_name']) ?></dd>

            <dt class="col-sm-4">Email</dt>
            <dd class="col-sm-8"><?= esc($booking['email']) ?></dd>

            <dt class="col-sm-4">Mobile</dt>
            <dd class="col-sm-8"><?= esc($booking['phone']) ?></dd>

            <dt class="col-sm-4">Postcode</dt>
            <dd class="col-sm-8"><?= esc($booking['postcode']) ?></dd>

            <dt class="col-sm-4">LCNL member</dt>
            <dd class="col-sm-8"><?= ! empty($booking['is_member']) ? 'Yes' : 'No' ?></dd>

            <dt class="col-sm-4">Tickets</dt>
            <dd class="col-sm-8">
              <?php if ((int) $booking['individual_tickets'] > 0): ?>
                <div><?= esc($booking['individual_tickets']) ?> individual &times; <?= $pound($booking['individual_price']) ?>
                  = <?= $pound($booking['individual_tickets'] * $booking['individual_price']) ?></div>
              <?php endif; ?>
              <?php if ((int) $booking['tables'] > 0): ?>
                <div><?= esc($booking['tables']) ?> table<?= (int) $booking['tables'] === 1 ? '' : 's' ?> of <?= esc($config->tableSize) ?>
                  &times; <?= $pound($booking['table_price']) ?> = <?= $pound($booking['tables'] * $booking['table_price']) ?></div>
              <?php endif; ?>
              <div class="small text-muted"><?= esc($people) ?> <?= $people === 1 ? 'person' : 'people' ?><?= $booking['price_band'] === 'early' ? ' · early bird price' : '' ?></div>
            </dd>

            <dt class="col-sm-4">Total</dt>
            <dd class="col-sm-8 fw-bold">
              <?= $amount ?> &mdash;
              <?php if ($cancelled): ?>
                <span class="text-secondary">cancelled</span>
              <?php elseif ($paid): ?>
                <span class="text-success">paid</span>
              <?php else: ?>
                <span class="text-danger">awaiting payment</span>
              <?php endif; ?>
            </dd>
          </dl>
        </div>
      </div>

      <div class="lcnl-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
          <h2 class="h5 fw-bold text-brand mb-3">On the night</h2>
          <ul class="mb-0">
            <li><strong><?= esc($config->eventLabel()) ?>, <?= esc($config->times) ?></strong> at <?= esc($config->venueLine()) ?></li>
            <li>Vegetarian dinner by Desi Delite and drinks are included.</li>
            <li>Dress code: your most festive <strong>Christmas outfit or Christmas jumper</strong>; there are prizes for the best dressed!</li>
            <li>Please <strong>bring cash</strong> for the charity raffle (<?= esc(implode(', ', $config->raffle)) ?>).</li>
          </ul>
        </div>
      </div>

      <p class="text-muted small mt-3">
        Spotted a mistake? Email <a href="mailto:<?= esc($config->contactEmail, 'attr') ?>"><?= esc($config->contactEmail) ?></a>
        quoting <?= esc($booking['booking_ref']) ?> and we will put it right.
      </p>

    </div>
  </div>
</div>

<?= $this->endSection() ?>
