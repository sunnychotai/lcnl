<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/** @var \Config\ChristmasMehfil $config */
$base  = 'admin/christmas-mehfil';
$pound = static fn($n): string => '£' . number_format((float) $n);
?>

<section class="hero-lcnl-watermark hero-overlay-ocean d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">Christmas Mehfil Bookings</h1>
    <p class="lead fs-5 mb-0"><?= esc($config->eventLabel()) ?> &mdash;
      <?= $config->isEarlyBird() ? 'early bird prices until ' . esc($config->earlyBirdLabel()) : 'standard prices' ?></p>
  </div>
</section>

<div class="container py-4">

  <?php if ($m = session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc($m) ?></div>
  <?php endif; ?>
  <?php if ($m = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($m) ?></div>
  <?php endif; ?>

  <div class="row g-3 mb-4">
    <?php
    $tiles = [
      ['label' => 'People booked' . ($config->capacity > 0 ? ' of ' . $config->capacity : ''), 'value' => $stats['people'], 'icon' => 'bi-people-fill', 'tone' => 'text-brand'],
      ['label' => 'Tables · individual', 'value' => $stats['tables'] . ' · ' . $stats['individual'], 'icon' => 'bi-ticket-perforated', 'tone' => 'text-brand'],
      ['label' => 'Bookings (' . $stats['members'] . ' by members)', 'value' => $stats['bookings'], 'icon' => 'bi-journal-check', 'tone' => 'text-brand'],
      ['label' => 'Paid (' . $stats['paidPeople'] . ' people)', 'value' => $stats['paid'], 'icon' => 'bi-check-circle-fill', 'tone' => 'text-success'],
      ['label' => 'Awaiting payment', 'value' => $stats['awaiting'], 'icon' => 'bi-hourglass-split', 'tone' => 'text-warning'],
      ['label' => 'Overdue (over ' . $config->payWithinHours . 'h)', 'value' => $stats['overdue'], 'icon' => 'bi-exclamation-triangle-fill', 'tone' => $stats['overdue'] > 0 ? 'text-danger' : 'text-secondary'],
      ['label' => 'Received', 'value' => $pound($stats['received']), 'icon' => 'bi-cash-coin', 'tone' => 'text-success'],
      ['label' => 'Expected', 'value' => $pound($stats['expected']), 'icon' => 'bi-bank', 'tone' => 'text-secondary'],
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
      <?php foreach (['all' => 'All', 'awaiting' => 'Awaiting payment', 'overdue' => 'Overdue', 'paid' => 'Paid', 'cancelled' => 'Cancelled'] as $key => $label): ?>
        <li class="nav-item">
          <a class="nav-link<?= $filter === $key ? ' active' : '' ?>"
            href="<?= base_url($base . ($key === 'all' ? '' : '?status=' . $key)) ?>"><?= esc($label) ?>
            <?php if ($key === 'overdue' && $stats['overdue'] > 0): ?>
              <span class="badge bg-danger ms-1"><?= $stats['overdue'] ?></span>
            <?php endif; ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
    <a href="<?= base_url($base . '/export') ?>" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-download me-1"></i> Export CSV
    </a>
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
      <table class="table table-striped table-bordered align-middle">
        <thead>
          <tr>
            <th scope="col">Reference</th>
            <th scope="col">Name</th>
            <th scope="col">Tickets</th>
            <th scope="col">Payment</th>
            <th scope="col">Booked</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($bookings as $r): ?>
            <?php
            $cancelled = $r['status'] === 'cancelled';
            $rowClass  = $cancelled ? 'table-secondary opacity-75' : ($r['is_overdue'] ? 'table-danger' : '');
            $who       = esc($r['first_name'] . ' ' . $r['last_name'], 'js');
            ?>
            <tr<?= $rowClass ? ' class="' . $rowClass . '"' : '' ?>>
              <td>
                <code class="fs-6"><?= esc($r['booking_ref']) ?></code>
                <?php if ($r['is_duplicate']): ?>
                  <span class="badge bg-warning text-dark d-block mt-1" title="Same email as an earlier booking">same email</span>
                <?php endif; ?>
                <?php if ($cancelled): ?>
                  <span class="badge bg-secondary d-block mt-1">cancelled</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="fw-semibold"><?= esc($r['first_name'] . ' ' . $r['last_name']) ?></span>
                <?php if (! empty($r['is_member'])): ?>
                  <span class="badge bg-brand ms-1">member</span>
                <?php endif; ?>
                <div class="small"><a href="mailto:<?= esc($r['email'], 'attr') ?>"><?= esc($r['email']) ?></a></div>
                <div class="small">
                  <a href="tel:<?= esc(preg_replace('/\s+/', '', $r['phone']), 'attr') ?>"><?= esc($r['phone']) ?></a>
                  &middot; <?= esc($r['postcode']) ?>
                </div>
                <?php if (! empty($r['admin_notes'])): ?>
                  <div class="small text-muted"><i class="bi bi-sticky"></i> <?= esc(mb_strimwidth($r['admin_notes'], 0, 60, '…')) ?></div>
                <?php endif; ?>
              </td>
              <td>
                <?php if ((int) $r['tables'] > 0): ?>
                  <div><?= esc($r['tables']) ?> table<?= (int) $r['tables'] === 1 ? '' : 's' ?></div>
                <?php endif; ?>
                <?php if ((int) $r['individual_tickets'] > 0): ?>
                  <div><?= esc($r['individual_tickets']) ?> individual</div>
                <?php endif; ?>
                <div class="small text-muted"><?= esc($r['people']) ?> people</div>
              </td>
              <td>
                <div class="fw-bold"><?= esc($pound($r['amount'])) ?>
                  <?php if ($r['price_band'] === 'early'): ?><span class="badge bg-info text-dark">early bird</span><?php endif; ?>
                </div>
                <?php if (! empty($r['payment_received'])): ?>
                  <span class="badge bg-success">received</span>
                  <div class="text-muted small">
                    <?= esc($r['payment_marked_by']) ?><br>
                    <?= esc(date('j M H:i', strtotime($r['payment_received_at']))) ?>
                  </div>
                <?php elseif (! $cancelled): ?>
                  <span class="badge <?= $r['is_overdue'] ? 'bg-danger' : 'bg-warning text-dark' ?>">
                    <?= $r['is_overdue'] ? 'overdue' : 'awaiting' ?>
                  </span>
                  <div class="small <?= $r['is_overdue'] ? 'text-danger fw-semibold' : 'text-muted' ?>">
                    pay by <?= esc($config->payBy($r['created_at'])->format('D j M, H:i')) ?>
                  </div>
                <?php endif; ?>
                <?php if ((int) $r['reminder_count'] > 0): ?>
                  <div class="small text-muted">
                    <?= (int) $r['reminder_count'] ?> reminder<?= (int) $r['reminder_count'] === 1 ? '' : 's' ?>,
                    last <?= esc(date('j M', strtotime($r['reminder_sent_at']))) ?>
                  </div>
                <?php endif; ?>
              </td>
              <td class="small text-muted">
                <?= esc(date('j M Y', strtotime($r['created_at']))) ?><br>
                <?= esc(date('H:i', strtotime($r['created_at']))) ?>
              </td>
              <td>
                <div class="d-flex flex-column gap-1">
                  <?php if (! $cancelled): ?>
                    <form action="<?= base_url($base . '/payment/' . $r['id']) ?>" method="post"
                      onsubmit="return confirm('<?= ! empty($r['payment_received'])
                        ? 'Mark this booking as unpaid?'
                        : 'Mark ' . $who . ' as paid (' . esc($pound($r['amount']), 'js') . ')? They will be emailed to confirm their booking.' ?>');">
                      <?= csrf_field() ?>
                      <button type="submit"
                        class="btn btn-sm w-100 <?= ! empty($r['payment_received']) ? 'btn-outline-secondary' : 'btn-success' ?>">
                        <?= ! empty($r['payment_received']) ? 'Mark unpaid' : 'Mark paid' ?>
                      </button>
                    </form>

                    <?php if ($r['status'] === 'booked'): ?>
                      <form action="<?= base_url($base . '/remind/' . $r['id']) ?>" method="post"
                        onsubmit="return confirm('Email a payment reminder to <?= $who ?>?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-primary w-100">Send reminder</button>
                      </form>
                    <?php endif; ?>
                  <?php endif; ?>

                  <a href="<?= base_url($base . '/edit/' . $r['id']) ?>" class="btn btn-sm btn-outline-secondary w-100">Edit</a>

                  <?php if ($cancelled): ?>
                    <form action="<?= base_url($base . '/reinstate/' . $r['id']) ?>" method="post">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn btn-sm btn-outline-success w-100">Reinstate</button>
                    </form>
                  <?php else: ?>
                    <form action="<?= base_url($base . '/cancel/' . $r['id']) ?>" method="post"
                      onsubmit="return confirm('Cancel the booking from <?= $who ?>? Their places become available to others.' + (this.notify.checked ? ' They will be emailed that it was cancelled for non-payment.' : ''));">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn btn-sm btn-outline-danger w-100">Cancel</button>
                      <div class="form-check small mt-1">
                        <input class="form-check-input" type="checkbox" name="notify" value="1" id="notify<?= $r['id'] ?>"
                          <?= empty($r['payment_received']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="notify<?= $r['id'] ?>">email booker</label>
                      </div>
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
          Early bird prices (&pound;<?= $config->prices['early']['individual'] ?> / &pound;<?= $config->prices['early']['table'] ?>)
          run to the end of <strong><?= esc($config->earlyBirdLabel()) ?></strong>, then
          &pound;<?= $config->prices['standard']['individual'] ?> / &pound;<?= $config->prices['standard']['table'] ?>.
          A booking keeps the price it was made at.</li>
        <li>Online booking closes at midnight on <strong><?= esc($config->closingLabel()) ?></strong>
          <?= $config->capacity > 0 ? 'or when ' . $config->capacity . ' people are booked (a table counts as ' . $config->tableSize . ')' : '' ?>.
          Cancelled bookings free their places.</li>
        <li>Bookers pay by bank transfer using their reference (e.g. XMAS-SUNCHO). Tick <strong>Mark paid</strong> once
          it shows on the LCNL statement: they get a &ldquo;booking confirmed&rdquo; email the first time.</li>
        <li>Nothing is cancelled automatically. Bookings unpaid after <?= $config->payWithinHours ?> hours turn
          <span class="text-danger fw-semibold">red</span>; cancel them here. With &ldquo;email booker&rdquo; ticked, they
          are told it was cancelled for non-payment.</li>
        <li>New bookings are emailed to <?= esc(implode(', ', $config->notifyEmails) ?: 'nobody — set mehfil.notifyEmails') ?>.</li>
      </ul>
    </div>
  </div>

</div>

<?= $this->endSection() ?>
