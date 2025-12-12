<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">

    <h2 class="mb-3">
        <i class="bi bi-envelope-fill me-2"></i>Email Queue
    </h2>

    <?php if ($message = session()->getFlashdata('message')): ?>
        <div class="alert alert-success"><?= esc($message) ?></div>
    <?php endif; ?>

    <!-- STATS -->
    <div class="row g-2 mb-3" id="emailStats">
        <div class="col-4">
            <div class="card text-center border-warning">
                <div class="card-body p-2">
                    <div class="fw-bold text-warning fs-5" id="statPending">–</div>
                    <small>Pending</small>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="card text-center border-danger">
                <div class="card-body p-2">
                    <div class="fw-bold text-danger fs-5" id="statFailed">–</div>
                    <small>Failed</small>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="card text-center border-success">
                <div class="card-body p-2">
                    <div class="fw-bold text-success fs-5" id="statSentToday">–</div>
                    <small>Sent Today</small>
                </div>
            </div>
        </div>
    </div>

    <!-- PRESET FILTERS -->
    <div class="btn-group btn-group-sm w-100 mb-3">
        <button class="btn btn-outline-danger preset" data-priority="1">
            P1 Critical
        </button>
        <button class="btn btn-outline-warning preset" data-priority="2">
            P2 High
        </button>
        <button class="btn btn-outline-secondary preset" data-status="failed">
            Failed
        </button>
        <button class="btn btn-outline-dark preset" data-status="pending">
            Pending
        </button>
    </div>

    <!-- FILTERS -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body p-2">
            <div class="row g-2 align-items-end">

                <div class="col-12 col-md-4">
                    <label class="form-label mb-1 small">Status</label>
                    <select id="filterStatus" class="form-select form-select-sm">
                        <option value="">All statuses</option>
                        <option value="pending">Pending</option>
                        <option value="sent">Sent</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label mb-1 small">Priority</label>
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
                        Clear filters
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
    $(function () {

        // Restore filters
        $('#filterStatus').val(localStorage.getItem('emailStatus'));
        $('#filterPriority').val(localStorage.getItem('emailPriority'));

        const table = $('#emailTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 25,

            ajax: {
                url: "<?= base_url('admin/system/emails/data') ?>",
                type: "POST",
                data: function (d) {
                    d.status = $('#filterStatus').val();
                    d.priority = $('#filterPriority').val();
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                }
            },

            order: [[0, 'desc']]
        });

        // Save + reload on filter change
        $('#filterStatus, #filterPriority').on('change', function () {
            localStorage.setItem('emailStatus', $('#filterStatus').val());
            localStorage.setItem('emailPriority', $('#filterPriority').val());
            table.ajax.reload();
        });

        // Presets
        $('.preset').on('click', function () {
            $('#filterStatus').val($(this).data('status') || '');
            $('#filterPriority').val($(this).data('priority') || '');
            table.ajax.reload();
        });

        // Clear
        $('#clearFilters').on('click', function () {
            $('#filterStatus').val('');
            $('#filterPriority').val('');
            localStorage.removeItem('emailStatus');
            localStorage.removeItem('emailPriority');
            table.ajax.reload();
        });

        // Load stats
        function loadStats() {
            $.get("<?= base_url('admin/system/emails/stats') ?>", function (d) {
                $('#statPending').text(d.pending);
                $('#statFailed').text(d.failed);
                $('#statSentToday').text(d.sentToday);
            });
        }

        loadStats();
    });
</script>

<?= $this->endSection() ?>

