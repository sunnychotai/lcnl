<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<style>
    /* Enhanced Header */
    .reports-header {
        background: linear-gradient(135deg, var(--brand) 0%, #982d52 100%);
        border-radius: 1rem;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(186, 27, 66, 0.2);
        position: relative;
        overflow: hidden;
    }

    .reports-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0) rotate(0deg);
        }

        50% {
            transform: translateY(-20px) rotate(5deg);
        }
    }

    .reports-icon {
        width: 70px;
        height: 70px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    /* Enhanced Stat Cards */
    .stat-card-report {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .stat-card-report::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--brand), #982d52);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .stat-card-report:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-color: var(--brand);
    }

    .stat-card-report:hover::before {
        transform: scaleX(1);
    }

    .stat-icon-report {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin: 0 auto 1rem;
        transition: all 0.3s ease;
    }

    .stat-card-report:hover .stat-icon-report {
        transform: scale(1.1) rotate(5deg);
    }

    .stat-value-report {
        font-size: 2.25rem;
        font-weight: 700;
        color: #1f2937;
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .stat-label-report {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }

    .stat-percentage {
        font-size: 0.75rem;
        color: #10b981;
        font-weight: 600;
        margin-top: 0.5rem;
    }

    /* Icon Colors */
    .icon-primary {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
    }

    .icon-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .icon-success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .icon-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .icon-info {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
    }

    .icon-purple {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }

    .icon-pink {
        background: linear-gradient(135deg, #ec4899, #db2777);
    }

    .icon-indigo {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
    }

    /* Report Cards */
    .report-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.5rem;
        transition: all 0.3s ease;
        margin-bottom: 1rem;
        position: relative;
        overflow: hidden;
    }

    .report-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, var(--brand), #982d52);
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .report-card:hover {
        border-color: var(--brand);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        transform: translateX(4px);
    }

    .report-card:hover::before {
        transform: scaleY(1);
    }

    .report-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .report-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .report-description {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0;
    }

    .report-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-report {
        padding: 0.5rem 1rem;
        border-radius: 0.75rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }

    .btn-report:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-view {
        background: linear-gradient(135deg, var(--brand), #982d52);
        color: white;
        border: none;
    }

    .btn-export {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .btn-export:hover {
        background: #e5e7eb;
        color: #1f2937;
    }

    /* Section Headers */
    .section-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .section-subtitle {
        font-size: 0.875rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    /* Enhanced Tabs */
    .nav-tabs {
        border-bottom: 2px solid #e5e7eb;
        margin-bottom: 2rem;
    }

    .nav-tabs .nav-link {
        border: none;
        color: #6b7280;
        font-weight: 600;
        padding: 1rem 1.5rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .nav-tabs .nav-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--brand);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .nav-tabs .nav-link:hover {
        color: var(--brand);
    }

    .nav-tabs .nav-link.active {
        color: var(--brand);
        background: none;
        border: none;
    }

    .nav-tabs .nav-link.active::after {
        transform: scaleX(1);
    }

    .nav-tabs .nav-link i {
        margin-right: 0.5rem;
    }

    /* Quick Stats Summary */
    .quick-stats {
        background: linear-gradient(135deg, #f8f9fa, #ffffff);
        border-radius: 1rem;
        padding: 2rem;
        margin-bottom: 2rem;
        border: 1px solid #e5e7eb;
    }

    .quick-stat-item {
        text-align: center;
        padding: 1rem;
    }

    .quick-stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--brand);
    }

    .quick-stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .reports-header {
            padding: 1.5rem;
        }

        .stat-value-report {
            font-size: 1.75rem;
        }

        .report-card {
            padding: 1rem;
        }
    }

    /* Animations */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stat-card-report {
        animation: slideIn 0.5s ease forwards;
    }

    .stat-card-report:nth-child(1) {
        animation-delay: 0s;
    }

    .stat-card-report:nth-child(2) {
        animation-delay: 0.05s;
    }

    .stat-card-report:nth-child(3) {
        animation-delay: 0.1s;
    }

    .stat-card-report:nth-child(4) {
        animation-delay: 0.15s;
    }

    .stat-card-report:nth-child(5) {
        animation-delay: 0.2s;
    }

    .stat-card-report:nth-child(6) {
        animation-delay: 0.25s;
    }

    .stat-card-report:nth-child(7) {
        animation-delay: 0.3s;
    }

    .stat-card-report:nth-child(8) {
        animation-delay: 0.35s;
    }

    .stat-card-report:nth-child(9) {
        animation-delay: 0.4s;
    }

    /* Badge */
    .badge-count {
        background: rgba(186, 27, 66, 0.1);
        color: var(--brand);
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.875rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state-icon {
        width: 100px;
        height: 100px;
        margin: 0 auto 1.5rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: #9ca3af;
    }

    .empty-state-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .empty-state-text {
        color: #6b7280;
        max-width: 400px;
        margin: 0 auto;
    }
</style>

<div class="container-fluid py-4">

    <!-- Enhanced Tabs -->
    <?php $activeTab = $activeTab ?? 'reports'; ?>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admin/membership') ?>">
                <i class="bi bi-people"></i>Members
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="<?= base_url('admin/membership/reports') ?>">
                <i class="bi bi-graph-up"></i>Reports
            </a>
        </li>
    </ul>

    <!-- Enhanced Header -->
    <div class="reports-header">
        <div class="position-relative" style="z-index: 1;">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="reports-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h1 class="h2 mb-2 fw-bold">Membership Reports</h1>
                    <p class="mb-0 opacity-90">Comprehensive insights and analytics for your membership data</p>
                </div>
                <div class="d-flex gap-2">
                    <div class="btn-group">
                        <button class="btn btn-light btn-pill dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-download me-1"></i>Export All
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item"
                                    href="<?= base_url('admin/membership/reports/export?format=csv') ?>">
                                    <i class="bi bi-filetype-csv me-2"></i>Export as CSV
                                </a></li>
                            <li><a class="dropdown-item"
                                    href="<?= base_url('admin/membership/reports/export?format=xlsx') ?>">
                                    <i class="bi bi-filetype-xlsx me-2"></i>Export as Excel
                                </a></li>
                            <li><a class="dropdown-item"
                                    href="<?= base_url('admin/membership/reports/export?format=pdf') ?>">
                                    <i class="bi bi-filetype-pdf me-2"></i>Export as PDF
                                </a></li>
                        </ul>
                    </div>
                    <button class="btn btn-outline-light btn-pill" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Summary Stats -->
    <div class="quick-stats">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="quick-stat-item">
                    <div class="quick-stat-value"><?= number_format($stats['total'] ?? 0) ?></div>
                    <div class="quick-stat-label">Total Members</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="quick-stat-item">
                    <div class="quick-stat-value">
                        <?= $stats['total'] > 0 ? number_format((($stats['life'] ?? 0) / $stats['total']) * 100, 1) : 0 ?>%
                    </div>
                    <div class="quick-stat-label">Life Members</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="quick-stat-item">
                    <div class="quick-stat-value">
                        <?= $stats['total'] > 0 ? number_format(((($stats['total'] - ($stats['email_invalid'] ?? 0)) / $stats['total']) * 100), 1) : 0 ?>%
                    </div>
                    <div class="quick-stat-label">Valid Emails</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="quick-stat-item">
                    <div class="quick-stat-value">
                        <?= $stats['total'] > 0 ? number_format(((($stats['total'] - ($stats['mobile_missing'] ?? 0)) / $stats['total']) * 100), 1) : 0 ?>%
                    </div>
                    <div class="quick-stat-label">Have Mobile</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Summary Cards -->
    <div class="mb-4">
        <div class="section-header">
            <div>
                <h2 class="section-title">Membership Overview</h2>
                <p class="section-subtitle">Key metrics and data quality indicators</p>
            </div>
        </div>

        <div class="row g-4">
            <?php
            $cards = [
                ['Total Members', $stats['total'] ?? 0, 'bi-people-fill', 'icon-primary', 100],
                ['LIFE Members', $stats['life'] ?? 0, 'bi-star-fill', 'icon-warning', ($stats['total'] > 0 ? (($stats['life'] ?? 0) / $stats['total']) * 100 : 0)],
                ['STANDARD Members', $stats['standard'] ?? 0, 'bi-award-fill', 'icon-success', ($stats['total'] > 0 ? (($stats['standard'] ?? 0) / $stats['total']) * 100 : 0)],
                ['Email Invalid', $stats['email_invalid'] ?? 0, 'bi-envelope-slash', 'icon-danger', ($stats['total'] > 0 ? (($stats['email_invalid'] ?? 0) / $stats['total']) * 100 : 0)],
                ['Mobile Missing', $stats['mobile_missing'] ?? 0, 'bi-telephone-x', 'icon-danger', ($stats['total'] > 0 ? (($stats['mobile_missing'] ?? 0) / $stats['total']) * 100 : 0)],
                ['Missing Gender', $stats['missing_gender'] ?? 0, 'bi-gender-ambiguous', 'icon-info', ($stats['total'] > 0 ? (($stats['missing_gender'] ?? 0) / $stats['total']) * 100 : 0)],
                ['Missing DOB', $stats['missing_dob'] ?? 0, 'bi-calendar-x', 'icon-purple', ($stats['total'] > 0 ? (($stats['missing_dob'] ?? 0) / $stats['total']) * 100 : 0)],
                ['Not Verified', $stats['not_verified'] ?? 0, 'bi-shield-x', 'icon-pink', ($stats['total'] > 0 ? (($stats['not_verified'] ?? 0) / $stats['total']) * 100 : 0)],
                ['Still Pending', $stats['pending'] ?? 0, 'bi-hourglass-split', 'icon-indigo', ($stats['total'] > 0 ? (($stats['pending'] ?? 0) / $stats['total']) * 100 : 0)],
            ];
            ?>
            <?php foreach ($cards as [$label, $value, $icon, $colorClass, $percentage]): ?>
                <div class="col-sm-6 col-lg-4 col-xl-3">
                    <div class="stat-card-report">
                        <div class="stat-icon-report <?= $colorClass ?>">
                            <i class="bi <?= $icon ?>"></i>
                        </div>
                        <div class="text-center">
                            <div class="stat-value-report"><?= number_format((int) $value) ?></div>
                            <div class="stat-label-report"><?= esc($label) ?></div>
                            <div class="stat-percentage"><?= number_format($percentage, 1) ?>% of total</div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Available Reports -->
    <div class="mb-4">
        <div class="section-header mb-4">
            <div>
                <h2 class="section-title">Available Reports</h2>
                <p class="section-subtitle">Click to view detailed member lists and export data</p>
            </div>
        </div>

        <div class="row g-4">

            <!-- Active Life Members -->
            <div class="col-lg-6">
                <div class="report-card">
                    <div class="d-flex gap-3">
                        <div class="report-icon-wrapper icon-warning">
                            <i class="bi bi-star-fill text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="report-title">Active Life Members</h3>
                            <p class="report-description">
                                All members with LIFE membership that are currently ACTIVE.
                                <span class="badge-count"><?= (int) ($stats['life'] ?? 0) ?> members</span>
                            </p>
                        </div>
                        <div class="report-actions">
                            <a href="<?= base_url('admin/membership/reports/active-life') ?>"
                                class="btn btn-view btn-report">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                            <a href="<?= base_url('admin/membership/reports/active-life?export=csv') ?>"
                                class="btn btn-export btn-report">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Invalid -->
            <div class="col-lg-6">
                <div class="report-card">
                    <div class="d-flex gap-3">
                        <div class="report-icon-wrapper icon-danger">
                            <i class="bi bi-envelope-slash text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="report-title">Email Invalid</h3>
                            <p class="report-description">
                                Members whose email ends with <code>@lcnl.org</code> or marked as invalid.
                                <span class="badge-count"><?= (int) ($stats['email_invalid'] ?? 0) ?> members</span>
                            </p>
                        </div>
                        <div class="report-actions">
                            <a href="<?= base_url('admin/membership/reports/email-invalid') ?>"
                                class="btn btn-view btn-report">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                            <a href="<?= base_url('admin/membership/reports/email-invalid?export=csv') ?>"
                                class="btn btn-export btn-report">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Missing -->
            <div class="col-lg-6">
                <div class="report-card">
                    <div class="d-flex gap-3">
                        <div class="report-icon-wrapper icon-danger">
                            <i class="bi bi-telephone-x text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="report-title">Mobile Missing</h3>
                            <p class="report-description">
                                Members with missing or empty mobile numbers.
                                <span class="badge-count"><?= (int) ($stats['mobile_missing'] ?? 0) ?> members</span>
                            </p>
                        </div>
                        <div class="report-actions">
                            <a href="<?= base_url('admin/membership/reports/mobile-missing') ?>"
                                class="btn btn-view btn-report">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                            <a href="<?= base_url('admin/membership/reports/mobile-missing?export=csv') ?>"
                                class="btn btn-export btn-report">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Missing Gender -->
            <div class="col-lg-6">
                <div class="report-card">
                    <div class="d-flex gap-3">
                        <div class="report-icon-wrapper icon-info">
                            <i class="bi bi-gender-ambiguous text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="report-title">Missing Gender</h3>
                            <p class="report-description">
                                Members with no gender information recorded.
                                <span class="badge-count"><?= (int) ($stats['missing_gender'] ?? 0) ?> members</span>
                            </p>
                        </div>
                        <div class="report-actions">
                            <a href="<?= base_url('admin/membership/reports/missing-gender') ?>"
                                class="btn btn-view btn-report">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                            <a href="<?= base_url('admin/membership/reports/missing-gender?export=csv') ?>"
                                class="btn btn-export btn-report">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Missing DOB -->
            <div class="col-lg-6">
                <div class="report-card">
                    <div class="d-flex gap-3">
                        <div class="report-icon-wrapper icon-purple">
                            <i class="bi bi-calendar-x text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="report-title">Missing Date of Birth</h3>
                            <p class="report-description">
                                Members with no date of birth recorded.
                                <span class="badge-count"><?= (int) ($stats['missing_dob'] ?? 0) ?> members</span>
                            </p>
                        </div>
                        <div class="report-actions">
                            <a href="<?= base_url('admin/membership/reports/missing-dob') ?>"
                                class="btn btn-view btn-report">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                            <a href="<?= base_url('admin/membership/reports/missing-dob?export=csv') ?>"
                                class="btn btn-export btn-report">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Not Verified -->
            <div class="col-lg-6">
                <div class="report-card">
                    <div class="d-flex gap-3">
                        <div class="report-icon-wrapper icon-pink">
                            <i class="bi bi-shield-x text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="report-title">Not Verified</h3>
                            <p class="report-description">
                                Members who have not verified their email address.
                                <span class="badge-count"><?= (int) ($stats['not_verified'] ?? 0) ?> members</span>
                            </p>
                        </div>
                        <div class="report-actions">
                            <a href="<?= base_url('admin/membership/reports/not-verified') ?>"
                                class="btn btn-view btn-report">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                            <a href="<?= base_url('admin/membership/reports/not-verified?export=csv') ?>"
                                class="btn btn-export btn-report">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Not Verified -->
            <div class="col-lg-6">
                <div class="report-card">
                    <div class="d-flex gap-3">
                        <div class="report-icon-wrapper icon-pink">
                            <i class="bi bi-shield-x text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="report-title">Disabled</h3>
                            <p class="report-description">
                                Membership status is Disabled.
                                <span class="badge-count"><?= (int) ($stats['disabled'] ?? 0) ?> members</span>
                            </p>
                        </div>
                        <div class="report-actions">
                            <a href="<?= base_url('admin/membership/reports/disabled') ?>"
                                class="btn btn-view btn-report">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                            <a href="<?= base_url('admin/membership/reports/disabled?export=csv') ?>"
                                class="btn btn-export btn-report">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Still Pending -->
            <div class="col-lg-6">
                <div class="report-card">
                    <div class="d-flex gap-3">
                        <div class="report-icon-wrapper icon-indigo">
                            <i class="bi bi-hourglass-split text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="report-title">Still Pending</h3>
                            <p class="report-description">
                                Members whose status is currently <code>pending</code>.
                                <span class="badge-count"><?= (int) ($stats['pending'] ?? 0) ?> members</span>
                            </p>
                        </div>
                        <div class="report-actions">
                            <a href="<?= base_url('admin/membership/reports/pending') ?>"
                                class="btn btn-view btn-report">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                            <a href="<?= base_url('admin/membership/reports/pending?export=csv') ?>"
                                class="btn btn-export btn-report">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Data Quality Insights -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">
                <i class="bi bi-lightbulb-fill text-warning me-2"></i>Data Quality Insights
            </h5>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="alert alert-info border-0 mb-0">
                        <h6 class="alert-heading fw-bold">
                            <i class="bi bi-info-circle-fill me-2"></i>Profile Completeness
                        </h6>
                        <p class="mb-2">
                            <?php
                            $totalFields = ($stats['total'] ?? 0) * 4; // 4 key fields: email, mobile, gender, dob
                            $missingFields = ($stats['mobile_missing'] ?? 0) + ($stats['missing_gender'] ?? 0) + ($stats['missing_dob'] ?? 0);
                            $completeness = $totalFields > 0 ? (($totalFields - $missingFields) / $totalFields) * 100 : 0;
                            ?>
                            Overall profile completeness: <strong><?= number_format($completeness, 1) ?>%</strong>
                        </p>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: <?= $completeness ?>%"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="alert alert-warning border-0 mb-0">
                        <h6 class="alert-heading fw-bold">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Action Required
                        </h6>
                        <p class="mb-0">
                            <strong><?= ($stats['email_invalid'] ?? 0) + ($stats['pending'] ?? 0) ?> members</strong>
                            need attention: invalid emails or pending verification.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    // Print optimization
    window.addEventListener('beforeprint', function () {
        document.querySelectorAll('.btn, .dropdown').forEach(el => el.style.display = 'none');
    });

    window.addEventListener('afterprint', function () {
        document.querySelectorAll('.btn, .dropdown').forEach(el => el.style.display = '');
    });
</script>

<?= $this->endSection() ?>

