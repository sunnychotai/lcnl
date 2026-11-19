<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- jQuery (must be first) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-ocean d-flex align-items-center justify-content-center">
    <div class="container position-relative text-center text-white py-3">
        <h1 class="fw-bold display-6 mb-2">Event Administration</h1>
        <p class="lead fs-5 mb-0">Registration Summary</p>
    </div>
</section>

<div class="container py-4">

    <?php if ($msg = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">
            <i class="bi bi-clipboard-data me-2"></i>
            Event Registration Summary
        </h2>
    </div>

    <p class="text-muted mb-4">
        Registrations grouped by event. Regs and people exclude cancelled bookings.
        Click an event to see its individual registrations.
    </p>

    <div class="card shadow-sm rounded-4">
        <div class="card-body">

            <table id="eventSummaryTable" class="table table-striped align-middle w-100">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Event Date</th>
                        <th>Regs</th>
                        <th>Total People</th>
                        <th>Guests</th>
                        <th>Member Regs</th>
                        <th>Confirmed</th>
                        <th>Cancelled</th>
                        <th>Last Registered</th>
                        <th></th>
                    </tr>
                </thead>
            </table>

        </div>
    </div>

</div>

<script>
    $(function () {
        const detailUrl = '<?= site_url('admin/content/events/event-registrations') ?>/';
        const escHtml = s => $('<div>').text(s ?? '').html();

        $('#eventSummaryTable').DataTable({
            ajax: {
                url: '<?= site_url('admin/content/events/event-registrations/summary') ?>'
            },

            pageLength: 25,
            responsive: true,
            order: [[8, 'desc']],
            columns: [
                {
                    data: 'event_name',
                    render: (d, type, row) => type === 'display'
                        ? '<a class="fw-semibold" href="' + detailUrl + row.event_id + '">' + escHtml(d) + '</a>'
                        : d
                },
                {
                    data: 'event_date',
                    render: (d, type) => type === 'display' && d
                        ? new Date(d.replace(' ', 'T')).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
                        : (d || '')
                },
                { data: 'registrations' },
                { data: 'total_people' },
                { data: 'total_guests' },
                { data: 'member_registrations' },
                { data: 'confirmed' },
                { data: 'cancelled' },
                {
                    data: 'last_registered_at',
                    render: (d, type) => type === 'display'
                        ? (d ? new Date(d.replace(' ', 'T')).toLocaleString('en-GB') : '-')
                        : (d || '')
                },
                {
                    data: 'event_id',
                    orderable: false,
                    searchable: false,
                    className: 'text-end',
                    render: d => '<a class="btn btn-sm btn-outline-primary rounded-pill" href="' + detailUrl + d + '">'
                        + '<i class="bi bi-people me-1"></i>View</a>'
                }
            ]
        });
    });
</script>

<?= $this->endSection() ?>

