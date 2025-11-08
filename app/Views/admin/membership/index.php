<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-3">

  <!-- Header & Actions -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">
      <i class="bi bi-people-fill me-2 text-brand"></i> Members
    </h1>
    <div class="d-flex gap-2">
      <a href="<?= base_url('admin/membership/export') ?>" class="btn btn-sm btn-outline-brand">
        <i class="bi bi-download me-1"></i> Export CSV
      </a>
    </div>


  </div>

  <!-- Stats Overview -->
  <div class="row g-3 mb-4">
    <!-- All -->
    <div class="col-md-3">
      <a href="<?= site_url('admin/membership?status=all') ?>" class="text-decoration-none">
        <div class="card no-hover border-0 text-center h-100 <?= $status === 'all' ? 'bg-gradient-accent text-white' : '' ?>">
          <div class="card-body">
            <i class="bi bi-people-fill fs-2 <?= $status === 'all' ? 'text-white' : 'text-brand' ?>"></i>
            <h6 class="fw-bold mb-0">All (<?= $counts['all'] ?>)</h6>
          </div>
        </div>
      </a>
    </div>
    <!-- Pending -->
    <div class="col-md-3">
      <a href="<?= site_url('admin/membership?status=pending') ?>" class="text-decoration-none">
        <div class="card no-hover border-0 text-center h-100 <?= $status === 'pending' ? 'bg-light' : '' ?>">
          <div class="card-body">
            <i class="bi bi-hourglass-split fs-2 text-warning mb-2"></i>
            <h6 class="fw-bold mb-0">Pending (<?= $counts['pending'] ?>)</h6>
          </div>
        </div>
      </a>
    </div>
    <!-- Active -->
    <div class="col-md-3">
      <a href="<?= site_url('admin/membership?status=active') ?>" class="text-decoration-none">
        <div class="card no-hover border-0 text-center h-100 <?= $status === 'active' ? 'bg-light' : '' ?>">
          <div class="card-body">
            <i class="bi bi-person-check-fill fs-2 text-success mb-2"></i>
            <h6 class="fw-bold mb-0">Active (<?= $counts['active'] ?>)</h6>
          </div>
        </div>
      </a>
    </div>
    <!-- Disabled -->
    <div class="col-md-3">
      <a href="<?= site_url('admin/membership?status=disabled') ?>" class="text-decoration-none">
        <div class="card no-hover border-0 text-center h-100 <?= $status === 'disabled' ? 'bg-light' : '' ?>">
          <div class="card-body">
            <i class="bi bi-person-dash-fill fs-2 text-danger mb-2"></i>
            <h6 class="fw-bold mb-0">Disabled (<?= $counts['disabled'] ?>)</h6>
          </div>
        </div>
      </a>
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
  <div class="lcnl-card shadow-soft border-0 mb-4">
    <div class="card-body">
      <form class="row g-2 align-items-end" method="get" id="memberFilterForm" onsubmit="return false;">
        <div class="col-auto">
          <label class="form-label small">Status</label>
          <select name="status" id="status" class="form-select form-select-sm">
            <?php foreach (['all', 'pending', 'active', 'disabled'] as $s): ?>
              <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>>
                <?= ucfirst($s) ?> <?= isset($counts[$s]) ? '(' . (int)$counts[$s] . ')' : '' ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-auto">
          <label class="form-label small">Search</label>
          <input type="text" name="q" id="q" value="<?= esc($q ?? '') ?>" class="form-control form-control-sm"
            placeholder="Name, email, mobile, or city">
        </div>
        <div class="col-auto">
          <button id="btnFilter" class="btn btn-sm btn-brand">
            <i class="bi bi-search me-1"></i> Filter
          </button>
        </div>
        <div class="col-auto ms-auto d-none" id="loadingSpinner">
          <span class="small text-muted"><i class="bi bi-arrow-repeat me-1"></i> Loading…</span>
        </div>
      </form>
    </div>
  </div>

  <!-- Members Table -->
  <div class="lcnl-card shadow-soft border-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email / Mobile</th>
            <th>City</th>
            <th>Status</th>
            <th>Created</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody id="memberRows">
          <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td>
                  <span class="badge-lcnl-id">LCNL<?= (int)$r['id'] ?></span>
                </td>
                <td>
                  <a class="fw-semibold text-decoration-none" href="<?= base_url('admin/membership/' . $r['id']) ?>">
                    <i class="bi bi-person-circle me-1 text-secondary"></i>
                    <?= esc(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?>
                  </a>
                </td>
                <td>
                  <div><?= esc($r['email']) ?></div>
                  <div class="text-muted small"><?= esc($r['mobile'] ?? '-') ?></div>
                </td>
                <td class="text-muted small"><?= esc($r['city'] ?? '-') ?></td>
                <td>
                  <span class="badge bg-<?=
                                        $r['status'] === 'active' ? 'success' : ($r['status'] === 'pending' ? 'warning text-dark' : 'secondary')
                                        ?>"><?= ucfirst($r['status']) ?></span>
                </td>
                <td class="text-muted small"><?= esc($r['created_at'] ?? '') ?></td>



                <td class="text-end">
                  <div class="lcnl-action-group d-inline-flex">

                    <!-- View -->
                    <a href="<?= base_url('admin/membership/' . $r['id']) ?>" class="btn-action" title="View">
                      <i class="bi bi-eye"></i>
                    </a>

                    <!-- Edit -->
                    <a href="<?= base_url('admin/membership/' . $r['id'] . '/edit') ?>" class="btn-action" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </a>

                    <!-- Status Action -->
                    <?php if ($r['status'] === 'active'): ?>
                      <!-- show disable only -->
                      <form method="post" action="<?= base_url('admin/membership/' . $r['id'] . '/disable') ?>" class="d-inline">
                        <?= csrf_field() ?>
                        <button class="btn-action btn-action-danger" title="Disable">
                          <i class="bi bi-slash-circle"></i>
                        </button>
                      </form>
                    <?php else: ?>
                      <!-- show activate only -->
                      <form method="post" action="<?= base_url('admin/membership/' . $r['id'] . '/activate') ?>" class="d-inline">
                        <?= csrf_field() ?>
                        <button class="btn-action btn-action-filled" title="Activate">
                          <i class="bi bi-check2"></i>
                        </button>
                      </form>
                    <?php endif; ?>

                  </div>
                </td>




              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center text-muted py-4">No members found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pagination -->
  <div class="d-flex justify-content-center my-3" id="pagerWrap">
    <?= $pager->links('default', 'default_full') ?>
  </div>

</div>

<!-- AJAX live search -->
<script>
  (function() {
    const $q = document.getElementById('q');
    const $status = document.getElementById('status');
    const $btn = document.getElementById('btnFilter');
    const $rows = document.getElementById('memberRows');
    const $pager = document.getElementById('pagerWrap');
    const $spinner = document.getElementById('loadingSpinner');
    let timer;

    function fetchRows(pageUrl) {
      const params = new URLSearchParams({
        q: $q.value.trim(),
        status: $status.value,
        ajax: 1
      });
      const url = pageUrl || ('<?= base_url('admin/membership') ?>' + '?' + params.toString());

      $spinner.classList.remove('d-none');
      fetch(url, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(r => r.text())
        .then(html => {
          // Expect JSON: { rowsHtml: '...', pagerHtml: '...' }
          const data = JSON.parse(html);
          $rows.innerHTML = data.rowsHtml || '';
          $pager.innerHTML = data.pagerHtml || '';
          // Wire up pager links for AJAX
          $pager.querySelectorAll('a.page-link').forEach(a => {
            a.addEventListener('click', e => {
              e.preventDefault();
              fetchRows(a.getAttribute('href'));
            });
          });
        })
        .catch(() => {
          $rows.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Problem loading results.</td></tr>';
        })
        .finally(() => $spinner.classList.add('d-none'));
    }

    function debounceSearch() {
      clearTimeout(timer);
      timer = setTimeout(fetchRows, 250);
    }

    $q.addEventListener('input', debounceSearch);
    $status.addEventListener('change', fetchRows);
    $btn.addEventListener('click', fetchRows);

    // Make existing pager links AJAX after first paint
    document.querySelectorAll('#pagerWrap a.page-link').forEach(a => {
      a.addEventListener('click', e => {
        e.preventDefault();
        fetchRows(a.getAttribute('href'));
      });
    });
  })();
</script>

<?= $this->endSection() ?>