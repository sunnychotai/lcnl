<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">
            <i class="bi bi-envelope-fill me-2"></i>Email Queue
        </h2>
        <div>
            <button id="refreshStats" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
            <a href="<?= base_url('admin/system/cron-logs') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-clock-history"></i> Cron Logs
            </a>
        </div>
    </div>

    <?php if ($message = session()->getFlashdata('message')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= esc($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- STATS -->
    <div class="row g-2 mb-3" id="emailStats">
        <div class="col-4">
            <div class="card text-center border-warning shadow-sm">
                <div class="card-body p-2">
                    <div class="fw-bold text-warning fs-5" id="statPending">
                        <span class="spinner-border spinner-border-sm"></span>
                    </div>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="card text-center border-danger shadow-sm">
                <div class="card-body p-2">
                    <div class="fw-bold text-danger fs-5" id="statFailed">
                        <span class="spinner-border spinner-border-sm"></span>
                    </div>
                    <small class="text-muted">Failed</small>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="card text-center border-success shadow-sm">
                <div class="card-body p-2">
                    <div class="fw-bold text-success fs-5" id="statSentToday">
                        <span class="spinner-border spinner-border-sm"></span>
                    </div>
                    <small class="text-muted">Sent Today</small>
                </div>
            </div>
        </div>
    </div>

    <!-- PRESET FILTERS -->
    <div class="btn-group btn-group-sm w-100 mb-3" role="group">
        <button class="btn btn-outline-danger preset" data-priority="1" data-status="">
            <i class="bi bi-exclamation-triangle-fill"></i> P1 Critical
        </button>
        <button class="btn btn-outline-warning preset" data-priority="2" data-status="">
            <i class="bi bi-exclamation-circle-fill"></i> P2 High
        </button>
        <button class="btn btn-outline-secondary preset" data-status="failed" data-priority="">
            <i class="bi bi-x-circle-fill"></i> Failed
        </button>
        <button class="btn btn-outline-dark preset" data-status="pending" data-priority="">
            <i class="bi bi-clock-fill"></i> Pending
        </button>
        <button class="btn btn-outline-info preset" data-status="sent" data-priority="">
            <i class="bi bi-check-circle-fill"></i> Sent
        </button>
    </div>

    <!-- FILTERS -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body p-2">
            <div class="row g-2 align-items-end">

                <div class="col-12 col-md-4">
                    <label class="form-label mb-1 small fw-bold">Status</label>
                    <select id="filterStatus" class="form-select form-select-sm">
                        <option value="">All statuses</option>
                        <option value="pending">Pending</option>
                        <option value="sending">Sending</option>
                        <option value="sent">Sent</option>
                        <option value="failed">Failed</option>
                        <option value="invalid">Invalid</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label mb-1 small fw-bold">Priority</label>
                    <select id="filterPriority" class="form-select form-select-sm">
                        <option value="">All priorities</option>
                        <option value="1">P1 – Critical</option>
                        <option value="2">P2 – High</option>
                        <option value="3">P3 – Normal</option>
                        <option value="4">P4 – Low</option>
                        <option value="5">P5 – Bulk</option>
                    </select>
                </div>

                <div class="col-12 col-md-4 d-grid">
                    <button id="clearFilters" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-lg"></i> Clear Filters
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-responsive">
        <table id="emailTable" class="table table-striped table-hover align-middle w-100">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>To</th>
                    <th>Subject</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Attempts</th>
                    <th>Scheduled</th>
                    <th>Sent</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>

</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(function() {

        // CSRF Token - refresh from meta or keep current
        function getCsrfToken() {
            return $('meta[name="<?= csrf_token() ?>"]').attr('content') || '<?= csrf_hash() ?>';
        }

        // Restore filters from localStorage
        const savedStatus = localStorage.getItem('emailStatus') || '';
        const savedPriority = localStorage.getItem('emailPriority') || '';

        $('#filterStatus').val(savedStatus);
        $('#filterPriority').val(savedPriority);

        // Initialize DataTable
        const table = $('#emailTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 25,
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],

            ajax: {
                url: "<?= base_url('admin/system/emails/data') ?>",
                type: "POST",
                data: function(d) {
                    d.status = $('#filterStatus').val();
                    d.priority = $('#filterPriority').val();
                    d['<?= csrf_token() ?>'] = getCsrfToken();
                },
                error: function(xhr, error, code) {
                    console.error('DataTable error:', error, code);
                    alert('Error loading email data. Please refresh the page.');
                }
            },

            columns: [{
                    data: 'id',
                    width: '50px'
                },
                {
                    data: 'to_email'
                },
                {
                    data: 'subject'
                },
                {
                    data: 'priority',
                    width: '80px'
                },
                {
                    data: 'status',
                    width: '80px'
                },
                {
                    data: 'attempts',
                    width: '80px'
                },
                {
                    data: 'scheduled_at',
                    width: '140px'
                },
                {
                    data: 'sent_at',
                    width: '140px'
                },
                {
                    data: 'actions',
                    orderable: false,
                    searchable: false,
                    width: '100px'
                }
            ],

            order: [
                [0, 'desc']
            ],

            language: {
                processing: '<div class="spinner-border spinner-border-sm text-primary"></div> Loading...',
                emptyTable: 'No emails in queue',
                zeroRecords: 'No matching emails found'
            }
        });

        // Save filters to localStorage and reload
        function applyFilters() {
            const status = $('#filterStatus').val();
            const priority = $('#filterPriority').val();

            localStorage.setItem('emailStatus', status);
            localStorage.setItem('emailPriority', priority);

            table.ajax.reload();
        }

        $('#filterStatus, #filterPriority').on('change', applyFilters);

        // Preset filter buttons
        $('.preset').on('click', function() {
            const $btn = $(this);
            const status = $btn.data('status') || '';
            const priority = $btn.data('priority') || '';

            // Update UI
            $('#filterStatus').val(status);
            $('#filterPriority').val(priority);

            // Apply filters
            applyFilters();

            // Visual feedback
            $('.preset').removeClass('active');
            $btn.addClass('active');
        });

        // Clear all filters
        $('#clearFilters').on('click', function() {
            $('#filterStatus').val('');
            $('#filterPriority').val('');
            localStorage.removeItem('emailStatus');
            localStorage.removeItem('emailPriority');
            $('.preset').removeClass('active');
            table.ajax.reload();
        });

        // Load stats
        function loadStats() {
            $('#statPending, #statFailed, #statSentToday').html('<span class="spinner-border spinner-border-sm"></span>');

            $.ajax({
                url: "<?= base_url('admin/system/emails/stats') ?>",
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#statPending').text(data.pending || 0);
                    $('#statFailed').text(data.failed || 0);
                    $('#statSentToday').text(data.sentToday || 0);
                },
                error: function(xhr, status, error) {
                    console.error('Stats error:', error);
                    $('#statPending, #statFailed, #statSentToday').text('–');
                }
            });
        }

        // Refresh stats button
        $('#refreshStats').on('click', function() {
            const $btn = $(this);
            $btn.prop('disabled', true);
            loadStats();
            table.ajax.reload();
            setTimeout(() => $btn.prop('disabled', false), 1000);
        });

        // Auto-refresh stats every 30 seconds
        loadStats();
        setInterval(loadStats, 30000);

        // Restore active preset button on page load
        if (savedStatus || savedPriority) {
            $('.preset').each(function() {
                const $btn = $(this);
                if ($btn.data('status') == savedStatus && $btn.data('priority') == savedPriority) {
                    $btn.addClass('active');
                }
            });
        }

    });
</script>

<?= $this->endSection() ?>