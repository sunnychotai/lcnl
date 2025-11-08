<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-4">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h1 class="h4 mb-0 d-flex align-items-center text-brand">
      <i class="bi bi-person-vcard-fill me-2 fs-4"></i>
      Member Details
    </h1>
    <div class="d-flex align-items-center gap-2">
      <a href="<?= base_url('admin/membership/' . $m['id'] . '/edit') ?>" class="btn btn-brand px-3 py-2 d-flex align-items-center">
        <i class="bi bi-pencil me-1"></i> Edit
      </a>
      <a href="<?= base_url('admin/membership') ?>" class="btn btn-outline-brand px-3 py-2 d-flex align-items-center">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>
    </div>
  </div>

  <?php if ($msg = session()->getFlashdata('message')): ?>
    <div class="alert alert-success shadow-sm border-0"><?= esc($msg) ?></div>
  <?php endif; ?>
  <?php if ($err = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger shadow-sm border-0"><?= esc($err) ?></div>
  <?php endif; ?>

  <div class="row g-4">
    <!-- Profile Info -->
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-brand text-white py-2 px-3">
          <h5 class="mb-0 fw-semibold"><i class="bi bi-person-lines-fill me-2"></i>Profile Overview</h5>
        </div>
        <div class="card-body bg-white p-4">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <span class="detail-label">Name</span>
                <span class="detail-value"><?= esc(($m['first_name'] ?? '') . ' ' . ($m['last_name'] ?? '')) ?></span>
              </div>

              <div class="mb-3">
                <span class="detail-label">Member ID</span>
                <span class="detail-value"><span class="badge-lcnl-id">LCNL<?= (int)$m['id'] ?></span></span>
              </div>

              <div class="mb-3">
                <span class="detail-label">Email</span>
                <span class="detail-value"><i class="bi bi-envelope text-brand me-1"></i><?= esc($m['email']) ?></span>
              </div>

              <div class="mb-3">
                <span class="detail-label">Mobile</span>
                <span class="detail-value"><i class="bi bi-telephone text-brand me-1"></i><?= esc($m['mobile'] ?? '-') ?></span>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <span class="detail-label">Status</span>
                <span class="detail-value">
                  <span class="badge bg-<?=
                                        $m['status'] === 'active' ? 'success' : ($m['status'] === 'pending' ? 'warning text-dark' : 'secondary')
                                        ?> px-3 py-2"><?= ucfirst($m['status']) ?></span>
                </span>
              </div>

              <div class="mb-3">
                <span class="detail-label">Last Login</span>
                <span class="detail-value"><?= esc($m['last_login'] ?? '-') ?></span>
              </div>

              <div class="mb-3">
                <span class="detail-label">Verified At</span>
                <span class="detail-value"><?= esc($m['verified_at'] ?? '-') ?></span>
              </div>
            </div>
          </div>

          <hr>

          <h6 class="fw-semibold text-brand mb-3"><i class="bi bi-geo-alt me-2"></i>Address</h6>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <span class="detail-label">Address Line 1</span>
                <span class="detail-value"><?= esc($m['address1'] ?? '-') ?></span>
              </div>
              <div class="mb-3">
                <span class="detail-label">Address Line 2</span>
                <span class="detail-value"><?= esc($m['address2'] ?? '-') ?></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <span class="detail-label">City</span>
                <span class="detail-value"><?= esc($m['city'] ?? '-') ?></span>
              </div>
              <div class="mb-3">
                <span class="detail-label">Postcode</span>
                <span class="detail-value"><?= esc($m['postcode'] ?? '-') ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Meta / Actions -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-light fw-semibold"><i class="bi bi-clock-history me-1"></i> Timeline</div>
        <div class="card-body small text-muted">
          <div><span class="detail-label">Created</span> <?= esc($m['created_at'] ?? '-') ?></div>
          <div><span class="detail-label">Updated</span> <?= esc($m['updated_at'] ?? '-') ?></div>
          <div><span class="detail-label">Consent At</span> <?= esc($m['consent_at'] ?? '-') ?></div>
        </div>
      </div>

      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-light fw-semibold"><i class="bi bi-gear me-1"></i> Quick Actions</div>
        <div class="card-body d-grid gap-2">
          <?php if (($m['status'] ?? '') === 'active'): ?>
            <form method="post" action="<?= base_url('admin/membership/' . $m['id'] . '/disable') ?>">
              <?= csrf_field() ?>
              <button class="btn btn-outline-danger w-100"><i class="bi bi-slash-circle me-1"></i> Disable Member</button>
            </form>
          <?php else: ?>
            <form method="post" action="<?= base_url('admin/membership/' . $m['id'] . '/activate') ?>">
              <?= csrf_field() ?>
              <button class="btn btn-brand w-100 text-white"><i class="bi bi-check2-circle me-1"></i> Activate Member</button>
            </form>
          <?php endif; ?>
          <form method="post" action="<?= base_url('admin/membership/' . $m['id'] . '/resend') ?>">
            <?= csrf_field() ?>
            <button class="btn btn-outline-brand w-100"><i class="bi bi-envelope me-1"></i> Resend Email</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>