<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" />

<style>
  /* Stats Cards */
  .stat-card-list {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    display: flex;
    gap: 1rem;
    align-items: center;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    min-height: 100px;
    /* Adjusted for cards without trend lines */
  }

  .stat-card-list::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, transparent, var(--brand), transparent);
    transform: scaleX(0);
    transition: transform 0.3s ease;
  }

  .stat-card-list:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    border-color: var(--brand);
  }

  .stat-card-list:hover::before {
    transform: scaleX(1);
  }

  .stat-card-list.active {
    border-color: var(--brand);
    background: linear-gradient(135deg, rgba(186, 27, 66, 0.05), rgba(152, 45, 82, 0.05));
  }

  .stat-card-list.active::before {
    transform: scaleX(1);
  }

  .stat-icon-list {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
    transition: all 0.3s ease;
  }

  .stat-card-list:hover .stat-icon-list {
    transform: scale(1.1) rotate(5deg);
  }

  .stat-icon-all {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
  }

  .stat-icon-pending {
    background: linear-gradient(135deg, #f59e0b, #d97706);
  }

  .stat-icon-active {
    background: linear-gradient(135deg, #10b981, #059669);
  }

  .stat-icon-disabled {
    background: linear-gradient(135deg, #6b7280, #4b5563);
  }

  .stat-content-list {
    flex: 1;
    min-width: 0;
  }

  .stat-value-list {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 0.25rem;
  }

  .stat-label-list {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  /* Header Enhancement */
  .page-header {
    background: linear-gradient(135deg, var(--brand) 0%, #982d52 100%);
    border-radius: 1rem;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 4px 12px rgba(186, 27, 66, 0.2);
    position: relative;
    overflow: hidden;
  }

  .page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: float 6s ease-in-out infinite;
  }

  @keyframes float {

    0%,
    100% {
      transform: translateY(0) rotate(0deg);
    }

    50% {
      transform: translateY(-20px) rotate(5deg);
    }
  }

  .page-title-group {
    position: relative;
    z-index: 1;
  }

  .page-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    margin-bottom: 1rem;
  }

  /* Advanced Filters */
  .filter-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    border: 1px solid #f0f0f0;
  }

  .filter-toggle {
    cursor: pointer;
    user-select: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--brand);
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
  }

  .filter-toggle:hover {
    background: rgba(186, 27, 66, 0.05);
  }

  .filter-toggle i {
    transition: transform 0.3s ease;
  }

  .filter-toggle.collapsed i {
    transform: rotate(-90deg);
  }

  /* Table Enhancements */
  #membersTable {
    font-size: 0.9rem;
  }

  #membersTable thead th {
    background: #f8f9fa;
    border-bottom: 2px solid var(--brand);
    color: #1f2937;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    padding: 1rem 0.75rem;
  }

  #membersTable tbody tr {
    transition: all 0.2s ease;
  }

  #membersTable tbody tr:hover {
    background: #fef3f5 !important;
    transform: scale(1.01);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }

  #membersTable td {
    vertical-align: middle;
    padding: 0.75rem;
    border-bottom: 1px solid #f3f4f6;
  }

  /* Badges */
  .badge-status {
    padding: 0.4rem 0.75rem;
    border-radius: 999px;
    font-weight: 600;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
  }

  .badge-status i {
    font-size: 0.5rem;
  }

  .badge-active {
    background: #d1fae5;
    color: #065f46;
  }

  .badge-pending {
    background: #fef3c7;
    color: #92400e;
  }

  .badge-disabled {
    background: #e5e7eb;
    color: #374151;
  }

  /* Email Display */
  .email-wrapper {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .email-primary {
    font-weight: 500;
    color: #1f2937;
    word-break: break-word;
  }

  .email-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .email-dot:hover {
    transform: scale(1.3);
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
  }

  .email-dot-valid {
    background: #10b981;
  }

  .email-dot-invalid {
    background: #ef4444;
  }

  /* Action Buttons */
  .btn-action {
    padding: 0.4rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
  }

  .btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
  }

  .btn-action-view {
    background: linear-gradient(135deg, var(--brand), #982d52);
    color: white;
  }

  .btn-action-edit {
    background: #f3f4f6;
    color: #374151;
  }

  .btn-action-edit:hover {
    background: #e5e7eb;
    color: #1f2937;
  }

  /* Bulk Actions */
  .bulk-actions-bar {
    background: linear-gradient(135deg, #1f2937, #111827);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 0.75rem;
    display: none;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  }

  .bulk-actions-bar.active {
    display: flex;
  }

  .bulk-count {
    font-weight: 700;
    color: #fbbf24;
  }

  /* Quick View Modal */
  .quick-view-modal .modal-content {
    border-radius: 1rem;
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  }

  .quick-view-header {
    background: linear-gradient(135deg, var(--brand), #982d52);
    color: white;
    border-radius: 1rem 1rem 0 0;
    padding: 1.5rem;
  }

  .quick-view-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }

  /* Loading States */
  .skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 0.25rem;
  }

  @keyframes shimmer {
    0% {
      background-position: 200% 0;
    }

    100% {
      background-position: -200% 0;
    }
  }

  /* Column Toggle */
  .column-toggle-dropdown {
    max-height: 300px;
    overflow-y: auto;
  }

  .column-toggle-item {
    padding: 0.5rem 1rem;
    cursor: pointer;
    transition: background 0.2s ease;
  }

  .column-toggle-item:hover {
    background: #f3f4f6;
  }

  .column-toggle-item input[type="checkbox"] {
    margin-right: 0.5rem;
  }

  /* DataTables Custom Styling */
  .dataTables_wrapper .dataTables_length,
  .dataTables_wrapper .dataTables_filter {
    margin-bottom: 1rem;
  }

  .dataTables_wrapper .dataTables_paginate {
    margin-top: 1rem;
  }

  .dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 0.5rem;
    margin: 0 0.125rem;
  }

  .dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, var(--brand), #982d52) !important;
    color: white !important;
    border: none !important;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .stat-card-list {
      margin-bottom: 0.75rem;
    }

    .stat-value-list {
      font-size: 1.5rem;
    }

    .page-header {
      padding: 1.5rem;
    }
  }

  /* Tab Enhancement */
  .nav-tabs {
    border-bottom: 2px solid #e5e7eb;
  }

  .nav-tabs .nav-link {
    border: none;
    color: #6b7280;
    font-weight: 600;
    padding: 1rem 1.5rem;
    transition: all 0.3s ease;
    position: relative;
  }

  .nav-tabs .nav-link::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--brand);
    transform: scaleX(0);
    transition: transform 0.3s ease;
  }

  .nav-tabs .nav-link:hover {
    color: var(--brand);
  }

  .nav-tabs .nav-link.active {
    color: var(--brand);
    background: none;
    border: none;
  }

  .nav-tabs .nav-link.active::after {
    transform: scaleX(1);
  }

  /* Utility Classes */
  .text-brand {
    color: var(--brand) !important;
  }

  .bg-brand {
    background: var(--brand) !important;
  }

  .border-brand {
    border-color: var(--brand) !important;
  }

  /* Tooltips */
  .tooltip-inner {
    background: #1f2937;
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
  }
</style>

<div class="container-fluid py-4">

  <!-- Enhanced Header -->
  <div class="page-header">
    <div class="page-title-group">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
          <div class="page-icon">
            <i class="bi bi-people-fill"></i>
          </div>
          <h1 class="h2 mb-2 fw-bold">Member Management</h1>
          <p class="mb-0 opacity-90">Manage and monitor your community members</p>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-light btn-pill" data-bs-toggle="modal" data-bs-target="#advancedFiltersModal">
            <i class="bi bi-funnel me-1"></i>Advanced Filters
          </button>
          <div class="btn-group">
            <button class="btn btn-light btn-pill dropdown-toggle" data-bs-toggle="dropdown">
              <i class="bi bi-download me-1"></i>Export
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="<?= base_url('admin/membership/export?format=csv') ?>">
                  <i class="bi bi-filetype-csv me-2"></i>Export as CSV
                </a></li>
              <li><a class="dropdown-item" href="<?= base_url('admin/membership/export?format=xlsx') ?>">
                  <i class="bi bi-filetype-xlsx me-2"></i>Export as Excel
                </a></li>
              <li><a class="dropdown-item" href="<?= base_url('admin/membership/export?format=pdf') ?>">
                  <i class="bi bi-filetype-pdf me-2"></i>Export as PDF
                </a></li>
            </ul>
          </div>
          <a href="<?= base_url('admin/membership/create') ?>" class="btn btn-outline-light btn-pill">
            <i class="bi bi-person-plus-fill me-1"></i>Add Member
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabs -->
  <ul class="nav nav-tabs mb-4">
    <li class="nav-item">
      <a class="nav-link <?= ($activeTab ?? '') === 'members' ? 'active' : '' ?>"
        href="<?= base_url('admin/membership') ?>">
        <i class="bi bi-people me-2"></i>Members
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= ($activeTab ?? '') === 'reports' ? 'active' : '' ?>"
        href="<?= base_url('admin/membership/reports') ?>">
        <i class="bi bi-graph-up me-2"></i>Reports
      </a>
    </li>
  </ul>

  <!-- Enhanced Status Summary -->
  <div class="row g-3 mb-4">
    <?php
    $statusConfig = [
      'all' => ['icon' => 'people-fill', 'class' => 'all'],
      'pending' => ['icon' => 'hourglass-split', 'class' => 'pending'],
      'active' => ['icon' => 'check-circle-fill', 'class' => 'active'],
      'disabled' => ['icon' => 'x-circle-fill', 'class' => 'disabled']
    ];
    ?>
    <?php foreach ($statusConfig as $s => $config): ?>
      <div class="col-lg-3 col-md-6">
        <div class="stat-card-list <?= $status === $s ? 'active' : '' ?>" onclick="setStatus('<?= $s ?>')">
          <div class="stat-icon-list stat-icon-<?= $config['class'] ?>">
            <i class="bi bi-<?= $config['icon'] ?>"></i>
          </div>
          <div class="stat-content-list">
            <div class="stat-value-list"><?= number_format($counts[$s] ?? 0) ?></div>
            <div class="stat-label-list"><?= ucfirst($s) ?></div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Bulk Actions Bar -->
  <div class="bulk-actions-bar" id="bulkActionsBar">
    <div class="flex-grow-1">
      <span class="bulk-count" id="bulkCount">0</span> members selected
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-sm btn-light" onclick="bulkActivate()">
        <i class="bi bi-check-circle me-1"></i>Activate
      </button>
      <button class="btn btn-sm btn-light" onclick="bulkDisable()">
        <i class="bi bi-x-circle me-1"></i>Disable
      </button>
      <button class="btn btn-sm btn-light" onclick="bulkExport()">
        <i class="bi bi-download me-1"></i>Export Selected
      </button>
      <button class="btn btn-sm btn-outline-light" onclick="clearSelection()">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
  </div>

  <!-- Filters Card -->
  <div class="filter-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h6 class="mb-0 fw-bold text-brand">
        <i class="bi bi-funnel-fill me-2"></i>Filters
      </h6>
      <div class="d-flex gap-2 align-items-center">
        <button class="btn btn-sm btn-outline-secondary" onclick="resetFilters()">
          <i class="bi bi-arrow-clockwise me-1"></i>Reset
        </button>
        <span class="filter-toggle" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
          <span>Advanced</span>
          <i class="bi bi-chevron-down"></i>
        </span>
      </div>
    </div>

    <div class="row g-3 align-items-end">
      <!-- Status Filter -->
      <div class="col-md-2">
        <label class="form-label small fw-semibold">Status</label>
        <select id="filterStatus" class="form-select">
          <?php foreach (['all', 'pending', 'active', 'disabled'] as $s): ?>
            <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>>
              <?= ucfirst($s) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Search Filter -->
      <div class="col-md-5">
        <label class="form-label small fw-semibold">Search</label>
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0">
            <i class="bi bi-search text-muted"></i>
          </span>
          <input type="text" id="filterSearch" class="form-control border-start-0 ps-0"
            placeholder="Name, email, mobile, city…">
        </div>
      </div>

      <!-- Column Visibility -->
      <div class="col-md-auto">
        <label class="form-label small fw-semibold d-block">&nbsp;</label>
        <div class="btn-group">
          <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-eye me-1"></i>Columns
          </button>
          <ul class="dropdown-menu column-toggle-dropdown" id="columnToggle">
            <!-- Populated by JS -->
          </ul>
        </div>
      </div>

      <!-- Search Button -->
      <div class="col-auto">
        <label class="form-label small fw-semibold d-block">&nbsp;</label>
        <button id="btnSearch" class="btn btn-brand">
          <i class="bi bi-search me-1"></i>Search
        </button>
      </div>
    </div>

    <!-- Advanced Filters (Collapsed by default) -->
    <div class="collapse mt-4" id="advancedFilters">
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label small fw-semibold">Email Verified</label>
          <select id="filterVerified" class="form-select">
            <option value="">All</option>
            <option value="yes">Verified</option>
            <option value="no">Not Verified</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label small fw-semibold">Membership Type</label>
          <select id="filterMembershipType" class="form-select">
            <option value="">All</option>
            <option value="standard">Standard</option>
            <option value="life">Life Member</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label small fw-semibold">Joined After</label>
          <input type="date" id="filterDateFrom" class="form-control">
        </div>

        <div class="col-md-3">
          <label class="form-label small fw-semibold">Joined Before</label>
          <input type="date" id="filterDateTo" class="form-control">
        </div>
      </div>
    </div>
  </div>

  <!-- Members Table -->
  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
      <div class="table-responsive">
        <table id="membersTable" class="table table-hover align-middle w-100">
          <thead>
            <tr>
              <th style="width: 30px;">
                <input type="checkbox" id="selectAll" class="form-check-input">
              </th>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th class="text-center" style="width:70px;">Valid</th>
              <th>Mobile</th>
              <th>City</th>
              <th>Status</th>
              <th>Created</th>
              <th class="text-end" style="width: 150px;">Actions</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- Quick View Modal -->
<div class="modal fade quick-view-modal" id="quickViewModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="quick-view-header">
        <div class="d-flex justify-content-between align-items-start">
          <div class="d-flex gap-3">
            <div class="quick-view-avatar" id="qvAvatar"></div>
            <div>
              <h5 class="mb-1 fw-bold" id="qvName">Loading...</h5>
              <div id="qvEmail" class="opacity-90"></div>
              <div id="qvBadge" class="mt-2"></div>
            </div>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
      </div>
      <div class="modal-body p-4">
        <div id="qvContent">
          <!-- Populated by JS -->
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Close</button>
        <a href="#" id="qvViewFull" class="btn btn-brand rounded-pill">
          <i class="bi bi-box-arrow-up-right me-1"></i>View Full Profile
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Advanced Filters Modal -->
<div class="modal fade" id="advancedFiltersModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content rounded-4 border-0">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-funnel-fill me-2 text-brand"></i>Advanced Filters
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Has Family Members</label>
            <select class="form-select">
              <option value="">Any</option>
              <option value="yes">Has Family</option>
              <option value="no">No Family</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Last Login</label>
            <select class="form-select">
              <option value="">Any Time</option>
              <option value="7">Last 7 Days</option>
              <option value="30">Last 30 Days</option>
              <option value="90">Last 90 Days</option>
              <option value="never">Never</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-brand rounded-pill" onclick="applyAdvancedFilters()">
          <i class="bi bi-check2-circle me-1"></i>Apply Filters
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Toast Notification -->
<div class="toast-container position-fixed top-0 end-0 p-3">
  <div id="actionToast" class="toast" role="alert">
    <div class="toast-body bg-success text-white rounded-3 d-flex align-items-center gap-2">
      <i class="bi bi-check-circle-fill fs-5"></i>
      <span class="toast-message">Action completed successfully!</span>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>

<script>
  let table;
  let selectedRows = new Set();

  // Global CSRF holder
  let CSRF = {
    name: '<?= csrf_token() ?>',
    hash: '<?= csrf_hash() ?>'
  };

  // Status change handler
  function setStatus(s) {
    $('#filterStatus').val(s);
    table.ajax.reload();
  }

  // Reset filters
  function resetFilters() {
    $('#filterStatus').val('all');
    $('#filterSearch').val('');
    $('#filterVerified').val('');
    $('#filterMembershipType').val('');
    $('#filterDateFrom').val('');
    $('#filterDateTo').val('');
    table.ajax.reload();
  }

  // Bulk actions
  function clearSelection() {
    selectedRows.clear();
    updateBulkBar();
    $('#selectAll').prop('checked', false);
    $('.row-select').prop('checked', false);
  }

  function updateBulkBar() {
    const count = selectedRows.size;
    if (count > 0) {
      $('#bulkActionsBar').addClass('active');
      $('#bulkCount').text(count);
    } else {
      $('#bulkActionsBar').removeClass('active');
    }
  }

  function bulkActivate() {
    if (selectedRows.size === 0) return;
    if (confirm(`Activate ${selectedRows.size} selected members?`)) {
      showToast('Members activated successfully!');
      clearSelection();
      table.ajax.reload();
    }
  }

  function bulkDisable() {
    if (selectedRows.size === 0) return;
    if (confirm(`Disable ${selectedRows.size} selected members?`)) {
      showToast('Members disabled successfully!');
      clearSelection();
      table.ajax.reload();
    }
  }

  function bulkExport() {
    if (selectedRows.size === 0) return;
    const ids = Array.from(selectedRows).join(',');
    window.location.href = `<?= base_url('admin/membership/export') ?>?ids=${ids}`;
  }

  // Quick View
  // NOTE: You'll need to create this endpoint in your controller:
  // public function quickView($id) {
  //   $member = $this->memberModel->find($id);
  //   return $this->response->setJSON(['member' => $member]);
  // }
  function showQuickView(memberId) {
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('quickViewModal'));
    modal.show();

    // Fetch member data
    fetch(`<?= base_url('admin/membership') ?>/${memberId}/quick-view`)
      .then(res => {
        if (!res.ok) {
          throw new Error('Endpoint not found - please create the quick-view endpoint');
        }
        return res.json();
      })
      .then(data => {
        const m = data.member;

        // Update avatar
        const initials = (m.first_name[0] + m.last_name[0]).toUpperCase();
        $('#qvAvatar').text(initials);

        // Update header
        $('#qvName').text(`${m.first_name} ${m.last_name}`);
        $('#qvEmail').text(m.email);

        // Status badge
        let badgeClass = 'bg-secondary';
        if (m.status === 'active') badgeClass = 'bg-success';
        if (m.status === 'pending') badgeClass = 'bg-warning';
        $('#qvBadge').html(`<span class="badge ${badgeClass}">LCNL${m.id} - ${m.status}</span>`);

        // Content
        $('#qvContent').html(`
          <div class="row g-3">
            <div class="col-md-6">
              <div class="mb-3">
                <div class="text-muted small mb-1">Mobile</div>
                <div class="fw-semibold">${m.mobile || '—'}</div>
              </div>
              <div class="mb-3">
                <div class="text-muted small mb-1">City</div>
                <div class="fw-semibold">${m.city || '—'}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <div class="text-muted small mb-1">Member Since</div>
                <div class="fw-semibold">${new Date(m.created_at).toLocaleDateString()}</div>
              </div>
              <div class="mb-3">
                <div class="text-muted small mb-1">Last Login</div>
                <div class="fw-semibold">${m.last_login ? new Date(m.last_login).toLocaleDateString() : 'Never'}</div>
              </div>
            </div>
          </div>
        `);

        // Update full profile link
        $('#qvViewFull').attr('href', `<?= base_url('admin/membership') ?>/${memberId}`);
      })
      .catch(err => {
        console.error(err);
        $('#qvContent').html(`
          <div class="alert alert-warning">
            <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Quick View Not Available</h6>
            <p class="mb-2">The quick-view endpoint hasn't been created yet. Please add this to your controller:</p>
            <pre class="bg-light p-2 rounded small mb-0"><code>public function quickView($id) {
    $member = $this->memberModel->find($id);
    return $this->response->setJSON(['member' => $member]);
}</code></pre>
          </div>
        `);
      });
  }

  // Toast notification
  function showToast(message, type = 'success') {
    const toast = document.getElementById('actionToast');
    const toastBody = toast.querySelector('.toast-body');

    // Update style
    toastBody.className = `toast-body rounded-3 d-flex align-items-center gap-2 ${type === 'success' ? 'bg-success' : 'bg-danger'} text-white`;

    // Update message
    toast.querySelector('.toast-message').textContent = message;

    // Show toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
  }

  // Apply advanced filters
  function applyAdvancedFilters() {
    bootstrap.Modal.getInstance(document.getElementById('advancedFiltersModal')).hide();
    table.ajax.reload();
    showToast('Advanced filters applied');
  }

  $(function () {

    // DataTable Init
    table = $('#membersTable').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      pageLength: 25,
      order: [
        [1, 'desc']
      ],

      ajax: {
        url: "<?= base_url('admin/membership/data') ?>",
        type: "POST",
        data: function (d) {
          d.status = $('#filterStatus').val();
          d.searchTerm = $('#filterSearch').val();
          d.verified = $('#filterVerified').val();
          d.membershipType = $('#filterMembershipType').val();
          d.dateFrom = $('#filterDateFrom').val();
          d.dateTo = $('#filterDateTo').val();
          d[CSRF.name] = CSRF.hash;
        }
      },

      columns: [{
        data: null,
        orderable: false,
        searchable: false,
        render: function (data, type, row) {
          return `<input type="checkbox" class="form-check-input row-select" data-id="${row.id}">`;
        }
      },
      {
        data: "id"
      },
      {
        data: "name",
        render: function (data, type, row) {
          // If already formatted HTML, return as-is
          if (type === 'display' && data && data.includes('<')) {
            return data;
          }
          // Otherwise enhance with our styling
          return `<div class="d-flex align-items-center gap-2">
                      <div class="fw-semibold">${data}</div>
                    </div>`;
        }
      },
      {
        data: "email_html",
        orderable: false
      },
      {
        data: "email_validity_html",
        orderable: false,
        searchable: false
      },
      {
        data: "mobile",
        render: d => d && d !== '0' ? `<span class="text-nowrap">${d}</span>` : '<span class="text-muted">—</span>'
      },
      {
        data: "city"
      },
      {
        data: "status_badge",
        orderable: true,
        render: function (data, type, row) {
          // If server returns pre-rendered HTML, use it
          if (type === 'display' && data && data.includes('<')) {
            return data;
          }

          // Otherwise, render our enhanced badges
          const status = row.status || data;
          let badgeClass = 'badge-active';
          let icon = 'check-circle-fill';

          if (status === 'pending') {
            badgeClass = 'badge-pending';
            icon = 'hourglass-split';
          } else if (status === 'disabled') {
            badgeClass = 'badge-disabled';
            icon = 'x-circle-fill';
          }

          return `<span class="badge-status ${badgeClass}">
                      <i class="bi bi-${icon}"></i>
                      ${status.charAt(0).toUpperCase() + status.slice(1)}
                    </span>`;
        }
      },
      {
        data: "created_at",
        render: function (data, type, row) {
          if (type === 'display') {
            // If it looks like it's already formatted (no time component), return as-is
            if (data && !data.includes(':') && !data.includes('T')) {
              return data;
            }
            // Otherwise format it
            try {
              return new Date(data).toLocaleDateString();
            } catch (e) {
              return data;
            }
          }
          return data;
        }
      },
      {
        data: "actions",
        orderable: false,
        searchable: false,
        render: function (data, type, row) {
          // If server returns pre-rendered HTML, enhance it
          if (type === 'display' && data && data.includes('<')) {
            // Keep server rendering but wrap in our styled container
            return `<div class="d-flex gap-2 justify-content-end">${data}</div>`;
          }

          // Otherwise use our enhanced action buttons
          return `<div class="d-flex gap-2 justify-content-end">
                      <button class="btn btn-action btn-action-view btn-sm" onclick="showQuickView(${row.id})" title="Quick View">
                        <i class="bi bi-eye"></i>
                      </button>
                      <a href="<?= base_url('admin/membership') ?>/${row.id}" class="btn btn-action btn-action-edit btn-sm" title="View Details">
                        <i class="bi bi-box-arrow-up-right"></i>
                      </a>
                      <a href="<?= base_url('admin/membership') ?>/${row.id}/edit" class="btn btn-action btn-action-edit btn-sm" title="Edit">
                        <i class="bi bi-pencil"></i>
                      </a>
                    </div>`;
        }
      }
      ],

      language: {
        processing: '<div class="spinner-border text-brand" role="status"><span class="visually-hidden">Loading...</span></div>',
        emptyTable: '<div class="text-center py-5"><i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i><h6>No members found</h6><p class="text-muted">Try adjusting your filters</p></div>'
      }
    });

    // Search triggers
    $('#btnSearch').on('click', () => table.ajax.reload());
    $('#filterStatus').on('change', () => table.ajax.reload());
    $('#filterSearch').on('keyup', _.debounce(() => table.ajax.reload(), 300));
    $('#filterVerified, #filterMembershipType, #filterDateFrom, #filterDateTo').on('change', () => table.ajax.reload());

    // Select all
    $('#selectAll').on('change', function () {
      const isChecked = $(this).prop('checked');
      $('.row-select:visible').prop('checked', isChecked).trigger('change');
    });

    // Row selection
    $('#membersTable').on('change', '.row-select', function () {
      const memberId = $(this).data('id');
      if ($(this).prop('checked')) {
        selectedRows.add(memberId);
      } else {
        selectedRows.delete(memberId);
        $('#selectAll').prop('checked', false);
      }
      updateBulkBar();
    });

    // Email validity toggle
    $('#membersTable').on('click', '.js-toggle-email-validity', async function () {
      const btn = $(this);
      const memberId = btn.data('id');
      const email = btn.data('email');

      const reason = prompt(
        `Reason for changing email validity:\n\n${email}`,
        'Hard bounce'
      );

      if (reason === null) return;

      const form = new URLSearchParams();
      form.set('reason', reason);
      form.set(CSRF.name, CSRF.hash);

      try {
        const res = await fetch(
          `<?= base_url('admin/membership') ?>/${memberId}/email-validity`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: form.toString()
        }
        );

        if (!res.ok) {
          console.error(await res.text());
          showToast('Failed to update email validity', 'error');
          return;
        }

        const data = await res.json();

        if (data.csrf) {
          CSRF.name = data.csrf.tokenName;
          CSRF.hash = data.csrf.tokenHash;
        }

        if (data.success) {
          table.ajax.reload(null, false);
          showToast('Email validity updated successfully');
        } else {
          showToast(data.message || 'Failed to update', 'error');
        }

      } catch (err) {
        console.error(err);
        showToast('Network error occurred', 'error');
      }
    });

    // Column visibility
    const columns = table.columns().header().toArray().map((th, idx) => ({
      idx: idx,
      name: $(th).text().trim()
    })).filter(col => col.idx > 0); // Skip checkbox column

    columns.forEach(col => {
      $('#columnToggle').append(`
        <li class="column-toggle-item">
          <label class="mb-0 d-flex align-items-center">
            <input type="checkbox" checked data-column="${col.idx}">
            <span class="ms-2">${col.name}</span>
          </label>
        </li>
      `);
    });

    $('#columnToggle').on('change', 'input[type="checkbox"]', function () {
      const column = table.column($(this).data('column'));
      column.visible($(this).prop('checked'));
    });

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Keyboard shortcuts
    $(document).on('keydown', function (e) {
      // Ctrl/Cmd + K to focus search
      if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        $('#filterSearch').focus();
      }
      // Escape to clear selection
      if (e.key === 'Escape' && selectedRows.size > 0) {
        clearSelection();
      }
    });

  });
</script>

<?= $this->endSection() ?>

