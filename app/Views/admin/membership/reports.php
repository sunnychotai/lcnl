<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<style>
    .stat-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 0.875rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: white;
        flex-shrink: 0;
    }

    .stat-value {
        font-size: 1.4rem;
        font-weight: 700;
        color: #1f2937;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #6b7280;
        font-weight: 500;
        margin-top: 0.125rem;
    }

    .report-row {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .report-row + .report-row {
        margin-top: 0.75rem;
    }

    .report-row-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: white;
        flex-shrink: 0;
    }

    .report-row-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .report-row-desc {
        font-size: 0.8125rem;
        color: #6b7280;
        margin: 0;
    }

    .icon-indigo  { background: linear-gradient(135deg, #6366f1, #4f46e5); }
    .icon-amber   { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .icon-sky     { background: linear-gradient(135deg, #0ea5e9, #0284c7); }
    .icon-green   { background: linear-gradient(135deg, #10b981, #059669); }
</style>

<div class="container-fluid py-4">

    <!-- Tabs -->
    <?php $activeTab = $activeTab ?? 'reports'; ?>
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admin/membership') ?>">
                <i class="bi bi-people me-1"></i>Members
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="<?= base_url('admin/membership/reports') ?>">
                <i class="bi bi-graph-up me-1"></i>Reports
            </a>
        </li>
    </ul>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="stat-card">
                <div class="stat-icon icon-indigo"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="stat-value"><?= number_format($stats['total']) ?></div>
                    <div class="stat-label">Total Members</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card">
                <div class="stat-icon icon-amber"><i class="bi bi-star-fill"></i></div>
                <div>
                    <div class="stat-value"><?= number_format($stats['life']) ?></div>
                    <div class="stat-label">Life Members</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card">
                <div class="stat-icon icon-sky"><i class="bi bi-award-fill"></i></div>
                <div>
                    <div class="stat-value"><?= number_format($stats['non_life']) ?></div>
                    <div class="stat-label">Non-Life Members</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card">
                <div class="stat-icon icon-green"><i class="bi bi-credit-card-fill"></i></div>
                <div>
                    <div class="stat-value"><?= number_format($stats['stripe']) ?></div>
                    <div class="stat-label">Paid via Stripe</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports -->
    <h6 class="text-muted fw-semibold text-uppercase mb-3" style="font-size:0.75rem;letter-spacing:.05em;">Download Reports</h6>

    <div class="report-row">
        <div class="report-row-icon icon-indigo"><i class="bi bi-people-fill"></i></div>
        <div class="flex-grow-1">
            <p class="report-row-title">All Members</p>
            <p class="report-row-desc"><?= number_format($stats['total']) ?> members &mdash; complete export of all member records</p>
        </div>
        <a href="<?= base_url('admin/membership/reports/export') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-download me-1"></i>CSV
        </a>
    </div>

    <div class="report-row">
        <div class="report-row-icon icon-amber"><i class="bi bi-star-fill"></i></div>
        <div class="flex-grow-1">
            <p class="report-row-title">Life Members</p>
            <p class="report-row-desc"><?= number_format($stats['life']) ?> members &mdash; active Life membership only</p>
        </div>
        <a href="<?= base_url('admin/membership/reports/life/export') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-download me-1"></i>CSV
        </a>
    </div>

    <div class="report-row">
        <div class="report-row-icon icon-sky"><i class="bi bi-award-fill"></i></div>
        <div class="flex-grow-1">
            <p class="report-row-title">Non-Life Members</p>
            <p class="report-row-desc"><?= number_format($stats['non_life']) ?> members &mdash; all members excluding Life membership</p>
        </div>
        <a href="<?= base_url('admin/membership/reports/non-life/export') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-download me-1"></i>CSV
        </a>
    </div>

    <div class="report-row">
        <div class="report-row-icon icon-green"><i class="bi bi-credit-card-fill"></i></div>
        <div class="flex-grow-1">
            <p class="report-row-title">Paid via Stripe (Active)</p>
            <p class="report-row-desc"><?= number_format($stats['stripe']) ?> members &mdash; active members with a completed Stripe payment</p>
        </div>
        <a href="<?= base_url('admin/membership/reports/stripe/export') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-download me-1"></i>CSV
        </a>
    </div>

</div>

<?= $this->endSection() ?>
