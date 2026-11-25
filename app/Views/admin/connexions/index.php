<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/** @var \Config\Connexions $config */
$base   = 'admin/connexions';
$pound  = static fn($n): string => '£' . number_format((float) $n);
$capOf  = static fn(string $g): string => $config->capacity[$g] > 0 ? ' of ' . $config->capacity[$g] : '';
$badges = [
  'paid'      => ['bg-success', 'paid'],
  'checkout'  => ['bg-info text-dark', 'in checkout'],
  'abandoned' => ['bg-light text-dark border', 'not paid'],
  'cancelled' => ['bg-secondary', 'cancelled'],
];
?>

<section class="hero-lcnl-watermark hero-overlay-ocean d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">Connexions Bookings</h1>
    <p class="lead fs-5 mb-0">Singles Mix &amp; Mingle &mdash; <?= esc($config->eventLabel()) ?></p>
  </div>
</section>

<div class="container py-4">

  <?php if ($m = session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc($m) ?></div>
  <?php endif; ?>
  <?php if ($m = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($m) ?></div>
  <?php endif; ?>

  <?php if (! $config->acceptsCard()): ?>
    <div class="alert alert-warning">
      <i class="bi bi-exclamation-triangle-fill me-1"></i>
      <strong>Online booking is switched off</strong>: set <code>stripe.connexionsPriceId</code> (and the Stripe keys) in
      <code>.env</code> to open it.
    </div>
  <?php endif; ?>

  <div class="row g-3 mb-4">
    <?php
    $tiles = [
      ['label' => 'Girls paid' . $capOf('girl'), 'value' => $stats['girls'], 'icon' => 'bi-person-heart', 'tone' => 'text-brand'],
      ['label' => 'Boys paid' . $capOf('boy'), 'value' => $stats['boys'], 'icon' => 'bi-person-fill', 'tone' => 'text-brand'],
      ['label' => 'Paid bookings (' . $stats['members'] . ' by members)', 'value' => $stats['paid'], 'icon' => 'bi-journal-check', 'tone' => 'text-success'],
      ['label' => 'Received by card', 'value' => $pound($stats['received']), 'icon' => 'bi-credit-card', 'tone' => 'text-success'],
      ['label' => 'Places left (girls · boys)', 'value' => ($stats['left']['girl'] ?? '∞') . ' · ' . ($stats['left']['boy'] ?? '∞'), 'icon' => 'bi-ticket-perforated', 'tone' => 'text-brand'],
      ['label' => 'In checkout now (holding places)', 'value' => $stats['checkout'], 'icon' => 'bi-hourglass-split', 'tone' => 'text-info'],
      ['label' => 'Started but not paid', 'value' => $stats['abandoned'], 'icon' => 'bi-cart-x', 'tone' => 'text-secondary'],
      ['label' => 'Bookings with a repeat email', 'value' => $stats['repeats'], 'icon' => 'bi-exclamation-diamond', 'tone' => $stats['repeats'] > 0 ? 'text-warning' : 'text-secondary'],
    ];
    foreach ($tiles as $t): ?>
      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body text-center">
            <i class="bi <?= esc($t['icon']) ?> fs-3 <?= esc($t['tone']) ?>"></i>
            <div class="fs-5 fw-bold"><?= esc($t['value']) ?></div>
            <div class="text-muted small"><?= esc($t['label']) ?></div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <ul class="nav nav-pills">
      <?php foreach (['all' => 'All', 'paid' => 'Paid', 'checkout' => 'In checkout', 'abandoned' => 'Not paid', 'cancelled' => 'Cancelled'] as $key => $label): ?>
        <li class="nav-item">
          <a class="nav-link<?= $filter === $key ? ' active' : '' ?>"
            href="<?= base_url($base . ($key === 'all' ? '' : '?status=' . $key)) ?>"><?= esc($label) ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="d-flex gap-2">
      <a href="<?= base_url($base . '/attendees') ?>" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-list-check me-1"></i> Door list
      </a>
      <a href="<?= base_url($base . '/export') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-download me-1"></i> Export CSV
      </a>
    </div>
  </div>

  <?php if (empty($bookings)): ?>
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center py-5 text-muted">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        No bookings here yet.
      </div>
    </div>
  <?php else: ?>

    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th scope="col">Reference</th>
            <th scope="col">Booked by</th>
            <th scope="col">Attendees</th>
            <th scope="col">Payment</th>
            <th scope="col">Booked</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($bookings as $b): ?>
            <?php
            [$badgeClass, $badgeText] = $badges[$b['state']];
            $muted = in_array($b['state'], ['cancelled', 'abandoned'], true);
            $ref   = esc($b['booking_ref'], 'js');
            ?>
            <tr<?= $muted ? ' class="table-secondary opacity-75"' : '' ?>>
              <td>
                <code class="fs-6"><?= esc($b['booking_ref']) ?></code>
                <span class="badge <?= $badgeClass ?> d-block mt-1"><?= esc($badgeText) ?></span>
                <?php if ($b['has_repeat']): ?>
                  <span class="badge bg-warning text-dark d-block mt-1" title="An attendee email also appears on another booking">repeat email</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="fw-semibold"><?= esc($b['first_name'] . ' ' . $b['last_name']) ?></span>
                <?php if (! empty($b['is_member'])): ?>
                  <span class="badge bg-brand ms-1">member</span>
                <?php endif; ?>
                <div class="small"><a href="mailto:<?= esc($b['email'], 'attr') ?>"><?= esc($b['email']) ?></a></div>
                <div class="small"><a href="tel:<?= esc(preg_replace('/\s+/', '', $b['phone']), 'attr') ?>"><?= esc($b['phone']) ?></a></div>
                <?php if (! empty($b['admin_notes'])): ?>
                  <div class="small text-muted"><i class="bi bi-sticky"></i> <?= esc(mb_strimwidth($b['admin_notes'], 0, 80, '…')) ?></div>
                <?php endif; ?>
              </td>
              <td class="small">
                <?php foreach ($b['attendees'] as $a): ?>
                  <div class="mb-1">
                    <span class="badge <?= $a['gender'] === 'girl' ? 'bg-danger-subtle text-danger-emphasis' : 'bg-primary-subtle text-primary-emphasis' ?>">
                      <?= esc($config->genders[$a['gender']]['one'] . ' ' . $a['position']) ?></span>
                    <strong><?= esc($a['first_name'] . ' ' . $a['last_name']) ?></strong>
                    <?php if ($a['is_repeat']): ?>
                      <span class="badge bg-warning text-dark" title="This email is also on another booking">repeat</span>
                    <?php endif; ?>
                    <br><span class="text-muted"><?= esc($a['email']) ?> &middot; <?= esc($a['phone']) ?></span>
                  </div>
                <?php endforeach; ?>
              </td>
              <td>
                <div class="fw-bold"><?= esc($pound($b['amount'])) ?></div>
                <div class="small text-muted"><?= esc($config->ticketsLabel((int) $b['girl_tickets'], (int) $b['boy_tickets'])) ?></div>
                <?php if (! empty($b['payment_received'])): ?>
                  <div class="text-muted small">
                    <?= esc($b['payment_marked_by']) ?>, <?= esc(date('j M H:i', strtotime($b['payment_received_at']))) ?>
                    <?php if ($b['stripe_url']): ?>
                      <br><a href="<?= esc($b['stripe_url'], 'attr') ?>" target="_blank" rel="noopener">View in Stripe</a>
                    <?php endif; ?>
                  </div>
                <?php elseif ($b['state'] === 'checkout'): ?>
                  <div class="small text-muted">held until <?= esc(date('H:i', strtotime($b['hold_until']))) ?></div>
                <?php endif; ?>
              </td>
              <td class="small text-muted">
                <?= esc(date('j M Y', strtotime($b['created_at']))) ?><br>
                <?= esc(date('H:i', strtotime($b['created_at']))) ?>
              </td>
              <td>
                <div class="d-flex flex-column gap-1">
                  <a href="<?= base_url($base . '/edit/' . $b['id']) ?>" class="btn btn-sm btn-outline-secondary w-100">Edit details</a>

                  <?php if ($b['status'] === 'paid'): ?>
                    <form action="<?= base_url($base . '/resend/' . $b['id']) ?>" method="post"
                      onsubmit="return confirm('Email the confirmation again to the booker and every attendee on <?= $ref ?>?');">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn btn-sm btn-outline-primary w-100">Resend emails</button>
                    </form>
                  <?php elseif ($b['status'] === 'pending'): ?>
                    <form action="<?= base_url($base . '/payment/' . $b['id']) ?>" method="post"
                      onsubmit="return confirm('Only do this if the payment for <?= $ref ?> is in Stripe (or was taken another way). Mark it paid and email the booker and attendees?');">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn btn-sm btn-outline-success w-100">Mark paid</button>
                    </form>
                  <?php endif; ?>

                  <?php if ($b['status'] === 'cancelled'): ?>
                    <form action="<?= base_url($base . '/reinstate/' . $b['id']) ?>" method="post">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn btn-sm btn-outline-success w-100">Reinstate</button>
                    </form>
                  <?php else: ?>
                    <form action="<?= base_url($base . '/cancel/' . $b['id']) ?>" method="post"
                      onsubmit="return confirm('Cancel booking <?= $ref ?>? Its places become available to others.<?= ! empty($b['payment_received']) ? ' It was paid by card: no refund is made here.' : '' ?>');">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn btn-sm btn-outline-danger w-100">Cancel</button>
                    </form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  <?php endif; ?>

  <div class="card border-0 shadow-sm mt-4">
    <div class="card-body small text-muted">
      <h2 class="h6 fw-bold text-brand">How this works</h2>
      <ul class="mb-0">
        <li>Public page: <a href="<?= esc(site_url($config->slug)) ?>"><?= esc(site_url($config->slug)) ?></a>.
          &pound;<?= esc($config->ticketPrice) ?> a ticket, up to <?= esc($config->maxPerGender) ?> girl and
          <?= esc($config->maxPerGender) ?> boy tickets per booking, paid by card through Stripe.</li>
        <li>Online booking closes at midnight on <strong><?= esc($config->closingLabel()) ?></strong>, or when places run out:
          <?= $config->capacity['girl'] > 0 ? esc($config->capacity['girl']) . ' girls' : 'no limit for girls' ?> and
          <?= $config->capacity['boy'] > 0 ? esc($config->capacity['boy']) . ' boys' : 'no limit for boys' ?>
          (<code>connexions.capacityGirls</code> / <code>connexions.capacityBoys</code> in <code>.env</code>).</li>
        <li>A booker going to Stripe holds their places for <?= esc($config->holdMinutes) ?> minutes (&ldquo;in checkout&rdquo;).
          If they don&rsquo;t pay, the places are released, the booking shows as &ldquo;not paid&rdquo;, and they are emailed a link to try again.</li>
        <li>When a payment goes through, the booker gets a confirmation with every detail, each attendee gets their own,
          and <?= esc(implode(', ', $config->notifyEmails) ?: 'nobody (set connexions.notifyEmails)') ?> is told.</li>
        <li>An attendee email on more than one paid or in-checkout booking is flagged <span class="badge bg-warning text-dark">repeat</span>, but allowed.</li>
        <li>Fixed someone&rsquo;s details? Use <strong>Resend emails</strong> so they have a correct confirmation to show at the door.
          Refunds, if ever given, are made in Stripe.</li>
      </ul>
    </div>
  </div>

</div>

<?= $this->endSection() ?>
