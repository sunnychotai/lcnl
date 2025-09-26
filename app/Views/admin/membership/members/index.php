<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-3">

 <!-- Header & Actions -->
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0">
    <i class="bi bi-people-fill me-2 text-primary"></i> Members
  </h1>
  <div>
    <a href="<?= base_url('admin/membership/members/export') ?>" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-download me-1"></i> Export CSV
    </a>
  </div>
</div>


  <!-- Stats Overview -->
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body text-center">
          <h6 class="text-muted mb-1">Active</h6>
          <h3 class="fw-bold text-success"><?= (int)($counts['active'] ?? 0) ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body text-center">
          <h6 class="text-muted mb-1">Pending</h6>
          <h3 class="fw-bold text-warning"><?= (int)($counts['pending'] ?? 0) ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body text-center">
          <h6 class="text-muted mb-1">Disabled</h6>
          <h3 class="fw-bold text-secondary"><?= (int)($counts['disabled'] ?? 0) ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body text-center">
          <h6 class="text-muted mb-1">Total</h6>
          <h3 class="fw-bold"><?= array_sum($counts ?? []) ?></h3>
        </div>
      </div>
    </div>
  </div>

  <!-- Flash Messages -->
  <?php if ($msg = session()->getFlashdata('message')): ?>
    <div class="alert alert-success"><?= esc($msg) ?></div>
  <?php endif; ?>
  <?php if ($err = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($err) ?></div>
  <?php endif; ?>

  <!-- Filters -->
  <div class="lcnl-card shadow-sm border-0 mb-4">
    <div class="card-body">
      <form class="row g-2 align-items-end" method="get">
        <div class="col-auto">
          <label class="form-label small">Status</label>
          <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <?php foreach (['all','pending','active','disabled'] as $s): ?>
              <option value="<?= $s ?>" <?= $status===$s?'selected':'' ?>>
                <?= ucfirst($s) ?> <?= isset($counts[$s]) ? '('.(int)$counts[$s].')' : '' ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-auto">
          <label class="form-label small">Search</label>
          <input type="text" name="q" value="<?= esc($q ?? '') ?>" class="form-control form-control-sm"
                 placeholder="Name, email or mobile">
        </div>
        <div class="col-auto">
          <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i> Filter</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Members Table -->
  <div class="lcnl-card shadow-sm border-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email / Mobile</th>
            <th>Status</th>
            <th>Created</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td class="text-muted small"><?= (int)$r['id'] ?></td>
            <td>
              <a class="fw-semibold text-decoration-none" href="<?= base_url('admin/membership/members/'.$r['id']) ?>">
                <i class="bi bi-person-circle me-1 text-secondary"></i>
                <?= esc(($r['first_name']??'').' '.($r['last_name']??'')) ?>
              </a>
            </td>
            <td>
              <div><?= esc($r['email']) ?></div>
              <div class="text-muted small"><?= esc($r['mobile'] ?? '-') ?></div>
            </td>
            <td>
              <span class="badge bg-<?=
                $r['status']==='active'?'success':
                ($r['status']==='pending'?'warning text-dark':'secondary')
              ?>"><?= ucfirst($r['status']) ?></span>
            </td>
            <td class="text-muted small"><?= esc($r['created_at'] ?? '') ?></td>
            <td class="text-end">
              <div class="btn-group btn-group-sm" role="group">
                <a class="btn btn-outline-secondary" href="<?= base_url('admin/membership/members/'.$r['id']) ?>" title="View">
                  <i class="bi bi-eye"></i>
                </a>
                <?php if ($r['status']!=='active'): ?>
                  <form method="post" action="<?= base_url('admin/membership/members/'.$r['id'].'/activate') ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-success" title="Activate"><i class="bi bi-check2-circle"></i></button>
                  </form>
                <?php endif; ?>
                <?php if ($r['status']!=='disabled'): ?>
                  <form method="post" action="<?= base_url('admin/membership/members/'.$r['id'].'/disable') ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline-danger" title="Disable"><i class="bi bi-slash-circle"></i></button>
                  </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
          <tr><td colspan="6" class="text-center text-muted py-4">No members found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pagination -->
  <div class="d-flex justify-content-center my-3">
    <?= $pager->links('default', 'default_full') ?>
  </div>

</div>

<?= $this->endSection() ?>
