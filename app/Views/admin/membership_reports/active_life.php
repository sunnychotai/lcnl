<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-3">

    <!-- Tabs -->
    <?php $activeTab = 'reports'; ?>
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/membership') ?>">Members</a></li>
        <li class="nav-item"><a class="nav-link active" href="<?= base_url('admin/membership/reports') ?>">Reports</a>
        </li>
    </ul>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h5 mb-0">
            <i class="bi bi-star-fill me-2 text-brand"></i> Active LIFE Members
        </h1>
        <a href="<?= base_url('admin/membership/reports') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <?= view('admin/membership_reports/_filters') ?>

    <div class="lcnl-card shadow-soft border-0">
        <div class="table-responsive">
            <table id="reportTable" class="table table-striped table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>First</th>
                        <th>Last</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>City</th>
                        <th>Status</th>
                        <th>Type</th>
                        <th>Created</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
    let table;

    // ✅ CSRF holder (refresh on every response)
    let CSRF = {
        name: '<?= csrf_token() ?>',
        hash: '<?= csrf_hash() ?>'
    };

    function exportHref() {
        const qs = new URLSearchParams({
            status: $('#filterStatus').val(),
            membership_type: $('#filterType').val(),
            city: $('#filterCity').val()
        }).toString();

        return "<?= base_url('admin/membership/reports/active-life/export') ?>?" + qs;
    }

    $(function () {

        table = $('#reportTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 25,
            order: [[0, 'desc']],

            ajax: {
                url: "<?= base_url('admin/membership/reports/active-life/data') ?>",
                type: "POST",
                data: function (d) {
                    d.status = $('#filterStatus').val();
                    d.membership_type = $('#filterType').val();
                    d.city = $('#filterCity').val();

                    // ✅ Always send latest CSRF
                    d[CSRF.name] = CSRF.hash;
                },
                dataSrc: function (json) {
                    // ✅ Refresh CSRF from controller response (dtRespond now includes csrf)
                    if (json && json.csrf) {
                        CSRF.name = json.csrf.tokenName;
                        CSRF.hash = json.csrf.tokenHash;
                    }
                    return json.data || [];
                },
                error: function (xhr) {
                    // Helpful debugging: check Network tab response body
                    console.error('DataTables AJAX error:', xhr.status, xhr.responseText);
                }
            },

            columns: [
                { data: "id", name: "id" },
                { data: "first_name", name: "first_name" },
                { data: "last_name", name: "last_name" },
                { data: "email", name: "email" },
                { data: "mobile", name: "mobile" },
                { data: "city", name: "city" },
                { data: "status", name: "status" },
                { data: "membership_type", name: "membership_type" },
                { data: "created_at", name: "created_at" },
            ]
        });

        // Apply filters
        $('#btnApply').on('click', function () {
            table.ajax.reload();
        });

        // Export button in your shared _filters view
        $('#btnExport').attr('href', exportHref());

        $('#filterStatus, #filterType, #filterCity').on('change keyup', function () {
            $('#btnExport').attr('href', exportHref());
        });
    });
</script>

<?= $this->endSection() ?>

