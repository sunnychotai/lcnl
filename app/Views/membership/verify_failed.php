<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('partials/page_header', ['overlay' => 'steel', 'heading' => 'Confirm your email', 'subheading' => 'Lohana Community North London']) ?>

<div class="container py-4 py-md-5">
  <div class="join-page">

    <?php if (!empty($disabled)): ?>
      <div class="join-card join-status">
        <div class="join-status-icon is-danger" aria-hidden="true"><i class="bi bi-x-lg"></i></div>
        <h2>This account is closed</h2>
        <div class="join-status-body">
          <p class="mb-0">
            Please contact <a href="mailto:membership@lcnl.org">membership@lcnl.org</a> if you think this is a mistake.
          </p>
        </div>
      </div>
    <?php else: ?>
      <?= view('membership/_steps', ['step' => 2]) ?>

      <?php if ($err = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc($err) ?>
        </div>
      <?php endif; ?>

      <div class="join-card join-status">
        <div class="join-status-icon is-warning" aria-hidden="true"><i class="bi bi-hourglass-bottom"></i></div>
        <h2>This link has expired</h2>
        <div class="join-status-body">
          <p class="mb-0">
            Confirmation links last 48 hours and can only be used once.
            Enter your email and we’ll send you a new one.
          </p>
        </div>

        <form method="post" action="<?= route_to('membership.resend') ?>" class="join-status-form join-form"
          onsubmit="this.querySelector('button').disabled = true;">
          <?= csrf_field() ?>
          <label class="form-label" for="email">Email address</label>
          <input type="email" id="email" name="email" class="form-control mb-3" inputmode="email"
            autocomplete="email" autocapitalize="off" spellcheck="false" required>
          <button type="submit" class="btn btn-brand rounded-pill px-4 w-100">Send a new link</button>
        </form>

        <p class="join-status-help">
          Already confirmed? <a href="<?= base_url('membership/login') ?>">Log in</a>
          &middot; New to LCNL? <a href="<?= base_url('membership/register') ?>">Register</a>
        </p>
      </div>
    <?php endif; ?>

  </div>
</div>

<?= $this->endSection() ?>
