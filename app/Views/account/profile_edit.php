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

          <a href="<?= route_to('account.family') ?>" class="btn btn-outline-brand btn-sm rounded-pill mb-3">
            <i class="bi bi-people me-1"></i> Manage Family
          </a>

          <!-- ================================
               PERSONAL OVERVIEW OF DOB + GENDER
          ================================= -->
          <div class="border rounded-3 p-3 mb-4 bg-light">
            <h6 class="fw-bold text-brand mb-3"><i class="bi bi-person-badge me-2"></i>Personal Information</h6>

            <div class="mb-2">
              <span class="detail-label small text-muted">Date of Birth</span><br>
              <span class="fw-semibold">
                <?= $m['date_of_birth'] ? date('d M Y', strtotime($m['date_of_birth'])) : '<span class="text-muted">Not provided</span>' ?>
              </span>
            </div>

            <div class="mb-1">
              <span class="detail-label small text-muted">Gender</span><br>
              <span class="fw-semibold text-capitalize">
                <?= $m['gender'] ? esc(str_replace('_', ' ', $m['gender'])) : '<span class="text-muted">Not provided</span>' ?>
              </span>
            </div>
          </div>

          <!-- ================================
               FAMILY SUMMARY (READ ONLY)
          ================================= -->
          <?php if (!empty($family)): ?>
            <div class="border rounded-3 p-3 mb-4 bg-light">
              <h6 class="fw-bold text-brand mb-3">
                <i class="bi bi-people-fill me-2"></i>Family Members
              </h6>

              <div class="table-responsive">
                <table class="table table-sm table-borderless align-middle">
                  <thead class="small text-muted">
                    <tr>
                      <th>Name</th>
                      <th>Relation</th>
                      <th>YOB</th>
                      <th>Age</th>
                      <th>Email</th>
                      <th>Telephone</th>
                    </tr>
                  </thead>
                  <tbody>

                    <?php foreach ($family as $f):
                      $age = $f['year_of_birth'] ? (date('Y') - (int) $f['year_of_birth']) : null;
                      ?>
                      <tr>
                        <td><?= esc($f['name']) ?></td>
                        <td><?= esc($f['relation']) ?></td>
                        <td><?= esc($f['year_of_birth'] ?? '-') ?></td>
                        <td><?= $age ?: '-' ?></td>
                        <td><?= esc($f['email'] ?: '-') ?></td>
                        <td><?= esc($f['telephone'] ?: '-') ?></td>
                      </tr>
                    <?php endforeach; ?>

                  </tbody>
                </table>
              </div>

              <a href="<?= route_to('account.family') ?>" class="btn btn-sm btn-outline-brand rounded-pill mt-2">
                <i class="bi bi-pencil-square me-1"></i> Edit Family Members
              </a>
            </div>
          <?php endif; ?>

          <!-- FORM -->
          <form method="post" action="<?= route_to('account.profile.update') ?>" class="mt-3">
            <?= csrf_field() ?>

            <!-- Readonly Details -->
            <div class="mb-3">
              <label class="form-label fw-semibold">First Name</label>
              <input type="text" class="form-control bg-light" value="<?= esc($m['first_name'] ?? '') ?>" readonly
                title="Cannot be edited">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Last Name</label>
              <input type="text" class="form-control bg-light" value="<?= esc($m['last_name'] ?? '') ?>" readonly
                title="Cannot be edited">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Email Address</label>
              <input type="email" class="form-control bg-light" value="<?= esc($m['email'] ?? '') ?>" readonly
                title="Cannot be edited">
            </div>

            <p class="small text-muted fst-italic mb-4">
              To change your name or email address, please contact the LCNL Membership Team.
            </p>

            <!-- Editable Details -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Mobile</label>
              <input type="text" name="mobile" value="<?= old('mobile', $m['mobile'] ?? '') ?>" class="form-control">
              <div class="form-text">Format: +447123456789 or 07123456789</div>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Date of Birth <span class="text-muted">(optional)</span></label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-calendar-date text-brand"></i></span>
                  <input type="date" name="date_of_birth" class="form-control"
                    value="<?= esc(old('date_of_birth', $m['date_of_birth'] ?? '')) ?>">
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Gender <span class="text-muted">(optional)</span></label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-gender-ambiguous text-brand"></i></span>
                  <select name="gender" class="form-select">
                    <?php $g = old('gender', $m['gender'] ?? ''); ?>
                    <option value="">— Select —</option>
                    <option value="male" <?= $g === 'male' ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= $g === 'female' ? 'selected' : '' ?>>Female</option>
                    <option value="other" <?= $g === 'other' ? 'selected' : '' ?>>Other</option>
                    <option value="prefer_not_to_say" <?= $g === 'prefer_not_to_say' ? 'selected' : '' ?>>Prefer not to say
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Address Line 1</label>
              <input type="text" name="address1" value="<?= old('address1', $m['address1'] ?? '') ?>"
                class="form-control">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Address Line 2</label>
              <input type="text" name="address2" value="<?= old('address2', $m['address2'] ?? '') ?>"
                class="form-control">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">City</label>
              <input type="text" name="city" value="<?= old('city', $m['city'] ?? '') ?>" class="form-control">
            </div>

            <div class="mb-4">
              <label class="form-label fw-semibold">Postcode</label>
              <input type="text" name="postcode" value="<?= old('postcode', $m['postcode'] ?? '') ?>"
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

