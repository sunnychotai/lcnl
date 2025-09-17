<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">
      <i class="bi bi-person-lines-fill me-2"></i> My Profile
    </h1>
    <p class="lead fs-6 mb-0">Update your contact details and preferences</p>
  </div>
</section>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="lcnl-card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">

          <?php if ($errors = session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach ($errors as $err): ?>
                  <li><?= esc($err) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <?php if ($msg = session()->getFlashdata('message')): ?>
            <div class="alert alert-success"><?= esc($msg) ?></div>
          <?php endif; ?>

          <form method="post" action="<?= route_to('account.profile.update') ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
              <label class="form-label fw-semibold">First Name</label>
              <input type="text" class="form-control" value="<?= esc($m['first_name'] ?? '') ?>" readonly>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Last Name</label>
              <input type="text" class="form-control" value="<?= esc($m['last_name'] ?? '') ?>" readonly>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Email</label>
              <input type="email" class="form-control" value="<?= esc($m['email'] ?? '') ?>" readonly>
            </div>

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

            <div class="mb-3">
              <label for="postcode" class="form-label fw-semibold">Postcode</label>
              <input type="text" name="postcode" id="postcode"
                     value="<?= old('postcode', $m['postcode'] ?? '') ?>"
                     class="form-control">
            </div>

            <div class="form-check mb-3">
              <input type="checkbox" class="form-check-input" id="consent" name="consent" value="1"
                     <?= !empty($m['consent_at']) ? 'checked' : '' ?>>
              <label for="consent" class="form-check-label">
                I consent to receive LCNL updates and communications.
              </label>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-brand rounded-pill">
                <i class="bi bi-save me-2"></i> Save Changes
              </button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
