<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">
      <i class="bi bi-house-heart-fill me-2"></i> My Household
    </h1>
    <p class="lead fs-6 mb-0">Create a household, add dependents, and link family members</p>
  </div>
</section>

<div class="container py-4">

  <?php if ($msg = session()->getFlashdata('message')): ?>
    <div class="alert alert-success"><?= esc($msg) ?></div>
  <?php endif; ?>
  <?php if ($err = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($err) ?></div>
  <?php endif; ?>
  <?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
      <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <?php if (empty($house)): ?>
    <!-- No household yet: create -->
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="form-card">
          <h3 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2"></i>Create Your Household</h3>
          <form method="post" action="<?= route_to('account.household.create') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
              <div class="col-sm-8">
                <label class="form-label">Household Name (optional)</label>
                <input type="text" name="household_name" class="form-control" placeholder="e.g., Chotai Household">
              </div>
              <div class="col-sm-4">
                <label class="form-label">Postcode (optional)</label>
                <input type="text" name="postcode" class="form-control" maxlength="12">
              </div>
              <div class="col-md-6">
                <label class="form-label">Address line 1 (optional)</label>
                <input type="text" name="address1" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label">Address line 2 (optional)</label>
                <input type="text" name="address2" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label">City/Town (optional)</label>
                <input type="text" name="city" class="form-control">
              </div>
              <div class="col-12">
                <button class="btn btn-brand btn-lg rounded-pill px-4">
                  <i class="bi bi-house-add-fill me-2"></i> Create Household
                </button>
              </div>
            </div>
          </form>
          <p class="text-muted small mt-2">You’ll be set as the <strong>Lead</strong> for this household.</p>
        </div>
      </div>
    </div>

  <?php else: ?>
    <!-- Existing household: summary + actions -->
    <div class="row g-4">

      <div class="col-lg-7">
        <div class="card shadow-sm border-0 rounded-4 h-100">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div>
                <h3 class="fw-bold mb-1">
                  <?= esc($house['household_name'] ?: 'Your Household') ?>
                </h3>
                <div class="text-muted">
                  Invite Code:
                  <span class="badge bg-warning text-dark"><?= esc($house['invite_code']) ?></span>
                  <span class="ms-2 small">Share this code with family to link their account.</span>
                </div>
              </div>
              <i class="bi bi-people-fill text-brand fs-2"></i>
            </div>

            <?php if ($house['address1'] || $house['postcode']): ?>
              <div class="mb-3">
                <i class="bi bi-geo-alt-fill me-1 text-brand"></i>
                <span class="text-muted">
                  <?= esc(trim(($house['address1'] ?? '') . ' ' . ($house['address2'] ?? ''))) ?><br>
                  <?= esc(($house['city'] ?? '')) ?> <?= esc(($house['postcode'] ?? '')) ?>
                </span>
              </div>
            <?php endif; ?>

            <h5 class="fw-bold mt-3">Household Members</h5>
            <div class="table-responsive">
              <table class="table align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach (($house['members'] ?? []) as $fm): ?>
                    <tr>
                      <td><?= esc(($fm['first_name'] ?? '').' '.($fm['last_name'] ?? '')) ?></td>
                      <td><span class="badge bg-<?=
                            $fm['role']==='lead'?'brand text-white':
                            ($fm['role']==='spouse'?'primary':'secondary')
                          ?>"><?= ucfirst($fm['role']) ?></span></td>
                      <td><?= esc($fm['email'] ?? '-') ?></td>
                      <td><?= esc($fm['mobile'] ?? '-') ?></td>
                      <td><span class="badge bg-<?=
                            $fm['status']==='active'?'success':
                            ($fm['status']==='pending'?'warning text-dark':'secondary')
                          ?>"><?= ucfirst($fm['status']) ?></span></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <!-- Add Dependent -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-person-plus-fill me-2 text-brand"></i>Add Dependent</h5>
            <form method="post" action="<?= route_to('account.household.addDependent') ?>">
              <?= csrf_field() ?>
              <div class="row g-3">
                <div class="col-sm-6">
                  <label class="form-label">First Name*</label>
                  <input type="text" name="first_name" class="form-control" required>
                </div>
                <div class="col-sm-6">
                  <label class="form-label">Surname</label>
                  <input type="text" name="last_name" class="form-control">
                </div>
                <div class="col-12">
                  <label class="form-label">Email (optional)</label>
                  <input type="email" name="email" class="form-control" inputmode="email" autocomplete="email">
                </div>
                <div class="col-12">
                  <label class="form-label">Mobile (optional)</label>
                  <input type="tel" name="mobile" class="form-control" inputmode="tel" placeholder="+447..." pattern="^\+?\d{7,15}$">
                </div>
                <div class="col-12">
                  <label class="form-label">Postcode (optional)</label>
                  <input type="text" name="postcode" class="form-control" maxlength="12" autocomplete="postal-code">
                </div>
                <div class="col-12">
                  <button class="btn btn-brand rounded-pill px-4"><i class="bi bi-plus-lg me-1"></i> Add</button>
                </div>
              </div>
            </form>
            <p class="text-muted small mt-2">Dependents are added under your household and can be given access later if needed.</p>
          </div>
        </div>

        <!-- Link Existing by Email -->
        <div class="card shadow-sm border-0 rounded-4">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-link-45deg me-2 text-brand"></i>Link Existing Member</h5>
            <form method="post" action="<?= route_to('account.household.linkExisting') ?>">
              <?= csrf_field() ?>
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label">Member Email</label>
                  <input type="email" name="email" class="form-control" inputmode="email" required>
                </div>
                <div class="col-12">
                  <label class="form-label">Role</label>
                  <select name="role" class="form-select">
                    <option value="spouse">Spouse</option>
                    <option value="dependent">Dependent</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div class="col-12">
                  <button class="btn btn-accent rounded-pill px-4"><i class="bi bi-person-check-fill me-1"></i> Link</button>
                </div>
              </div>
            </form>
            <p class="text-muted small mt-2">The person must already have an LCNL account (email registered).</p>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

</div>

<?= $this->endSection() ?>
