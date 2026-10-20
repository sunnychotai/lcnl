<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('partials/page_header', ['overlay' => 'steel', 'heading' => 'Check your email', 'subheading' => 'One more step to join LCNL']) ?>

<?php $pendingEmail = session('pending_email'); ?>

<div class="container py-4 py-md-5">
  <div class="join-page">

    <?= view('membership/_steps', ['step' => 2]) ?>

    <?php if ($msg = session()->getFlashdata('message')): ?>
      <div class="alert alert-success" role="status">
        <i class="bi bi-check-circle-fill me-2"></i><?= esc($msg) ?>
      </div>
    <?php endif; ?>
    <?php if ($err = session()->getFlashdata('error')): ?>
      <div class="alert alert-danger" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc($err) ?>
      </div>
    <?php endif; ?>

    <div class="join-card join-status">
      <div class="join-status-icon" aria-hidden="true"><i class="bi bi-envelope-paper-fill"></i></div>
      <h2>Confirm your email address</h2>

      <div class="join-status-body">
        <p>
          We’ve sent a link to
          <?= $pendingEmail ? '<strong>' . esc($pendingEmail) . '</strong>' : 'your email address' ?>.
          Click it to confirm your address and activate your account.
        </p>
        <p class="mb-0">The link works for 48 hours. It can take a few minutes to arrive, so check your spam or junk folder too.</p>
      </div>

      <?php if ($pendingEmail): ?>
        <form method="post" action="<?= route_to('membership.resend') ?>"
          onsubmit="this.querySelector('button').disabled = true;">
          <?= csrf_field() ?>
          <input type="hidden" name="email" value="<?= esc($pendingEmail, 'attr') ?>">
          <button type="submit" class="btn btn-outline-brand rounded-pill px-4">Send the email again</button>
        </form>
      <?php endif; ?>

      <p class="join-status-help">
        Typed the wrong address? <a href="<?= base_url('membership/register') ?>">Register again</a> with the right one.<br>
        Still stuck? Email <a href="mailto:membership@lcnl.org">membership@lcnl.org</a>.
      </p>
    </div>

  </div>
</div>

<?= $this->endSection() ?>
