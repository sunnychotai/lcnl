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

    <?php
    // Parse the JSON summary
    $summary = json_decode($log['summary'], true) ?? [];
    $hasData = !empty($summary);
    ?>

    <div class="row g-3">
        <!-- Left Column: Summary -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold text-brand mb-3">Job Summary</h5>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Job</dt>
                        <dd class="col-sm-8"><?= esc($log['job_name']) ?></dd>

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">
                            <?php
                            $badge = 'secondary';
                            if ($log['status'] === 'success') $badge = 'success';
                            elseif ($log['status'] === 'partial') $badge = 'warning';
                            elseif ($log['status'] === 'error') $badge = 'danger';
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

            <?php if ($hasData && isset($summary['test_mode'])): ?>
                <!-- Test Mode Indicator -->
                <?php if ($summary['test_mode']): ?>
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Test Mode:</strong> Emails sent to <?= esc($summary['test_email'] ?? 'test address') ?>
                    </div>
                <?php endif; ?>

                <?php if ($summary['paused']): ?>
                    <div class="alert alert-warning mt-3">
                        <i class="bi bi-pause-circle me-2"></i>
                        <strong>Auto-Paused:</strong> Too many consecutive failures
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Right Column: Email Stats -->
        <div class="col-lg-6">
            <?php if ($hasData && isset($summary['processed'])): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold text-brand mb-3">Email Processing</h5>
                        <div class="row g-3">
                            <div class="col-4 text-center">
                                <div class="fs-2 fw-bold text-primary"><?= (int)$summary['processed'] ?></div>
                                <div class="text-muted small">Processed</div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="fs-2 fw-bold text-success"><?= (int)$summary['sent'] ?></div>
                                <div class="text-muted small">Sent</div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="fs-2 fw-bold text-danger"><?= (int)$summary['failed'] ?></div>
                                <div class="text-muted small">Failed</div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($hasData && !empty($summary['limit_info'])): ?>
        <!-- Limit Information -->
        <div class="row g-3 mt-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold text-brand mb-3">Rate Limits</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted small mb-2">10 Minute Window</h6>
                                <div class="progress mb-2" style="height: 25px;">
                                    <?php
                                    $sent10 = (int)($summary['limit_info']['sent_last_10_min'] ?? 0);
                                    $remaining10 = (int)($summary['limit_info']['remaining_10_min'] ?? 0);
                                    $total10 = $sent10 + $remaining10;
                                    $percent10 = $total10 > 0 ? ($sent10 / $total10) * 100 : 0;
                                    ?>
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: <?= number_format($percent10, 1) ?>%">
                                        <?= $sent10 ?> / <?= $total10 ?>
                                    </div>
                                </div>
                                <small class="text-muted">Remaining: <?= $remaining10 ?></small>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted small mb-2">24 Hour Window</h6>
                                <div class="progress mb-2" style="height: 25px;">
                                    <?php
                                    $sent24 = (int)($summary['limit_info']['sent_last_24h'] ?? 0);
                                    $remaining24 = (int)($summary['limit_info']['remaining_24h'] ?? 0);
                                    $total24 = $sent24 + $remaining24;
                                    $percent24 = $total24 > 0 ? ($sent24 / $total24) * 100 : 0;
                                    ?>
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: <?= number_format($percent24, 1) ?>%">
                                        <?= $sent24 ?> / <?= $total24 ?>
                                    </div>
                                </div>
                                <small class="text-muted">Remaining: <?= $remaining24 ?></small>
                            </div>
                        </div>
                        <hr>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="text-muted small">Batch Requested</div>
                                <div class="fw-bold"><?= (int)($summary['limit_info']['batch_requested'] ?? 0) ?></div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">Per-Run Cap</div>
                                <div class="fw-bold"><?= (int)($summary['limit_info']['hard_per_run_cap'] ?? 0) ?></div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">Effective Take</div>
                                <div class="fw-bold text-primary"><?= (int)($summary['limit_info']['take_effective'] ?? 0) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($hasData && !empty($summary['backoff'])): ?>
        <!-- Backoff Information -->
        <div class="row g-3 mt-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm border-warning">
                    <div class="card-body">
                        <h5 class="fw-bold text-warning mb-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>Backoff Events
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Row ID</th>
                                        <th>Error</th>
                                        <th>Delay (ms)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($summary['backoff'] as $backoff): ?>
                                        <tr>
                                            <td><?= (int)$backoff['row_id'] ?></td>
                                            <td><code class="small"><?= esc($backoff['error']) ?></code></td>
                                            <td><?= (int)$backoff['delay_ms_now'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($hasData && !empty($summary['errors'])): ?>
        <!-- Errors -->
        <div class="row g-3 mt-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm border-danger">
                    <div class="card-body">
                        <h5 class="fw-bold text-danger mb-3">
                            <i class="bi bi-x-circle me-2"></i>Errors
                        </h5>
                        <ul class="mb-0">
                            <?php foreach ($summary['errors'] as $error): ?>
                                <li><code><?= esc($error) ?></code></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Raw JSON (Collapsible) -->
    <div class="row g-3 mt-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold text-brand mb-3">
                        <a data-bs-toggle="collapse" href="#rawJson" class="text-decoration-none">
                            <i class="bi bi-code-square me-2"></i>Raw JSON
                            <i class="bi bi-chevron-down ms-2"></i>
                        </a>
                    </h5>
                    <div class="collapse" id="rawJson">
                        <pre class="bg-light p-3 rounded mb-0"
                            style="white-space:pre-wrap;word-wrap:break-word;"><?= esc($log['summary']) ?></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>