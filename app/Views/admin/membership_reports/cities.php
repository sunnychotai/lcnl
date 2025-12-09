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
            <i class="bi bi-envelope-slash me-2 text-brand"></i> Email Unknown (@lcnl.org)
        </h1>
        <a href="<?= base_url('admin/membership/reports') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <?= view('admin/membership_reports/_filters') // paste shared filter bar here or inline ?>

    <div class="lcnl-card shadow-soft border-0">
        <div class="table-responsive">
            <table id="reportTable" class="table table-striped table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>City</th>
                        <th>Total</th>
                    </tr>
                </thead>

            </table>
        </div>
    </div>
</div>

<!-- DT assets -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
    let table;

    function exportHref() {
        const qs = new URLSearchParams({
            status: $('#filterStatus').val(),
            membership_type: $('#filterType').val(),
            city: $('#filterCity').val()
        }).toString();
        return "<?= base_url('admin/membership/reports/membership-types/export') ?>?" + qs;
    }

    $(function () {
        table = $('#reportTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 25,
            ajax: {
                url: "<?= base_url('admin/membership/reports/membership-types/data') ?>",
                type: "POST",
                data: function (d) {
                    d.status = $('#filterStatus').val();
                    d.membership_type = $('#filterType').val();
                    d.city = $('#filterCity').val();
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                }
            },
            columns: [
                { data: "city", name: "m.city" },
                { data: "total" }
            ],
            order: [[1, 'desc']]

        });

        $('#btnApply').on('click', function () { table.ajax.reload(); });
        $('#btnExport').attr('href', exportHref());
        $('#filterStatus, #filterType, #filterCity').on('change keyup', function () {
            $('#btnExport').attr('href', exportHref());
        });
    });
</script>

<?= $this->endSection() ?>

