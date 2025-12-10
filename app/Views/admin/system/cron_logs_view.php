<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">
            <i class="bi bi-eye me-2"></i> Cron Log #<?= (int) $log['id'] ?>
        </h2>
        <a href="<?= base_url('admin/system/cron-logs') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold text-brand mb-3">Summary</h5>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Job</dt>
                        <dd class="col-sm-8"><?= esc($log['job_name']) ?></dd>

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">
                            <?php
                            $badge = 'secondary';
                            if ($log['status'] === 'success')
                                $badge = 'success';
                            elseif ($log['status'] === 'partial')
                                $badge = 'warning';
                            elseif ($log['status'] === 'error')
                                $badge = 'danger';
                            ?>
                            <span class="badge bg-<?= $badge ?>"><?= ucfirst($log['status']) ?></span>
                        </dd>

                        <dt class="col-sm-4">Started</dt>
                        <dd class="col-sm-8"><?= esc($log['started_at']) ?></dd>

                        <dt class="col-sm-4">Finished</dt>
                        <dd class="col-sm-8"><?= esc($log['finished_at']) ?></dd>

                        <dt class="col-sm-4">Duration</dt>
                        <dd class="col-sm-8">
                            <?php
                            $start = strtotime($log['started_at']);
                            $end = strtotime($log['finished_at']);
                            echo ($end && $start) ? max(0, $end - $start) . 's' : '-';
                            ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold text-brand mb-3">Raw JSON</h5>
                    <pre class="bg-light p-3 rounded"
                        style="white-space:pre-wrap;word-wrap:break-word;"><?= esc($log['summary']) ?></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

