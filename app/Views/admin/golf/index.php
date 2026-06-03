<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- jQuery + DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<section class="hero-lcnl-watermark hero-overlay-ocean d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">Golf Event 2026</h1>
    <p class="lead fs-5 mb-0">Registration Administration</p>
  </div>
</section>

<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
      <h2 class="mb-0"><i class="bi bi-flag me-2"></i>Registrations</h2>
      <p class="text-muted mb-0 small">
        <?= count($registrations) ?> registration<?= count($registrations) !== 1 ? 's' : '' ?> received
      </p>
    </div>
    <a href="<?= site_url('admin/content/golf/export') ?>"
      class="btn btn-success rounded-pill px-4">
      <i class="bi bi-download me-2"></i>Export CSV
    </a>
  </div>

  <!-- Summary stats -->
  <?php
  $submitted  = count(array_filter($registrations, fn($r) => $r['status'] === 'submitted'));
  $confirmed  = count(array_filter($registrations, fn($r) => $r['status'] === 'confirmed'));
  $cancelled  = count(array_filter($registrations, fn($r) => $r['status'] === 'cancelled'));
  $totalPlayers = 0;
  foreach ($registrations as $r) {
      if (!empty($r['p1_first_name'])) $totalPlayers++;
      if (!empty($r['p2_first_name'])) $totalPlayers++;
      if (!empty($r['p3_first_name'])) $totalPlayers++;
  }
  ?>

  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-2 fw-bold text-brand"><?= count($registrations) ?></div>
        <div class="small text-muted">Total Registrations</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-2 fw-bold text-success"><?= $totalPlayers ?></div>
        <div class="small text-muted">Total Players</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-2 fw-bold text-warning"><?= $submitted ?></div>
        <div class="small text-muted">Awaiting Payment</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-2 fw-bold text-info"><?= $confirmed ?></div>
        <div class="small text-muted">Confirmed</div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm rounded-4">
    <div class="card-body p-0 table-responsive">
      <table id="golfTable" class="table table-striped align-middle mb-0 small">
        <thead class="table-dark">
          <tr>
            <th>Ref</th>
            <th>Registered</th>
            <th>Player 1</th>
            <th>P1 Email</th>
            <th>P1 Handicap</th>
            <th>P1 Meal</th>
            <th>Player 2</th>
            <th>Player 3</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($registrations as $r): ?>
          <tr>
            <td class="font-monospace fw-bold text-brand"><?= esc($r['registration_ref']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
            <td><?= esc($r['p1_first_name'] . ' ' . $r['p1_last_name']) ?></td>
            <td><?= esc($r['p1_email']) ?></td>
            <td><?= esc($r['p1_handicap']) ?></td>
            <td><?= $r['p1_meal'] === 'vegetarian' ? 'Veg' : 'Non-Veg' ?></td>
            <td>
              <?php if (!empty($r['p2_first_name'])): ?>
                <?= esc($r['p2_first_name'] . ' ' . $r['p2_last_name']) ?>
                <div class="text-muted" style="font-size:11px;">
                  H: <?= esc($r['p2_handicap']) ?> &bull;
                  <?= $r['p2_meal'] === 'vegetarian' ? 'Veg' : 'Non-Veg' ?>
                </div>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if (!empty($r['p3_first_name'])): ?>
                <?= esc($r['p3_first_name'] . ' ' . $r['p3_last_name']) ?>
                <div class="text-muted" style="font-size:11px;">
                  H: <?= esc($r['p3_handicap']) ?> &bull;
                  <?= $r['p3_meal'] === 'vegetarian' ? 'Veg' : 'Non-Veg' ?>
                </div>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($r['status'] === 'confirmed'): ?>
                <span class="badge bg-success">Confirmed</span>
              <?php elseif ($r['status'] === 'cancelled'): ?>
                <span class="badge bg-danger">Cancelled</span>
              <?php else: ?>
                <span class="badge bg-warning text-dark">Submitted</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<script>
  $(function () {
    $('#golfTable').DataTable({
      order: [[1, 'desc']],
      pageLength: 25,
      responsive: true,
    });
  });
</script>

<?= $this->endSection() ?>
