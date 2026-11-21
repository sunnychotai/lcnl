<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$statusBadges = [
    'draft'     => ['bg-light text-dark border', 'Draft'],
    'queued'    => ['bg-primary', 'Queued'],
    'cancelled' => ['bg-secondary', 'Cancelled'],
];
?>

<section class="hero-lcnl-watermark hero-overlay-ocean d-flex align-items-center justify-content-center">
    <div class="container position-relative text-center text-white py-3">
        <h1 class="fw-bold display-6 mb-2">Group emails</h1>
        <p class="lead fs-5 mb-0">Emails written once and sent to a whole group</p>
    </div>
</section>

<div class="container py-4">

    <?php if ($msg = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger shadow-sm mb-4"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc($msg) ?></div>
    <?php endif; ?>

    <p class="text-muted">
        To write a new one, open an event's registrations
        (<a href="<?= site_url('admin/content/events/event-registrations') ?>">Event registrations</a>)
        and choose <strong>Email registrants</strong>.
    </p>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Last change</th>
                            <th>Subject</th>
                            <th>To</th>
                            <th>Status</th>
                            <th class="text-end">Sent</th>
                            <th class="text-end">Waiting</th>
                            <th class="text-end">Failed</th>
                            <th>By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($groupEmails as $g): [$cls, $label] = $statusBadges[$g['status']] ?? ['bg-light text-dark', $g['status']]; $p = $progress[$g['id']]; ?>
                            <tr>
                                <td class="small text-nowrap"><?= date('j M Y, H:i', strtotime($g['queued_at'] ?: $g['updated_at'])) ?></td>
                                <td>
                                    <a href="<?= site_url('admin/group-emails/' . $g['id']) ?>"><?= esc($g['subject']) ?></a>
                                </td>
                                <td class="small"><?= esc($g['audience_label']) ?></td>
                                <td><span class="badge <?= $cls ?>"><?= esc($label) ?></span></td>
                                <td class="text-end"><?= $g['status'] === 'draft' ? '–' : $p['sent'] . ' / ' . (int) $g['recipient_count'] ?></td>
                                <td class="text-end"><?= $g['status'] === 'draft' ? '–' : $p['pending'] + $p['sending'] ?></td>
                                <td class="text-end<?= $p['failed'] ? ' text-danger fw-bold' : '' ?>"><?= $g['status'] === 'draft' ? '–' : $p['failed'] ?></td>
                                <td class="small"><?= esc($g['created_by_name'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$groupEmails): ?>
                            <tr><td colspan="8" class="text-muted">No group emails yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
