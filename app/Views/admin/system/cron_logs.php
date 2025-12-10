<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">
            <i class="bi bi-clock-history me-2"></i> Cron Logs
        </h2>
    </div>


    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Job</th>
                        <th>Status</th>
                        <th>Started</th>
                        <th>Finished</th>
                        <th>Duration</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $row):
                        $start = strtotime($row['started_at']);
                        $end = strtotime($row['finished_at']);
                        $dur = $end && $start ? max(0, $end - $start) : 0;
                        $badge = 'secondary';
                        if ($row['status'] === 'success')
                            $badge = 'success';
                        elseif ($row['status'] === 'partial')
                            $badge = 'warning';
                        elseif ($row['status'] === 'error')
                            $badge = 'danger';
                        ?>
                        <tr>
                            <td><?= (int) $row['id'] ?></td>
                            <td class="fw-semibold"><?= esc($row['job_name']) ?></td>
                            <td>
                                <span class="badge bg-<?= $badge ?>"><?= ucfirst($row['status']) ?></span>
                            </td>
                            <td><?= esc($row['started_at']) ?></td>
                            <td><?= esc($row['finished_at']) ?></td>
                            <td><?= $dur ?>s</td>
                            <td class="text-end">
                                <a href="<?= base_url('admin/system/cron-logs/' . $row['id']) ?>"
                                    class="btn btn-sm btn-outline-brand">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No logs yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card-body">
            <?= $pager->links('default', 'default_full') ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

