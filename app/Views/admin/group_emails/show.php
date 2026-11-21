<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$rowBadges = [
    'pending' => ['bg-warning text-dark', 'Waiting'],
    'sending' => ['bg-info text-dark', 'Sending'],
    'sent'    => ['bg-success', 'Sent'],
    'failed'  => ['bg-danger', 'Failed'],
];
$total = (int) $groupEmail['recipient_count'];
$pct   = $total ? (int) round($progress['sent'] / $total * 100) : 0;
?>

<section class="hero-lcnl-watermark hero-overlay-ocean d-flex align-items-center justify-content-center">
    <div class="container position-relative text-center text-white py-3">
        <h1 class="fw-bold display-6 mb-2"><?= esc($groupEmail['subject']) ?></h1>
        <p class="lead fs-5 mb-0"><?= esc($groupEmail['audience_label']) ?></p>
    </div>
</section>

<div class="container py-4">

    <?php foreach (['success' => 'check-circle-fill', 'error' => 'exclamation-triangle-fill'] as $type => $icon): ?>
        <?php if ($msg = session()->getFlashdata($type)): ?>
            <div class="alert alert-<?= $type === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show shadow-sm mb-4">
                <i class="bi bi-<?= $icon ?> me-2"></i><?= esc($msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="d-flex gap-2 flex-wrap">
            <?php if ($backUrl): ?>
                <a href="<?= esc($backUrl, 'attr') ?>" class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i>Back to registrations
                </a>
            <?php endif; ?>
            <a href="<?= site_url('admin/group-emails') ?>" class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-envelope-paper me-1"></i>All group emails
            </a>
        </div>
        <?php if ($progress['pending'] > 0): ?>
            <form method="post" action="<?= site_url('admin/group-emails/' . $groupEmail['id'] . '/cancel') ?>"
                data-confirm="Take the <?= $progress['pending'] ?> unsent email<?= $progress['pending'] === 1 ? '' : 's' ?> out of the queue? Ones already sent can't be recalled.">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline-danger rounded-pill">
                    <i class="bi bi-x-circle me-1"></i>Cancel unsent (<?= $progress['pending'] ?>)
                </button>
            </form>
        <?php endif; ?>
    </div>

    <div class="row g-3 mb-3">
        <?php foreach ([
            ['Recipients', $total, 'text-brand'],
            ['Sent', $progress['sent'], 'text-success'],
            ['Waiting', $progress['pending'] + $progress['sending'], 'text-warning'],
            ['Failed', $progress['failed'], 'text-danger'],
            ['Cancelled', (int) $groupEmail['cancelled_count'], 'text-secondary'],
        ] as [$label, $n, $cls]): ?>
            <div class="col-6 col-md">
                <div class="card border-0 shadow-sm text-center py-3 h-100">
                    <div class="fs-2 fw-bold <?= $cls ?>"><?= (int) $n ?></div>
                    <div class="small text-muted"><?= $label ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="progress mb-4" role="progressbar" aria-label="Sent" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100" style="height: 8px;">
        <div class="progress-bar bg-success" style="width: <?= $pct ?>%"></div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body small">
            <div class="row g-2">
                <div class="col-md-4"><span class="text-muted">Queued:</span> <?= $groupEmail['queued_at'] ? date('j M Y, H:i', strtotime($groupEmail['queued_at'])) : '–' ?></div>
                <div class="col-md-4"><span class="text-muted">By:</span> <?= esc($groupEmail['created_by_name'] ?: '–') ?></div>
                <div class="col-md-4"><span class="text-muted">Replies to:</span> <?= esc($groupEmail['reply_to'] ?: '–') ?></div>
                <div class="col-md-4"><span class="text-muted">Tests sent:</span> <?= (int) $groupEmail['test_count'] ?></div>
                <div class="col-md-4"><span class="text-muted">Left out:</span> <?= (int) $groupEmail['skipped_count'] ?> (repeat or invalid)</div>
                <?php if ($groupEmail['cancelled_at']): ?>
                    <div class="col-md-4"><span class="text-muted">Cancelled:</span> <?= date('j M Y, H:i', strtotime($groupEmail['cancelled_at'])) ?></div>
                <?php endif; ?>
            </div>
            <?php if ($progress['failed'] > 0): ?>
                <p class="mt-3 mb-0 text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    Failed emails can be retried one by one from the <a href="<?= site_url('admin/system/emails') ?>">email queue</a>,
                    or all at once with <code>php spark emails:requeue</code>.
                </p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="rowsTable">
                    <thead class="table-light">
                        <tr><th>Name</th><th>Email</th><th>Status</th><th>Sent</th><th>Error</th><th class="text-end"></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $r): [$cls, $label] = $rowBadges[$r['status']] ?? ['bg-light text-dark', $r['status']]; ?>
                            <tr>
                                <td><?= esc($r['to_name'] ?? '') ?></td>
                                <td><?= esc($r['to_email']) ?></td>
                                <td><span class="badge <?= $cls ?>"><?= esc($label) ?></span></td>
                                <td class="small"><?= $r['sent_at'] ? date('j M, H:i', strtotime($r['sent_at'])) : '' ?></td>
                                <td class="small text-danger"><?= esc($r['last_error'] ?? '') ?></td>
                                <td class="text-end">
                                    <a href="<?= site_url('admin/system/emails/view/' . $r['id']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$rows): ?>
                            <tr><td colspan="6" class="text-muted">No copies left in the queue.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
            e.preventDefault();
        }
    });
</script>

<?= $this->endSection() ?>
