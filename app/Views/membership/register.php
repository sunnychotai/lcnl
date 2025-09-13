<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2"><i class="bi bi-person-plus-fill me-2"></i>Register</h1>
    <p class="lead fs-6 mb-0">Quick, mobile-first sign up</p>
  </div>
</section>

<div class="container py-4">
  <?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $err): ?>
          <li><?= esc($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="form-card">
        <h3 class="fw-bold mb-3">Create your LCNL account</h3>
        <form method="post" action="<?= route_to('membership.create') ?>" novalidate>
          <?= csrf_field() ?>

          <div class="row g-3">
            <div class="col-sm-6">
              <label class="form-label">First Name*</label>
              <input type="text" name="first_name" value="<?= old('first_name') ?>" class="form-control"
                     autocomplete="given-name" required>
            </div>
            <div class="col-sm-6">
              <label class="form-label">Surname*</label>
              <input type="text" name="last_name" value="<?= old('last_name') ?>" class="form-control"
                     autocomplete="family-name" required>
            </div>

            <div class="col-12">
              <label class="form-label">Email*</label>
              <input type="email" name="email" value="<?= old('email') ?>" class="form-control"
                     inputmode="email" autocomplete="email" required>
            </div>

            <div class="col-12">
              <label class="form-label">Mobile (optional)</label>
              <input type="tel" name="mobile" value="<?= old('mobile') ?>" class="form-control"
                     inputmode="tel" autocomplete="tel" placeholder="+447..." pattern="^\+?\d{7,15}$">
              <div class="input-help">Use international format, e.g. +447...</div>
            </div>

            <div class="col-sm-6">
              <label class="form-label">Password*</label>
              <input type="password" name="password" class="form-control" autocomplete="new-password" required>
            </div>
            <div class="col-sm-6">
              <label class="form-label">Confirm Password*</label>
              <input type="password" name="pass_confirm" class="form-control" autocomplete="new-password" required>
            </div>

            <div class="col-sm-6">
              <label class="form-label">Postcode (optional)</label>
              <input type="text" name="postcode" value="<?= old('postcode') ?>" class="form-control"
                     autocomplete="postal-code" maxlength="12">
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="consent" id="consent" value="1" required>
                <label class="form-check-label" for="consent">
                  I consent to LCNL storing my details and contacting me about membership and events.
                </label>
              </div>
            </div>

            <div class="col-12">
              <button class="btn btn-brand btn-lg rounded-pill px-4">
                <i class="bi bi-check2-circle me-2"></i> Create Account
              </button>
            </div>
          </div>
        </form>
      </div>

      <p class="text-muted small mt-3">
        Already a member? <a href="<?= base_url('login') ?>">Login here</a>.
      </p>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
