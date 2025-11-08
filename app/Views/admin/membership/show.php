<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-4">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0 d-flex align-items-center text-brand">
      <i class="bi bi-person-vcard-fill me-2 fs-4"></i>
      Member Details
    </h1>
    <div class="d-flex align-items-center gap-2">
      <a href="<?= base_url('admin/membership/' . $m['id'] . '/edit') ?>" class="btn btn-sm btn-brand">
        <i class="bi bi-pencil me-1"></i> Edit
      </a>
      <a href="<?= base_url('admin/membership') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
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
        <div class="card-body bg-white">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <span class="text-muted small">Name</span>
                <div class="fw-semibold"><?= esc(($m['first_name'] ?? '') . ' ' . ($m['last_name'] ?? '')) ?></div>
              </div>
              <div class="mb-3">
                <span class="text-muted small">Member ID</span>
                <div><span class="badge-lcnl-id">LCNL<?= (int)$m['id'] ?></span></div>
              </div>

              <div class="mb-3">
                <span class="text-muted small">Email</span>
                <div><i class="bi bi-envelope text-brand me-1"></i><?= esc($m['email']) ?></div>
              </div>
              <div class="mb-3">
                <span class="text-muted small">Mobile</span>
                <div><i class="bi bi-telephone text-brand me-1"></i><?= esc($m['mobile'] ?? '-') ?></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <span class="text-muted small">Status</span>
                <div>
                  <span class="badge bg-<?=
                                        $m['status'] === 'active' ? 'success' : ($m['status'] === 'pending' ? 'warning text-dark' : 'secondary')
                                        ?> px-3 py-2"><?= ucfirst($m['status']) ?></span>
                </div>
              </div>
              <div class="mb-3">
                <span class="text-muted small">Last Login</span>
                <div><?= esc($m['last_login'] ?? '-') ?></div>
              </div>
              <div class="mb-3">
                <span class="text-muted small">Verified At</span>
                <div><?= esc($m['verified_at'] ?? '-') ?></div>
              </div>
            </div>
          </div>

          <hr>

          <h6 class="fw-semibold text-brand mb-3"><i class="bi bi-geo-alt me-2"></i>Address</h6>
          <div class="row">
            <div class="col-md-6">
              <div><span class="text-muted small">Address Line 1</span>
                <div><?= esc($m['address1'] ?? '-') ?></div>
              </div>
              <div><span class="text-muted small">Address Line 2</span>
                <div><?= esc($m['address2'] ?? '-') ?></div>
              </div>
            </div>
            <div class="col-md-6">
              <div><span class="text-muted small">City</span>
                <div><?= esc($m['city'] ?? '-') ?></div>
              </div>
              <div><span class="text-muted small">Postcode</span>
                <div><?= esc($m['postcode'] ?? '-') ?></div>
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
          <div>Created: <?= esc($m['created_at'] ?? '-') ?></div>
          <div>Updated: <?= esc($m['updated_at'] ?? '-') ?></div>
          <div>Consent At: <?= esc($m['consent_at'] ?? '-') ?></div>
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