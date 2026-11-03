<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php /** @var \Config\ChopdaPujan $config */ ?>

<section class="hero-lcnl-watermark hero-overlay-ocean d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">Chopda Pujan Registrations</h1>
    <p class="lead fs-5 mb-0"><?= esc($config->eventLabel()) ?> &mdash; &pound;<?= esc($config->fee) ?> per pooja</p>
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
      ['label' => 'Poojas booked', 'value' => $stats['poojas'], 'icon' => 'bi-journal-check', 'tone' => 'text-brand'],
      ['label' => 'Attending (all)', 'value' => $stats['attendees'], 'icon' => 'bi-people-fill', 'tone' => 'text-brand'],
      ['label' => 'Payment received', 'value' => $stats['paid'], 'icon' => 'bi-check-circle-fill', 'tone' => 'text-success'],
      ['label' => 'Awaiting payment', 'value' => $stats['awaiting'], 'icon' => 'bi-hourglass-split', 'tone' => 'text-warning'],
      ['label' => 'Received', 'value' => '£' . number_format($stats['received']), 'icon' => 'bi-cash-coin', 'tone' => 'text-success'],
      ['label' => 'Expected', 'value' => '£' . number_format($stats['expected']), 'icon' => 'bi-bank', 'tone' => 'text-secondary'],
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
            href="<?= base_url('admin/chopda-pujan' . ($key === 'all' ? '' : '?status=' . $key)) ?>"><?= esc($label) ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
    <a href="<?= base_url('admin/chopda-pujan/export') ?>" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-download me-1"></i> Export CSV
    </a>
  </div>

  <?php if (empty($registrations)): ?>
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center py-5 text-muted">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        No registrations here yet.
      </div>
    </div>
  <?php else: ?>

    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle">
        <thead>
          <tr>
            <th scope="col">Ref</th>
            <th scope="col">Name</th>
            <th scope="col">Attending</th>
            <th scope="col">Payment</th>
            <th scope="col">Registered</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($registrations as $r): ?>
            <?php $cancelled = $r['status'] === 'cancelled'; ?>
            <tr<?= $cancelled ? ' class="table-secondary opacity-75"' : '' ?>>
              <td>
                <code><?= esc($r['registration_ref']) ?></code>
                <?php if ($r['is_duplicate']): ?>
                  <span class="badge bg-warning text-dark d-block mt-1" title="Same email as an earlier registration">same email</span>
                <?php endif; ?>
                <?php if ($cancelled): ?>
                  <span class="badge bg-secondary d-block mt-1">cancelled</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="fw-semibold"><?= esc($r['first_name'] . ' ' . $r['last_name']) ?></span>
                <div class="small">
                  <a href="mailto:<?= esc($r['email'], 'attr') ?>"><?= esc($r['email']) ?></a>
                </div>
                <div class="small">
                  <a href="tel:<?= esc(preg_replace('/\s+/', '', $r['phone']), 'attr') ?>"><?= esc($r['phone']) ?></a>
                </div>
                <?php if (! empty($r['comments'])): ?>
                  <div class="small text-muted" title="<?= esc($r['comments'], 'attr') ?>">
                    <i class="bi bi-chat-left-text"></i> <?= esc(mb_strimwidth($r['comments'], 0, 60, '…')) ?>
                  </div>
                <?php endif; ?>
                <?php if (! empty($r['admin_notes'])): ?>
                  <div class="small text-muted"><i class="bi bi-sticky"></i> <?= esc(mb_strimwidth($r['admin_notes'], 0, 60, '…')) ?></div>
                <?php endif; ?>
              </td>
              <td class="text-center fw-semibold"><?= esc($r['num_attendees']) ?></td>
              <td>
                <?php $card = $r['payment_method'] === 'card'; ?>
                <span class="badge <?= $card ? 'bg-primary' : 'bg-secondary' ?>">
                  <i class="bi <?= $card ? 'bi-credit-card' : 'bi-bank' ?> me-1"></i><?= $card ? 'Card' : 'Bank' ?>
                </span>
                <?php if (! empty($r['payment_received'])): ?>
                  <span class="badge bg-success">received</span>
                  <div class="text-muted small">
                    <?= esc($r['payment_marked_by']) ?><br>
                    <?= esc(date('j M H:i', strtotime($r['payment_received_at']))) ?>
                  </div>
                <?php else: ?>
                  <span class="badge bg-warning text-dark">awaiting</span>
                  <div class="text-muted small">
                    <?= $card ? 'card payment not completed' : ($r['payment_timing'] === 'paid' ? 'says paid' : 'will pay') ?>
                  </div>
                <?php endif; ?>
                <?php if (! empty($r['stripe_payment_intent_id'])): ?>
                  <div class="small mt-1">
                    <a href="<?= esc(\App\Libraries\StripeService::dashboardPaymentUrl($r['stripe_payment_intent_id']), 'attr') ?>"
                      target="_blank" rel="noopener">View in Stripe <i class="bi bi-box-arrow-up-right"></i></a>
                  </div>
                <?php endif; ?>
                <div class="small mt-1">Ref: <strong><?= esc($r['payment_reference']) ?></strong></div>
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
                    <form action="<?= base_url('admin/chopda-pujan/payment/' . $r['id']) ?>" method="post"
                      <?= ! empty($r['payment_received']) ? 'onsubmit="return confirm(\'Mark this registration as unpaid?\');"' : '' ?>>
                      <?= csrf_field() ?>
                      <button type="submit"
                        class="btn btn-sm w-100 <?= ! empty($r['payment_received']) ? 'btn-outline-secondary' : 'btn-success' ?>">
                        <?= ! empty($r['payment_received']) ? 'Mark unpaid' : 'Mark paid' ?>
                      </button>
                    </form>

                    <?php if ($r['status'] === 'registered'): ?>
                      <form action="<?= base_url('admin/chopda-pujan/remind/' . $r['id']) ?>" method="post"
                        onsubmit="return confirm('Email a payment reminder to <?= esc($r['first_name'] . ' ' . $r['last_name'], 'js') ?>?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-primary w-100">Send reminder</button>
                      </form>
                    <?php endif; ?>
                  <?php endif; ?>

                  <a href="<?= base_url('admin/chopda-pujan/edit/' . $r['id']) ?>" class="btn btn-sm btn-outline-secondary w-100">Edit</a>

                  <?php if ($cancelled): ?>
                    <form action="<?= base_url('admin/chopda-pujan/reinstate/' . $r['id']) ?>" method="post">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn btn-sm btn-outline-success w-100">Reinstate</button>
                    </form>
                  <?php else: ?>
                    <form action="<?= base_url('admin/chopda-pujan/cancel/' . $r['id']) ?>" method="post"
                      onsubmit="return confirm('Cancel the registration from <?= esc($r['first_name'] . ' ' . $r['last_name'], 'js') ?>?');">
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
          Online registration closes at midnight on <strong><?= esc($config->closingLabel()) ?></strong>.</li>
        <li>One registration is one pooja (&pound;<?= esc($config->fee) ?>, up to <?= esc($config->yajmansPerPooja) ?> Yajmans).
          &ldquo;Attending&rdquo; is everyone coming, for breakfast numbers.</li>
        <li>Payment methods on offer: <strong><?= esc(implode(' and ', array_map(
              static fn(string $m): string => $m === 'card' ? 'card (Stripe)' : 'bank transfer',
              $config->paymentMethods()
            ))) ?></strong>.
          Switch them with <code>chopda.cardPayments</code> / <code>chopda.bankTransfer</code> in <code>.env</code>.</li>
        <li><strong>Card</strong> payments are marked paid automatically when Stripe confirms them. Refunds are made
          in the Stripe dashboard; then cancel the registration here.</li>
        <li>For a <strong>bank transfer</strong>, tick <strong>Mark paid</strong> once it shows on the LCNL statement
          &mdash; the registrant gets a &ldquo;place confirmed&rdquo; email the first time.</li>
        <li>New registrations are emailed to <?= esc(implode(', ', $config->notifyEmails) ?: 'nobody — set chopda.notifyEmails') ?>.</li>
      </ul>
    </div>
  </div>

</div>

<?= $this->endSection() ?>
