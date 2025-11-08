<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- HERO -->
<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">
      <i class="bi bi-person-lines-fill me-2"></i> My Profile
    </h1>
    <p class="lead fs-6 mb-0">Update your contact details and preferences</p>
  </div>
</section>

<!-- CONTENT -->
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4 p-md-5">

          <!-- Card Title -->
          <h4 class="fw-bold text-brand mb-4">
            <i class="bi bi-person-circle me-2"></i> Profile Details
          </h4>

          <!-- Alerts -->
          <?php if ($errors = session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger rounded-3 small">
              <ul class="mb-0 ps-3">
                <?php foreach ($errors as $err): ?>
                  <li><?= esc($err) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <?php if ($msg = session()->getFlashdata('message')): ?>
            <div class="alert alert-success rounded-3 small">
              <i class="bi bi-check-circle-fill me-2"></i><?= esc($msg) ?>
            </div>
          <?php endif; ?>

          <!-- FORM -->
          <form method="post" action="<?= route_to('account.profile.update') ?>" class="mt-3">
            <?= csrf_field() ?>

            <!-- Readonly Details -->
            <div class="mb-3">
              <label class="form-label fw-semibold">First Name</label>
              <input type="text" class="form-control bg-light"
                value="<?= esc($m['first_name'] ?? '') ?>" readonly title="Cannot be edited">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Last Name</label>
              <input type="text" class="form-control bg-light"
                value="<?= esc($m['last_name'] ?? '') ?>" readonly title="Cannot be edited">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Email Address</label>
              <input type="email" class="form-control bg-light"
                value="<?= esc($m['email'] ?? '') ?>" readonly title="Cannot be edited">
            </div>

            <p class="small text-muted fst-italic mb-4">
              To change your name or email address, please contact the LCNL Membership Team.
            </p>

            <!-- Editable Details -->
            <div class="mb-3">
              <label for="mobile" class="form-label fw-semibold">Mobile</label>
              <input type="text" name="mobile" id="mobile"
                value="<?= old('mobile', $m['mobile'] ?? '') ?>"
                class="form-control">
              <div class="form-text">Format: +447123456789 or 07123456789</div>
            </div>

            <div class="mb-3">
              <label for="address1" class="form-label fw-semibold">Address Line 1</label>
              <input type="text" name="address1" id="address1"
                value="<?= old('address1', $m['address1'] ?? '') ?>"
                class="form-control">
            </div>

            <div class="mb-3">
              <label for="address2" class="form-label fw-semibold">Address Line 2</label>
              <input type="text" name="address2" id="address2"
                value="<?= old('address2', $m['address2'] ?? '') ?>"
                class="form-control">
            </div>

            <div class="mb-3">
              <label for="city" class="form-label fw-semibold">City</label>
              <input type="text" name="city" id="city"
                value="<?= old('city', $m['city'] ?? '') ?>"
                class="form-control">
            </div>

            <div class="mb-4">
              <label for="postcode" class="form-label fw-semibold">Postcode</label>
              <input type="text" name="postcode" id="postcode"
                value="<?= old('postcode', $m['postcode'] ?? '') ?>"
                class="form-control">
            </div>

            <div class="form-check mb-4">
              <input type="checkbox" class="form-check-input" id="consent" name="consent" value="1"
                <?= !empty($m['consent_at']) ? 'checked' : '' ?>>
              <label for="consent" class="form-check-label small">
                I consent to receive LCNL updates and communications.
              </label>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center">
              <a href="<?= route_to('account.dashboard') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left-circle me-2"></i>Cancel
              </a>
              <button type="submit" class="btn btn-brand rounded-pill px-4">
                <i class="bi bi-save me-2"></i>Save Changes
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>