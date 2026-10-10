<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php /** @var \Config\Legacy50 $config */ ?>

<section class="legacy-hero d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">Legacy 50 Pledges</h1>
    <p class="lead fs-5 mb-0">100 Donors, One Lasting Legacy &mdash; target <?= esc($config->targetLabel()) ?></p>
  </div>
</section>

<div class="container py-4">

  <?php if ($m = session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc($m) ?></div>
  <?php endif; ?>
  <?php if ($m = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($m) ?></div>
  <?php endif; ?>

  <!-- Summary (admin only — progress is never shown publicly) -->
  <div class="row g-3 mb-4">
    <?php
    $tiles = [
      ['label' => 'Pledges (of ' . $config->targetDonors . ')', 'value' => $stats['pledges'], 'icon' => 'bi-heart-fill', 'tone' => 'text-brand'],
      ['label' => 'Payment received', 'value' => $stats['paid'], 'icon' => 'bi-check-circle-fill', 'tone' => 'text-success'],
      ['label' => 'Awaiting payment', 'value' => $stats['awaiting'], 'icon' => 'bi-hourglass-split', 'tone' => 'text-warning'],
      ['label' => 'Received (with LCNL match)', 'value' => '£' . number_format($stats['received']) . ' (£' . number_format($stats['withMatch']) . ')', 'icon' => 'bi-cash-coin', 'tone' => 'text-success'],
      ['label' => 'Gift Aid declared', 'value' => $stats['giftAid'], 'icon' => 'bi-gift-fill', 'tone' => 'text-secondary'],
      ['label' => 'Gift Aid claimable (paid)', 'value' => '£' . number_format($stats['giftAidEstimate']), 'icon' => 'bi-bank', 'tone' => 'text-secondary'],
    ];
    foreach ($tiles as $t): ?>
      <div class="col-6 col-lg-2">
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
      <?php foreach (['all' => 'All', 'awaiting' => 'Awaiting payment', 'paid' => 'Paid', 'cancelled' => 'Cancelled'] as $key => $label): ?>
        <li class="nav-item">
          <a class="nav-link<?= $filter === $key ? ' active' : '' ?>"
            href="<?= base_url('admin/legacy50' . ($key === 'all' ? '' : '?status=' . $key)) ?>"><?= esc($label) ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="d-flex flex-wrap gap-2">
      <a href="<?= base_url('admin/legacy50/export') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-download me-1"></i> All pledges
      </a>
      <a href="<?= base_url('admin/legacy50/export/plaque') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-award me-1"></i> Plaque list
      </a>
      <a href="<?= base_url('admin/legacy50/export/gift-aid') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-gift me-1"></i> Gift Aid (HMRC)
      </a>
    </div>
  </div>

  <?php if (empty($pledges)): ?>
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center py-5 text-muted">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        No pledges here yet.
      </div>
    </div>
  <?php else: ?>

    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle">
        <thead>
          <tr>
            <th scope="col">Ref</th>
            <th scope="col">Donor</th>
            <th scope="col">Plaque name</th>
            <th scope="col">Gift Aid</th>
            <th scope="col">Payment</th>
            <th scope="col">Pledged</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pledges as $p): ?>
            <?php $cancelled = $p['status'] === 'cancelled'; ?>
            <tr<?= $cancelled ? ' class="table-secondary opacity-75"' : '' ?>>
              <td>
                <code><?= esc($p['pledge_ref']) ?></code>
                <?php if ($p['is_duplicate']): ?>
                  <span class="badge bg-warning text-dark d-block mt-1" title="Same email as an earlier pledge">possible duplicate</span>
                <?php endif; ?>
                <?php if ($cancelled): ?>
                  <span class="badge bg-secondary d-block mt-1">cancelled</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="fw-semibold"><?= esc(trim($p['title'] . ' ' . $p['first_name'] . ' ' . $p['last_name'])) ?></span>
                <div class="small">
                  <a href="mailto:<?= esc($p['email'], 'attr') ?>"><?= esc($p['email']) ?></a>
                </div>
                <div class="small">
                  <a href="tel:<?= esc(preg_replace('/\s+/', '', $p['phone']), 'attr') ?>"><?= esc($p['phone']) ?></a>
                </div>
                <?php if (! empty($p['comments'])): ?>
                  <div class="small text-muted" title="<?= esc($p['comments'], 'attr') ?>">
                    <i class="bi bi-chat-left-text"></i> <?= esc(mb_strimwidth($p['comments'], 0, 60, '…')) ?>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <?= esc($p['plaque_name']) ?>
                <?php if ($p['plaque_name'] !== $p['plaque_name_submitted']): ?>
                  <div class="small text-muted">Submitted: <?= esc($p['plaque_name_submitted']) ?></div>
                <?php endif; ?>
                <?php if (! empty($p['admin_notes'])): ?>
                  <div class="small text-muted"><i class="bi bi-sticky"></i> <?= esc(mb_strimwidth($p['admin_notes'], 0, 60, '…')) ?></div>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($p['gift_aid']): ?>
                  <span class="badge bg-success">yes</span>
                  <div class="small text-muted"><?= esc($p['postcode']) ?></div>
                <?php else: ?>
                  <span class="text-muted small">no</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if (! empty($p['payment_received'])): ?>
                  <span class="badge bg-success">received</span>
                  <div class="text-muted small">
                    <?= esc($p['payment_marked_by']) ?><br>
                    <?= esc(date('j M H:i', strtotime($p['payment_received_at']))) ?>
                  </div>
                <?php else: ?>
                  <span class="badge bg-warning text-dark">awaiting</span>
                  <div class="text-muted small">
                    <?= $p['payment_timing'] === 'paid' ? 'says paid' : 'will pay' ?>
                  </div>
                <?php endif; ?>
                <div class="small mt-1">Ref: <strong><?= esc($p['payment_reference']) ?></strong></div>
                <?php if ((int) $p['reminder_count'] > 0): ?>
                  <div class="small text-muted">
                    <?= (int) $p['reminder_count'] ?> reminder<?= (int) $p['reminder_count'] === 1 ? '' : 's' ?>,
                    last <?= esc(date('j M', strtotime($p['reminder_sent_at']))) ?>
                  </div>
                <?php endif; ?>
              </td>
              <td class="small text-muted">
                <?= esc(date('j M Y', strtotime($p['created_at']))) ?><br>
                <?= esc(date('H:i', strtotime($p['created_at']))) ?>
              </td>
              <td>
                <div class="d-flex flex-column gap-1">
                  <?php if (! $cancelled): ?>
                    <form action="<?= base_url('admin/legacy50/payment/' . $p['id']) ?>" method="post"
                      <?= ! empty($p['payment_received']) ? 'onsubmit="return confirm(\'Mark this pledge as unpaid?\');"' : '' ?>>
                      <?= csrf_field() ?>
                      <button type="submit"
                        class="btn btn-sm w-100 <?= ! empty($p['payment_received']) ? 'btn-outline-secondary' : 'btn-success' ?>">
                        <?= ! empty($p['payment_received']) ? 'Mark unpaid' : 'Mark paid' ?>
                      </button>
                    </form>

                    <?php if ($p['status'] === 'pledged'): ?>
                      <form action="<?= base_url('admin/legacy50/remind/' . $p['id']) ?>" method="post"
                        onsubmit="return confirm('Email a payment reminder to <?= esc($p['first_name'] . ' ' . $p['last_name'], 'js') ?>?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-primary w-100">Send reminder</button>
                      </form>
                    <?php endif; ?>
                  <?php endif; ?>

                  <a href="<?= base_url('admin/legacy50/edit/' . $p['id']) ?>" class="btn btn-sm btn-outline-secondary w-100">Edit</a>

                  <?php if ($cancelled): ?>
                    <form action="<?= base_url('admin/legacy50/reinstate/' . $p['id']) ?>" method="post">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn btn-sm btn-outline-success w-100">Reinstate</button>
                    </form>
                  <?php else: ?>
                    <form action="<?= base_url('admin/legacy50/cancel/' . $p['id']) ?>" method="post"
                      onsubmit="return confirm('Cancel the pledge from <?= esc($p['first_name'] . ' ' . $p['last_name'], 'js') ?>?');">
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
        <li>Public pledge page: <a href="<?= esc(site_url($config->slug)) ?>"><?= esc(site_url($config->slug)) ?></a>.
          Pledges close at midnight on <strong><?= esc($config->closingLabel()) ?></strong>.</li>
        <li>Tick <strong>Mark paid</strong> once the transfer shows on the LCF statement &mdash; the donor gets a thank-you email the first time.</li>
        <li>The <strong>Gift Aid (HMRC)</strong> export lists paid Gift Aid donations in the column order of HMRC&rsquo;s Charities Online schedule.</li>
        <li>New pledges are emailed to <?= esc(implode(', ', $config->notifyEmails) ?: 'nobody — set legacy50.notifyEmails') ?>.</li>
      </ul>
    </div>
  </div>

</div>

<?= $this->endSection() ?>
