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

  <?php if ($msg = session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4">
      <i class="bi bi-check-circle-fill me-2"></i><?= esc($msg) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if ($msg = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4">
      <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc($msg) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

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
  $submitted    = count(array_filter($registrations, fn($r) => $r['status'] === 'submitted'));
  $confirmed    = count(array_filter($registrations, fn($r) => $r['status'] === 'confirmed'));
  $cancelled    = count(array_filter($registrations, fn($r) => $r['status'] === 'cancelled'));
  $totalPlayers = 0;
  foreach ($registrations as $r) {
      if (!empty($r['p1_first_name'])) $totalPlayers++;
      if (!empty($r['p2_first_name'])) $totalPlayers++;
      if (!empty($r['p3_first_name'])) $totalPlayers++;
      if (!empty($r['p4_first_name'])) $totalPlayers++;
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
        <div class="fs-2 fw-bold text-success"><?= $confirmed ?></div>
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
            <th>Team Name</th>
            <th>Registered</th>
            <th>Player 1</th>
            <th>P1 Email</th>
            <th>P1 Handicap</th>
            <th>P1 Meal</th>
            <th>P1 T-Shirt</th>
            <th>Player 2</th>
            <th>Player 3</th>
            <th>Player 4</th>
            <th>Status / Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($registrations as $r): ?>
          <?php
            // Build a summary of player names for the modal
            $playerNames = [];
            foreach (['p1','p2','p3','p4'] as $px) {
                if (!empty($r[$px . '_first_name'])) {
                    $playerNames[] = esc($r[$px . '_first_name'] . ' ' . $r[$px . '_last_name']);
                }
            }
          ?>
          <tr>
            <td class="font-monospace fw-bold text-brand"><?= esc($r['registration_ref']) ?></td>
            <td><?= !empty($r['team_name']) ? esc($r['team_name']) : '<span class="text-muted">—</span>' ?></td>
            <td><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
            <td><?= esc($r['p1_first_name'] . ' ' . $r['p1_last_name']) ?></td>
            <td><?= esc($r['p1_email']) ?></td>
            <td><?= esc($r['p1_handicap']) ?></td>
            <td><?= $r['p1_meal'] === 'vegetarian' ? 'Veg' : 'Non-Veg' ?></td>
            <td><?= esc($r['p1_tshirt'] ?? '—') ?></td>
            <td>
              <?php if (!empty($r['p2_first_name'])): ?>
                <?= esc($r['p2_first_name'] . ' ' . $r['p2_last_name']) ?>
                <div class="text-muted" style="font-size:11px;">
                  H: <?= esc($r['p2_handicap']) ?> &bull;
                  <?= $r['p2_meal'] === 'vegetarian' ? 'Veg' : 'Non-Veg' ?> &bull;
                  <?= esc($r['p2_tshirt'] ?? '—') ?>
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
                  <?= $r['p3_meal'] === 'vegetarian' ? 'Veg' : 'Non-Veg' ?> &bull;
                  <?= esc($r['p3_tshirt'] ?? '—') ?>
                </div>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if (!empty($r['p4_first_name'])): ?>
                <?= esc($r['p4_first_name'] . ' ' . $r['p4_last_name']) ?>
                <div class="text-muted" style="font-size:11px;">
                  H: <?= esc($r['p4_handicap']) ?> &bull;
                  <?= $r['p4_meal'] === 'vegetarian' ? 'Veg' : 'Non-Veg' ?> &bull;
                  <?= esc($r['p4_tshirt'] ?? '—') ?>
                </div>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($r['status'] === 'confirmed'): ?>
                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Confirmed</span>
              <?php elseif ($r['status'] === 'cancelled'): ?>
                <span class="badge bg-danger">Cancelled</span>
              <?php else: ?>
                <div class="d-flex align-items-center gap-2">
                  <span class="badge bg-warning text-dark">Awaiting Payment</span>
                  <button type="button"
                    class="btn btn-sm btn-outline-success rounded-pill"
                    data-bs-toggle="modal"
                    data-bs-target="#confirmModal"
                    data-id="<?= $r['id'] ?>"
                    data-ref="<?= esc($r['registration_ref']) ?>"
                    data-players="<?= esc(implode(', ', $playerNames)) ?>">
                    <i class="bi bi-check-circle me-1"></i>Confirm Paid
                  </button>
                </div>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- ── Confirm Payment Modal ──────────────────────────── -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">

      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="confirmModalLabel">
          <i class="bi bi-check-circle-fill me-2"></i>Confirm Payment Received
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body px-4 py-3">
        <p class="mb-3">
          Please confirm that payment has been received for the following registration.
          This will update the status to <strong>Confirmed</strong> and send a confirmation
          email to each player.
        </p>

        <table class="table table-sm table-bordered mb-0">
          <tr>
            <th class="text-muted fw-semibold w-35">Reference</th>
            <td class="font-monospace fw-bold text-brand" id="modalRef">—</td>
          </tr>
          <tr>
            <th class="text-muted fw-semibold">Players</th>
            <td id="modalPlayers">—</td>
          </tr>
        </table>

        <div class="alert alert-warning small mt-3 mb-0">
          <i class="bi bi-envelope me-1"></i>
          A confirmation email will be queued for each player. This action cannot be undone.
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">
          Cancel
        </button>
        <form id="confirmForm" method="post" action="" class="d-inline">
          <?= csrf_field() ?>
          <button type="submit" class="btn btn-success rounded-pill px-4">
            <i class="bi bi-check-circle me-2"></i>Yes, Confirm Payment
          </button>
        </form>
      </div>

    </div>
  </div>
</div>

<script>
  // Populate modal with the clicked row's data
  document.getElementById('confirmModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('modalRef').textContent     = btn.dataset.ref;
    document.getElementById('modalPlayers').textContent = btn.dataset.players;
    document.getElementById('confirmForm').action =
      '<?= site_url('admin/content/golf/confirm/') ?>' + btn.dataset.id;
  });

  $(function () {
    $('#golfTable').DataTable({
      order: [[1, 'desc']],
      pageLength: 25,
      responsive: true,
    });
  });
</script>

<?= $this->endSection() ?>
