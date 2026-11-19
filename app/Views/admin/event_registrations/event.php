<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- jQuery + DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<?php
$statusBadges = [
    'submitted' => ['bg-warning text-dark', 'Submitted'],
    'confirmed' => ['bg-success', 'Confirmed'],
    'cancelled' => ['bg-secondary', 'Cancelled'],
];
$statusUrl = site_url('admin/content/events/event-registrations/status');
?>

<section class="hero-lcnl-watermark hero-overlay-ocean d-flex align-items-center justify-content-center">
    <div class="container position-relative text-center text-white py-3">
        <h1 class="fw-bold display-6 mb-2"><?= esc($event['title']) ?></h1>
        <p class="lead fs-5 mb-0">
            Registrations
            <?php if (!empty($event['event_date'])): ?>
                &middot; <?= date('j M Y', strtotime($event['event_date'])) ?>
            <?php endif; ?>
        </p>
    </div>
</section>

<div class="container py-4">

    <?php if ($msg = session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i><?= esc($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($msg = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <a href="<?= site_url('admin/content/events/event-registrations') ?>" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i>All events
        </a>
        <a href="<?= site_url('admin/content/events/event-registrations/' . $event['id'] . '/export') ?>"
            class="btn btn-success rounded-pill px-4">
            <i class="bi bi-download me-2"></i>Export CSV
        </a>
    </div>

    <!-- Summary stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm text-center py-3 h-100">
                <div class="fs-2 fw-bold text-brand"><?= $stats['live'] ?></div>
                <div class="small text-muted">Registrations</div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm text-center py-3 h-100">
                <div class="fs-2 fw-bold text-primary"><?= $stats['people'] ?></div>
                <div class="small text-muted">
                    Total people
                    <?php if (!empty($event['max_headcount'])): ?>
                        <span class="d-block">of <?= (int) $event['max_headcount'] ?> max</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm text-center py-3 h-100">
                <div class="fs-2 fw-bold text-warning"><?= $stats['submitted'] ?></div>
                <div class="small text-muted">Submitted</div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm text-center py-3 h-100">
                <div class="fs-2 fw-bold text-success"><?= $stats['confirmed'] ?></div>
                <div class="small text-muted">Confirmed</div>
            </div>
        </div>
        <div class="col-12 col-md">
            <div class="card border-0 shadow-sm text-center py-3 h-100">
                <div class="fs-2 fw-bold text-secondary"><?= $stats['cancelled'] ?></div>
                <div class="small text-muted">Cancelled</div>
            </div>
        </div>
    </div>

    <p class="text-muted small mb-3">
        Registrations and total people exclude cancelled bookings, which also free up the event's capacity.
        Changing a status here does not email the person.
    </p>

    <div class="card shadow-sm rounded-4">
        <div class="card-body table-responsive">
            <?php if (empty($registrations)): ?>
                <p class="text-muted text-center my-4">No registrations for this event yet.</p>
            <?php else: ?>
                <table id="registrationsTable" class="table table-striped align-middle w-100 small">
                    <thead>
                        <tr>
                            <th>Registered</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th class="text-center">Guests</th>
                            <th class="text-center">Party</th>
                            <th>Member</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registrations as $r): ?>
                            <?php
                            $name  = trim($r['first_name'] . ' ' . $r['last_name']);
                            $badge = $statusBadges[$r['status']] ?? ['bg-light text-dark', ucfirst($r['status'])];
                            $party = (int) $r['num_participants'] + (int) $r['num_guests'];
                            ?>
                            <tr class="<?= $r['status'] === 'cancelled' ? 'text-muted' : '' ?>">
                                <td data-order="<?= esc($r['created_at'], 'attr') ?>" class="text-nowrap">
                                    <?= date('d/m/Y H:i', strtotime($r['created_at'])) ?>
                                </td>
                                <td class="fw-semibold">
                                    <?= $r['status'] === 'cancelled' ? '<s>' . esc($name) . '</s>' : esc($name) ?>
                                </td>
                                <td><a href="mailto:<?= esc($r['email'], 'attr') ?>"><?= esc($r['email']) ?></a></td>
                                <td class="text-nowrap">
                                    <a href="tel:<?= esc(preg_replace('/[^\d+]/', '', $r['phone']), 'attr') ?>"><?= esc($r['phone']) ?></a>
                                </td>
                                <td class="text-center"><?= (int) $r['num_guests'] ?></td>
                                <td class="text-center"><?= $party ?></td>
                                <td>
                                    <?php if (!empty($r['member_id'])): ?>
                                        <a href="<?= site_url('admin/membership/' . (int) $r['member_id']) ?>"
                                            class="badge bg-info text-dark text-decoration-none">Member #<?= (int) $r['member_id'] ?></a>
                                    <?php else: ?>
                                        <span class="text-muted">Guest</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($r['notes'])): ?>
                                        <span class="d-inline-block text-truncate align-bottom" style="max-width: 11rem;"
                                            title="<?= esc($r['notes'], 'attr') ?>"><?= esc($r['notes']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge <?= $badge[0] ?>"><?= esc($badge[1]) ?></span></td>
                                <td class="text-end text-nowrap">
                                    <?php if ($r['status'] === 'submitted'): ?>
                                        <form action="<?= $statusUrl . '/' . $r['id'] ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Confirm">
                                                <i class="bi bi-check-lg"></i><span class="visually-hidden">Confirm <?= esc($name) ?></span>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if ($r['status'] !== 'cancelled'): ?>
                                        <form action="<?= $statusUrl . '/' . $r['id'] ?>" method="post" class="d-inline"
                                            data-confirm="Cancel <?= esc($name, 'attr') ?>'s registration? This frees up <?= $party ?> place<?= $party === 1 ? '' : 's' ?>.">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancel">
                                                <i class="bi bi-x-lg"></i><span class="visually-hidden">Cancel <?= esc($name) ?></span>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form action="<?= $statusUrl . '/' . $r['id'] ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="status" value="submitted">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Reinstate">
                                                <i class="bi bi-arrow-counterclockwise"></i><span class="visually-hidden">Reinstate <?= esc($name) ?></span>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
    $(function () {
        $('#registrationsTable').DataTable({
            pageLength: 50,
            order: [[0, 'desc']],
            columnDefs: [{ targets: -1, orderable: false, searchable: false }]
        });

        $(document).on('submit', 'form[data-confirm]', function (e) {
            if (!window.confirm(this.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });
</script>

<?= $this->endSection() ?>
