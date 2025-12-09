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
            ['Missing Gender', $stats['missing_gender'] ?? 0, 'bi-gender-ambiguous'],
            ['Missing DOB', $stats['missing_dob'] ?? 0, 'bi-calendar-x'],
            ['Not Verified', $stats['not_verified'] ?? 0, 'bi-shield-x'],
            ['Pending', $stats['pending'] ?? 0, 'bi-hourglass-split'],
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
                            <td class="fw-semibold">
                                <i class="bi bi-star-fill text-warning me-2"></i>
                                Active Life Members
                            </td>
                            <td>All members with LIFE membership that are ACTIVE.</td>
                            <td class="text-end">
                                <a href="<?= base_url('admin/membership/reports/active-life') ?>"
                                    class="btn btn-sm btn-outline-brand">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td class="fw-semibold">
                                <i class="bi bi-envelope-slash me-2 text-brand"></i>
                                Email Unknown
                            </td>
                            <td>Members whose email ends with <code>@lcnl.org</code>.</td>
                            <td class="text-end">
                                <a href="<?= base_url('admin/membership/reports/email-unknown') ?>"
                                    class="btn btn-sm btn-outline-brand">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td class="fw-semibold">
                                <i class="bi bi-telephone-x me-2 text-brand"></i>
                                Mobile Missing
                            </td>
                            <td>Members with missing mobile numbers.</td>
                            <td class="text-end">
                                <a href="<?= base_url('admin/membership/reports/mobile-missing') ?>"
                                    class="btn btn-sm btn-outline-brand">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td class="fw-semibold">
                                <i class="bi bi-gender-ambiguous me-2 text-brand"></i>
                                Missing Gender
                            </td>
                            <td>Members with no gender recorded.</td>
                            <td class="text-end">
                                <a href="<?= base_url('admin/membership/reports/missing-gender') ?>"
                                    class="btn btn-sm btn-outline-brand">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td class="fw-semibold">
                                <i class="bi bi-calendar-x me-2 text-brand"></i>
                                Missing DOB
                            </td>
                            <td>Members with no date of birth recorded.</td>
                            <td class="text-end">
                                <a href="<?= base_url('admin/membership/reports/missing-dob') ?>"
                                    class="btn btn-sm btn-outline-brand">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td class="fw-semibold">
                                <i class="bi bi-shield-x me-2 text-brand"></i>
                                Not Verified
                            </td>
                            <td>Members who have not verified their account.</td>
                            <td class="text-end">
                                <a href="<?= base_url('admin/membership/reports/not-verified') ?>"
                                    class="btn btn-sm btn-outline-brand">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td class="fw-semibold">
                                <i class="bi bi-hourglass-split me-2 text-brand"></i>
                                Still Pending
                            </td>
                            <td>Members whose status is currently <code>pending</code>.</td>
                            <td class="text-end">
                                <a href="<?= base_url('admin/membership/reports/pending') ?>"
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

