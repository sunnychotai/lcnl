<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/** @var \Config\Legacy50 $config */
$fullName = trim(($pledge['title'] ? $pledge['title'] . ' ' : '') . $pledge['first_name'] . ' ' . $pledge['last_name']);
?>

<?php $this->setVar('heading', 'Thank you for your pledge'); ?>
<?php $this->setVar('subheading', '50 Years of LCNL – 100 Donors, One Lasting Legacy'); ?>
<?= $this->include('legacy50/_hero') ?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      <div class="lcnl-card border-0 shadow-sm mb-4">
        <div class="card-body p-4 text-center">
          <i class="bi bi-heart-fill text-brand" style="font-size:3rem;" aria-hidden="true"></i>
          <h2 class="h4 fw-bold mt-3 mb-2">Thank you, <?= esc($pledge['first_name']) ?></h2>
          <p class="mb-0">
            Thank you for making your pledge. <strong>Please now make payment</strong> to the account
            below using the reference you gave us.
          </p>
        </div>
      </div>

      <h2 class="h5 fw-bold text-brand mb-3"><i class="bi bi-bank me-1"></i> Make your payment</h2>
      <?php $this->setVar('reference', $pledge['payment_reference']); ?>
      <?= $this->include('legacy50/_bank') ?>

      <div class="alert alert-info small">
        <i class="bi bi-envelope-check me-1"></i>
        We have also emailed these details to <strong><?= esc($pledge['email']) ?></strong>.
        If it doesn&rsquo;t arrive within a few minutes, please check your spam folder.
      </div>

      <div class="lcnl-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
          <h2 class="h5 fw-bold text-brand mb-3">Your pledge</h2>
          <dl class="row mb-0">
            <dt class="col-sm-4">Pledge reference</dt>
            <dd class="col-sm-8"><span class="badge bg-brand"><?= esc($pledge['pledge_ref']) ?></span></dd>

            <dt class="col-sm-4">Name</dt>
            <dd class="col-sm-8"><?= esc($fullName) ?></dd>

            <dt class="col-sm-4">Email</dt>
            <dd class="col-sm-8"><?= esc($pledge['email']) ?></dd>

            <dt class="col-sm-4">Mobile</dt>
            <dd class="col-sm-8"><?= esc($pledge['phone']) ?></dd>

            <dt class="col-sm-4">Name on the plaque</dt>
            <dd class="col-sm-8 fw-semibold"><?= esc($pledge['plaque_name']) ?></dd>

            <dt class="col-sm-4">Donation</dt>
            <dd class="col-sm-8">&pound;<?= esc(number_format((float) $pledge['amount'])) ?>
              <span class="text-muted">(matched by LCNL to &pound;<?= esc(number_format((float) $pledge['amount'] + $config->match)) ?>)</span>
            </dd>

            <dt class="col-sm-4">Payment reference</dt>
            <dd class="col-sm-8 fw-semibold"><?= esc($pledge['payment_reference']) ?></dd>

            <dt class="col-sm-4">Gift Aid</dt>
            <dd class="col-sm-8">
              <?php if ($pledge['gift_aid']): ?>
                Yes &mdash; declared for <?= esc(implode(', ', array_filter([
                  $pledge['house_name_number'], $pledge['street'], $pledge['town'], $pledge['postcode'],
                ]))) ?>
              <?php else: ?>
                No
              <?php endif; ?>
            </dd>
          </dl>
        </div>
      </div>

      <p class="text-muted small">
        Spotted a mistake? Email <a href="mailto:info@lcnl.org">info@lcnl.org</a> quoting
        <?= esc($pledge['pledge_ref']) ?> and we will put it right.
      </p>

    </div>
  </div>
</div>

<?= $this->endSection() ?>
