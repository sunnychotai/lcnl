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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">
            <i class="bi bi-clipboard-data me-2"></i>
            Event Registration Summary
        </h2>
    </div>

    <p class="text-muted mb-4">
        Registrations grouped by event name
    </p>

    <div class="card shadow-sm rounded-4">
        <div class="card-body">

            <table id="eventSummaryTable" class="table table-striped align-middle w-100">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Regs</th>
                        <th>Total People</th>
                        <th>Guests</th>
                        <th>Member Regs</th>
                        <th>Guest Regs</th>

                        <th>Last Registered</th>
                    </tr>
                </thead>
            </table>

        </div>
    </div>

</div>

<script>
    $(function () {
        $('#eventSummaryTable').DataTable({
            ajax: {
                url: '<?= site_url('admin/content/events/event-registrations/summary') ?>',
                type: 'POST'
            },

            pageLength: 25,
            responsive: true,
            columns: [
                { data: 'event_name' },
                { data: 'registrations' },
                { data: 'total_people' },
                { data: 'total_guests' },
                { data: 'member_registrations' },
                { data: 'guest_registrations' },

                {
                    data: 'last_registered_at',
                    render: d => d ? new Date(d.replace(' ', 'T')).toLocaleString() : '-'
                }
            ]
        });
    });
</script>

<?= $this->endSection() ?>

