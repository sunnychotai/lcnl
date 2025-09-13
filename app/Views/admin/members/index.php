<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2"><i class="bi bi-person-badge-fill me-2"></i>Membership Admin</h1>
    <p class="lead fs-6 mb-0">Review and manage member registrations</p>
  </div>
</section>

<div class="container py-4">
  <?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success"><?= esc(session('message')) ?></div>
  <?php endif; ?>

  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <ul class="nav nav-pills">
      <?php
        $tabs = [
          'pending'  => 'Pending'.(isset($counts['pending']) ? " ({$counts['pending']})" : ''),
          'active'   => 'Active'.(isset($counts['active']) ? " ({$counts['active']})" : ''),
          'disabled' => 'Disabled'.(isset($counts['disabled']) ? " ({$counts['disabled']})" : ''),
          'all'      => 'All'
        ];
        foreach ($tabs as $key => $label):
      ?>
        <li class="nav-item">
          <a class="nav-link <?= ($status === $key ? 'active' : '') ?>"
             href="<?= base_url('admin/members?status='.$key) ?>"><?= $label ?></a>
        </li>
      <?php endforeach; ?>
    </ul>

    <form method="get" class="d-flex" action="<?= base_url('admin/members') ?>">
      <input type="hidden" name="status" value="<?= esc($status) ?>">
      <input type="text" name="q" value="<?= esc($q ?? '') ?>" class="form-control me-2" placeholder="Search name/email/mobile">
      <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
    </form>
  </div>

  <div class="card shadow-sm border-0">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Status</th>
            <th>Joined</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($members)): foreach ($members as $m): ?>
            <tr>
              <td><?= esc($m['first_name'].' '.$m['last_name']) ?></td>
              <td><?= esc($m['email']) ?></td>
              <td><?= esc($m['mobile'] ?? '-') ?></td>
              <td><span class="badge bg-<?= $m['status']==='active'?'success':($m['status']==='pending'?'warning text-dark':'secondary') ?>">
                <?= ucfirst($m['status']) ?></span>
              </td>
              <td><?= $m['created_at'] ? date('d M Y', strtotime($m['created_at'])) : '-' ?></td>
              <td class="text-end">
                <?php if ($m['status'] !== 'active'): ?>
                  <form method="post" action="<?= route_to('admin.members.activate', $m['id']) ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-success"><i class="bi bi-check2-circle me-1"></i>Activate</button>
                  </form>
                <?php endif; ?>

                <?php if ($m['status'] !== 'disabled'): ?>
                  <form method="post" action="<?= route_to('admin.members.disable', $m['id']) ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-slash-circle me-1"></i>Disable</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="6" class="text-center text-muted py-4">No members found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
