<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var \Config\Connexions $config
 * @var array       $booking
 * @var array       $attendees
 * @var string|null $payUrl
 */
$paid      = ! empty($booking['payment_received']);
$cancelled = $booking['status'] === 'cancelled';
$amount    = '&pound;' . esc(number_format((float) $booking['amount']));
$tickets   = $config->ticketsLabel((int) $booking['girl_tickets'], (int) $booking['boy_tickets']);
?>

<?= view('partials/page_header', [
  'overlay'    => 'rosewood',
  'eyebrow'    => 'Connexions – Singles Mix & Mingle',
  'heading'    => $paid ? 'Thank you for booking' : ($cancelled ? 'Booking cancelled' : 'Your booking is not paid yet'),
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
              Thank you, <?= esc($booking['first_name']) ?>. We have received your payment of <?= $amount ?>
              and your booking is confirmed.
            </p>
          <?php else: ?>
            <i class="bi bi-credit-card text-brand" style="font-size:3rem;" aria-hidden="true"></i>
            <h2 class="h4 fw-bold mt-3 mb-2">Your payment has not been completed</h2>
            <p class="mb-3">
              Your booking is <strong>not confirmed</strong> until you have paid <?= $amount ?>. You have not been charged.
            </p>
            <?php if ($payUrl): ?>
              <a href="<?= esc($payUrl, 'attr') ?>" class="btn btn-brand btn-lg">
                <i class="bi bi-lock-fill me-1"></i> Pay <?= $amount ?> by card
              </a>
            <?php else: ?>
              <p class="mb-0 text-muted">Online booking has closed. Please email
                <a href="mailto:<?= esc($config->contactEmail, 'attr') ?>"><?= esc($config->contactEmail) ?></a> if you need help.</p>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>

      <?php if ($paid): ?>
        <div class="xmas-important mb-4" role="note">
          <i class="bi bi-envelope-check-fill"></i>
          <div>
            <strong>Please bring your confirmation email with you on the day</strong> (on your phone is fine).
            We have emailed it to <strong><?= esc($booking['email']) ?></strong>, and each attendee has been sent their own.
            Tickets are non-refundable. If it doesn&rsquo;t arrive within a few minutes, please check your spam folder.
          </div>
        </div>
      <?php endif; ?>

      <div class="lcnl-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
          <h2 class="h5 fw-bold text-brand mb-3">Your booking</h2>
          <dl class="row mb-0">
            <dt class="col-sm-4">Reference</dt>
            <dd class="col-sm-8"><span class="badge bg-brand fs-6"><?= esc($booking['booking_ref']) ?></span></dd>

            <dt class="col-sm-4">Booked by</dt>
            <dd class="col-sm-8">
              <?= esc($booking['first_name'] . ' ' . $booking['last_name']) ?><br>
              <span class="small"><?= esc($booking['email']) ?> &middot; <?= esc($booking['phone']) ?></span>
            </dd>

            <dt class="col-sm-4">LCNL member</dt>
            <dd class="col-sm-8"><?= ! empty($booking['is_member']) ? 'Yes' : 'No' ?></dd>

            <dt class="col-sm-4">Tickets</dt>
            <dd class="col-sm-8"><?= esc($tickets) ?> &times; &pound;<?= esc(number_format((float) $booking['ticket_price'])) ?></dd>

            <dt class="col-sm-4">Total</dt>
            <dd class="col-sm-8 fw-bold">
              <?= $amount ?> &mdash;
              <?php if ($cancelled): ?>
                <span class="text-secondary">cancelled</span>
              <?php elseif ($paid): ?>
                <span class="text-success">paid</span>
              <?php else: ?>
                <span class="text-danger">not paid</span>
              <?php endif; ?>
            </dd>
          </dl>

          <h3 class="h6 fw-bold text-brand mt-4 mb-2">Attendees</h3>
          <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
              <thead>
                <tr>
                  <th scope="col">Ticket</th>
                  <th scope="col">Name</th>
                  <th scope="col">Email</th>
                  <th scope="col">Mobile</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($attendees as $a): ?>
                  <tr>
                    <td class="text-nowrap"><?= esc($config->genders[$a['gender']]['one'] . ' ' . $a['position']) ?></td>
                    <td><?= esc($a['first_name'] . ' ' . $a['last_name']) ?></td>
                    <td class="text-break"><?= esc($a['email']) ?></td>
                    <td class="text-nowrap"><?= esc($a['phone']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="lcnl-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
          <h2 class="h5 fw-bold text-brand mb-3">On the night</h2>
          <ul class="mb-0">
            <li><strong><?= esc($config->eventLabel()) ?>, <?= esc($config->times) ?></strong>
              (registration from <?= esc($config->registrationFrom) ?>) at <?= esc($config->venueLine()) ?></li>
            <li>Your ticket includes a welcome drink and nibbles. Other drinks can be bought at the venue.</li>
            <li>Each attendee must <strong>show their confirmation email on arrival</strong>.</li>
          </ul>
        </div>
      </div>

      <p class="text-muted small mt-3">
        Spotted a mistake in the attendee details? Email
        <a href="mailto:<?= esc($config->contactEmail, 'attr') ?>"><?= esc($config->contactEmail) ?></a>
        quoting <?= esc($booking['booking_ref']) ?> before the event, so we can put it right.
      </p>

    </div>
  </div>
</div>

<?= $this->endSection() ?>
