<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php /** @var \Config\MelaStalls $config */ ?>

<section class="hero-lcnl-watermark hero-overlay-ocean d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">Mela Stall Bookings</h1>
    <p class="lead fs-5 mb-0">Golden Jubilee Mela &mdash; <?= esc($config->eventLabel()) ?></p>
  </div>
</section>

<div class="container py-4">

  <?php if ($m = session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc($m) ?></div>
  <?php endif; ?>
  <?php if ($m = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($m) ?></div>
  <?php endif; ?>

  <!-- Summary -->
  <div class="row g-3 mb-4">
    <?php
    $tiles = [
      ['label' => 'Stalls booked', 'value' => $stats['total'], 'icon' => 'bi-shop', 'tone' => 'text-brand'],
      ['label' => 'Payment received', 'value' => $stats['paid'], 'icon' => 'bi-check-circle-fill', 'tone' => 'text-success'],
      ['label' => 'Awaiting payment', 'value' => $stats['unpaid'], 'icon' => 'bi-hourglass-split', 'tone' => 'text-warning'],
      ['label' => 'Food stalls', 'value' => $stats['food'], 'icon' => 'bi-egg-fried', 'tone' => 'text-secondary'],
    ];
    foreach ($tiles as $t): ?>
      <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body text-center">
            <i class="bi <?= esc($t['icon']) ?> fs-3 <?= esc($t['tone']) ?>"></i>
            <div class="fs-3 fw-bold"><?= esc($t['value']) ?></div>
            <div class="text-muted small"><?= esc($t['label']) ?></div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if ($stats['foodNoDoc'] > 0): ?>
    <div class="alert alert-warning">
      <i class="bi bi-exclamation-triangle-fill me-1"></i>
      <strong><?= esc($stats['foodNoDoc']) ?></strong>
      food stall<?= $stats['foodNoDoc'] === 1 ? ' has' : 's have' ?>
      no hygiene certificate uploaded.
    </div>
  <?php endif; ?>

  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <h2 class="h4 mb-0">All bookings</h2>
    <div class="d-flex gap-2">
      <a href="<?= base_url('admin/content/mela-stalls/export') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-download me-1"></i> Export CSV
      </a>
    </div>
  </div>

  <?php if (empty($bookings)): ?>
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center py-5 text-muted">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        No stall bookings yet.
      </div>
    </div>
  <?php else: ?>

    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle">
        <thead>
          <tr>
            <th scope="col">Ref</th>
            <th scope="col">Company</th>
            <th scope="col">Type</th>
            <th scope="col">Contact</th>
            <th scope="col">Docs</th>
            <th scope="col">Payment</th>
            <th scope="col">Booked</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($bookings as $b): ?>
            <?php
            $category = $config->categories[$b['category']] ?? $b['category'];
            if ($b['category'] === 'other' && ! empty($b['category_other'])) {
                $category = $b['category_other'];
            }
            $cancelled = $b['status'] === 'cancelled';
            ?>
            <tr<?= $cancelled ? ' class="table-secondary opacity-75"' : '' ?>>
              <td>
                <code><?= esc($b['booking_ref']) ?></code>
                <?php if ($b['is_duplicate']): ?>
                  <span class="badge bg-warning text-dark d-block mt-1" title="Same company and email as an earlier booking">
                    possible duplicate
                  </span>
                <?php endif; ?>
              </td>
              <td>
                <span class="fw-semibold"><?= esc($b['company_name']) ?></span>
                <?php if ($cancelled): ?>
                  <span class="badge bg-secondary">cancelled</span>
                <?php endif; ?>
                <div class="text-muted small"><?= esc(mb_strimwidth($b['items_description'], 0, 70, '…')) ?></div>
              </td>
              <td>
                <?= esc($category) ?>
                <?php if (! empty($b['is_food_stall'])): ?>
                  <span class="badge bg-info text-dark d-block mt-1">food</span>
                <?php endif; ?>
              </td>
              <td>
                <?= esc($b['contact_name']) ?>
                <div class="small">
                  <a href="tel:<?= esc(preg_replace('/\s+/', '', $b['contact_phone']), 'attr') ?>">
                    <?= esc($b['contact_phone']) ?>
                  </a>
                </div>
                <div class="small">
                  <a href="mailto:<?= esc($b['contact_email'], 'attr') ?>"><?= esc($b['contact_email']) ?></a>
                </div>
                <?php if (! empty($b['vehicle_reg'])): ?>
                  <div class="small text-muted">Vehicle: <?= esc($b['vehicle_reg']) ?></div>
                <?php endif; ?>
              </td>
              <td>
                <?php if (empty($b['documents'])): ?>
                  <?php if (! empty($b['is_food_stall'])): ?>
                    <span class="badge bg-danger">none</span>
                  <?php else: ?>
                    <span class="text-muted small">&mdash;</span>
                  <?php endif; ?>
                <?php else: ?>
                  <?php foreach ($b['documents'] as $doc): ?>
                    <a href="<?= base_url('admin/content/mela-stalls/document/' . $doc['id']) ?>"
                      target="_blank" rel="noopener" class="d-block small text-truncate"
                      style="max-width:150px;" title="<?= esc($doc['original_name'], 'attr') ?>">
                      <i class="bi bi-paperclip"></i> <?= esc($doc['original_name']) ?>
                    </a>
                  <?php endforeach; ?>
                <?php endif; ?>
              </td>
              <td>
                <?php if (! empty($b['payment_received'])): ?>
                  <span class="badge bg-success">received</span>
                  <div class="text-muted small">
                    <?= esc($b['payment_marked_by']) ?><br>
                    <?= esc(date('j M H:i', strtotime($b['payment_received_at']))) ?>
                  </div>
                <?php else: ?>
                  <span class="badge bg-warning text-dark">awaiting</span>
                  <div class="text-muted small">
                    <?= ! empty($b['confirmed_payment']) ? 'says paid' : 'not ticked' ?>
                  </div>
                <?php endif; ?>
                <div class="text-muted small mt-1">
                  Ref: <?= esc($config->paymentReference($b['company_name'])) ?>
                </div>
              </td>
              <td class="small text-muted">
                <?= esc(date('j M Y', strtotime($b['created_at']))) ?><br>
                <?= esc(date('H:i', strtotime($b['created_at']))) ?>
              </td>
              <td>
                <div class="d-flex flex-column gap-1">
                  <form action="<?= base_url('admin/content/mela-stalls/payment/' . $b['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit"
                      class="btn btn-sm w-100 <?= ! empty($b['payment_received']) ? 'btn-outline-secondary' : 'btn-success' ?>">
                      <?= ! empty($b['payment_received']) ? 'Mark unpaid' : 'Mark paid' ?>
                    </button>
                  </form>

                  <?php if (! $cancelled): ?>
                    <form action="<?= base_url('admin/content/mela-stalls/cancel/' . $b['id']) ?>" method="post"
                      onsubmit="return confirm('Cancel the booking for <?= esc($b['company_name'], 'js') ?>?');">
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
    <div class="card-body">
      <h2 class="h6 fw-bold text-brand">Booking link</h2>
      <p class="small text-muted mb-2">
        Unlisted &mdash; not linked anywhere on the site and excluded from search engines.
        Hand it out only to stall holders you have vetted.
      </p>
      <code class="user-select-all"><?= esc(site_url($config->slug)) ?></code>
      <p class="small text-muted mb-0 mt-2">
        Bookings close at midnight on <strong><?= esc($config->closingLabel()) ?></strong>.
      </p>
    </div>
  </div>

</div>

<?= $this->endSection() ?>
