<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Members</h1>
    <a href="<?= base_url('admin/members?status=pending') ?>" class="btn btn-sm btn-outline-warning">
      Pending <span class="badge bg-warning text-dark"><?= (int)($counts['pending'] ?? 0) ?></span>
    </a>
  </div>

  <?php if ($msg = session()->getFlashdata('message')): ?>
    <div class="alert alert-success"><?= esc($msg) ?></div>
  <?php endif; ?>
  <?php if ($err = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($err) ?></div>
  <?php endif; ?>

  <!-- Filters -->
  <form class="row g-2 align-items-end mb-3" method="get">
    <div class="col-auto">
      <label class="form-label small">Status</label>
      <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
        <?php foreach (['all','pending','active','disabled'] as $s): ?>
          <option value="<?= $s ?>" <?= $status===$s?'selected':'' ?>>
            <?= ucfirst($s) ?>
            <?php if (isset($counts[$s])): ?> (<?= (int)$counts[$s] ?>)<?php endif; ?>
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
      <button class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Filter</button>
    </div>
  </form>

  <!-- Table -->
  <div class="table-responsive">
    <table class="table table-hover align-middle">
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
            <a class="fw-semibold text-decoration-none" href="<?= base_url('admin/members/'.$r['id']) ?>">
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
            <?php if ($r['status']!=='active'): ?>
              <form class="d-inline" method="post" action="<?= base_url('admin/members/'.$r['id'].'/activate') ?>">
                <?= csrf_field() ?>
                <button class="btn btn-sm btn-success"><i class="bi bi-check2-circle me-1"></i> Activate</button>
              </form>
            <?php endif; ?>

            <?php if ($r['status']!=='disabled'): ?>
              <form class="d-inline" method="post" action="<?= base_url('admin/members/'.$r['id'].'/disable') ?>">
                <?= csrf_field() ?>
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-slash-circle me-1"></i> Disable</button>
              </form>
            <?php endif; ?>

            <a class="btn btn-sm btn-outline-secondary" href="<?= base_url('admin/members/'.$r['id']) ?>">
              <i class="bi bi-eye me-1"></i> View
            </a>
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

<?= $this->endSection() ?>
