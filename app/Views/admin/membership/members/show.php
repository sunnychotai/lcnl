<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Member #<?= (int)$m['id'] ?></h1>
    <div>
      <?php if (($m['status'] ?? '') !== 'active'): ?>
        <form class="d-inline" method="post" action="<?= base_url('admin/membership/members/'.$m['id'].'/activate') ?>">
          <?= csrf_field() ?>
          <button class="btn btn-success btn-sm"><i class="bi bi-check2-circle me-1"></i> Activate</button>
        </form>
      <?php endif; ?>
      <?php if (($m['status'] ?? '') !== 'disabled'): ?>
        <form class="d-inline" method="post" action="<?= base_url('admin/membership/members/'.$m['id'].'/disable') ?>">
          <?= csrf_field() ?>
          <button class="btn btn-outline-danger btn-sm"><i class="bi bi-slash-circle me-1"></i> Disable</button>
        </form>
      <?php endif; ?>
      <a class="btn btn-outline-secondary btn-sm" href="<?= base_url('admin/membership/members') ?>">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>
  </div>

  <?php if ($msg = session()->getFlashdata('message')): ?>
    <div class="alert alert-success"><?= esc($msg) ?></div>
  <?php endif; ?>
  <?php if ($err = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($err) ?></div>
  <?php endif; ?>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <h5 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2"></i> Profile</h5>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-2"><span class="text-muted small">Name</span><div><?= esc(($m['first_name']??'').' '.($m['last_name']??'')) ?></div></div>
              <div class="mb-2"><span class="text-muted small">Email</span><div><?= esc($m['email']) ?></div></div>
              <div class="mb-2"><span class="text-muted small">Mobile</span><div><?= esc($m['mobile'] ?? '-') ?></div></div>
            </div>
            <div class="col-md-6">
              <div class="mb-2"><span class="text-muted small">Status</span><div>
                <span class="badge bg-<?=
                  $m['status']==='active'?'success':
                  ($m['status']==='pending'?'warning text-dark':'secondary')
                ?>"><?= ucfirst($m['status']) ?></span>
              </div></div>
              <div class="mb-2"><span class="text-muted small">Postcode</span><div><?= esc($m['postcode'] ?? '-') ?></div></div>
              <div class="mb-2"><span class="text-muted small">Last Login</span><div><?= esc($m['last_login'] ?? '-') ?></div></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Meta -->
    <div class="col-lg-4">
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
          <h6 class="fw-bold mb-2"><i class="bi bi-clock-history me-1"></i> Timeline</h6>
          <div class="small text-muted">Created: <?= esc($m['created_at'] ?? '-') ?></div>
          <div class="small text-muted">Updated: <?= esc($m['updated_at'] ?? '-') ?></div>
          <div class="small text-muted">Verified: <?= esc($m['verified_at'] ?? '-') ?></div>
        </div>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-body">
          <h6 class="fw-bold mb-2"><i class="bi bi-gear me-1"></i> Actions</h6>
          <div class="d-grid gap-2">
            <form method="post" action="<?= base_url('admin/membership/members/'.$m['id'].'/activate') ?>">
              <?= csrf_field() ?>
              <button class="btn btn-success btn-sm w-100"><i class="bi bi-check2-circle me-1"></i> Activate</button>
            </form>
            <form method="post" action="<?= base_url('admin/membership/members/'.$m['id'].'/disable') ?>">
              <?= csrf_field() ?>
              <button class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-slash-circle me-1"></i> Disable</button>
            </form>
            <form method="post" action="<?= base_url('admin/membership/members/'.$m['id'].'/resend') ?>">
              <?= csrf_field() ?>
              <button class="btn btn-outline-secondary btn-sm w-100" disabled title="Email queue not enabled">Resend Email</button>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<?= $this->endSection() ?>
