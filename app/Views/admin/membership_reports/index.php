<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-3">

    <!-- Tabs -->
    <?php $activeTab = $activeTab ?? 'reports'; ?>
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admin/membership') ?>">Members</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="<?= base_url('admin/membership/reports') ?>">Reports</a>
        </li>
    </ul>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">
            <i class="bi bi-graph-up-arrow me-2 text-brand"></i> Membership Reports
        </h1>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <?php
        $cards = [
            ['Total Members', $stats['total'] ?? 0, 'bi-people-fill'],
            ['LIFE Members', $stats['life'] ?? 0, 'bi-star-fill'],
            ['STANDARD Members', $stats['standard'] ?? 0, 'bi-award-fill'],
            ['Email Unknown (@lcnl.org)', $stats['email_unknown'] ?? 0, 'bi-envelope-slash'],
            ['Mobile Missing', $stats['mobile_missing'] ?? 0, 'bi-phone'],
        ];
        ?>
        <?php foreach ($cards as [$label, $value, $icon]): ?>
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi <?= $icon ?> fs-2 text-brand mb-2"></i>
                        <div class="h3 fw-bold mb-0"><?= (int) $value ?></div>
                        <div class="small text-muted"><?= esc($label) ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Reports List -->
    <div class="lcnl-card shadow-soft border-0">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Available Reports</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Report</th>
                            <th>Description</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold">Email Unknown</td>
                            <td>Members whose email ends with <code>@lcnl.org</code>.</td>
                            <td class="text-end">
                                <a href="<?= base_url('admin/membership/reports/email-unknown') ?>"
                                    class="btn btn-sm btn-outline-brand">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Mobile Missing</td>
                            <td>Members with missing mobile numbers.</td>
                            <td class="text-end">
                                <a href="<?= base_url('admin/membership/reports/mobile-missing') ?>"
                                    class="btn btn-sm btn-outline-brand">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Membership Types</td>
                            <td>Current active membership type per member (Life/Standard).</td>
                            <td class="text-end">
                                <a href="<?= base_url('admin/membership/reports/membership-types') ?>"
                                    class="btn btn-sm btn-outline-brand">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Cities</td>
                            <td>Distribution by city with counts.</td>
                            <td class="text-end">
                                <a href="<?= base_url('admin/membership/reports/cities') ?>"
                                    class="btn btn-sm btn-outline-brand">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>

