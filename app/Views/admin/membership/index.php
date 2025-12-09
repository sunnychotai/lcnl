<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" />

<div class="container-fluid py-3">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">
      <i class="bi bi-people-fill me-2 text-brand"></i> Members
    </h1>
    <a href="<?= base_url('admin/membership/export') ?>" class="btn btn-sm btn-outline-brand">
      <i class="bi bi-download me-1"></i> Export CSV
    </a>
  </div>

  <ul class="nav nav-tabs mb-3">
    <li class="nav-item">
      <a class="nav-link <?= ($activeTab ?? '') === 'members' ? 'active' : '' ?>"
        href="<?= base_url('admin/membership') ?>">
        Members
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link <?= ($activeTab ?? '') === 'reports' ? 'active' : '' ?>"
        href="<?= base_url('admin/membership/reports') ?>">
        Reports
      </a>
    </li>
  </ul>


  <!-- Status Summary -->
  <div class="row g-3 mb-4">
    <?php foreach (['all', 'pending', 'active', 'disabled'] as $s): ?>
      <div class="col-md-3">
        <a href="#" class="text-decoration-none" onclick="setStatus('<?= $s ?>')">
          <div class="card no-hover border-0 text-center h-100 <?= $status === $s ? 'bg-light' : '' ?>">
            <div class="card-body">
              <i class="bi bi-people fs-2 text-brand mb-2"></i>
              <h6 class="fw-bold mb-0 text-brand">
                <?= ucfirst($s) ?> (<?= $counts[$s] ?? 0 ?>)
              </h6>
            </div>
          </div>
        </a>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Filters -->
  <div class="lcnl-card shadow-soft border-0 mb-4">
    <div class="card-body">
      <div class="row g-2 align-items-end">

        <div class="col-md-2">
          <label class="form-label small">Status</label>
          <select id="filterStatus" class="form-select form-select-sm">
            <?php foreach (['all', 'pending', 'active', 'disabled'] as $s): ?>
              <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>>
                <?= ucfirst($s) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label small">Search</label>
          <input type="text" id="filterSearch" class="form-control form-control-sm"
            placeholder="Name, email, mobile, city…">
        </div>

        <div class="col-auto">
          <button id="btnSearch" class="btn btn-sm btn-brand">
            <i class="bi bi-search"></i>
          </button>
        </div>

      </div>
    </div>
  </div>

  <!-- Members Table -->
  <div class="lcnl-card shadow-soft border-0">
    <div class="table-responsive">
      <table id="membersTable" class="table table-striped table-hover align-middle w-100">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>City</th>
            <th>Status</th>
            <th>Created</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>

<!-- DataTables Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
  let table;

  // set status buttons
  function setStatus(s) {
    $('#filterStatus').val(s);
    table.ajax.reload();
  }

  $(document).ready(function () {

    table = $('#membersTable').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      pageLength: 25,

      ajax: {
        url: "<?= base_url('admin/membership/data') ?>",
        type: "POST",
        data: function (d) {
          d.status = $('#filterStatus').val();
          d.searchTerm = $('#filterSearch').val();
          d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
        }
      },

      columns: [{
        data: "id"
      },
      {
        data: "name"
      },
      {
        data: "email"
      },
      {
        data: "mobile"
      },
      {
        data: "city"
      },
      {
        data: "status_badge"
      },
      {
        data: "created_at"
      },
      {
        data: "actions",
        orderable: false,
        searchable: false
      }
      ],

      order: [
        [0, 'desc']
      ]
    });

    $('#btnSearch').on('click', function () {
      table.ajax.reload();
    });

    $('#filterStatus').on('change', function () {
      table.ajax.reload();
    });

    $('#filterSearch').on('keyup', _.debounce(function () {
      table.ajax.reload();
    }, 250));

  });
</script>

<?= $this->endSection() ?>

