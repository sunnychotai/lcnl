<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">
            <i class="bi bi-envelope-open me-2"></i>Email #<?= (int)$email['id'] ?>
        </h2>
        <a href="<?= base_url('admin/system/emails') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Queue
        </a>
    </div>

    <div class="row g-3">
        <!-- Left Column: Metadata -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold text-brand mb-3">Email Details</h5>

                    <dl class="row mb-0">
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">
                            <?php
                            $badgeClass = match ($email['status']) {
                                'pending' => 'bg-warning',
                                'sending' => 'bg-info',
                                'sent' => 'bg-success',
                                'failed' => 'bg-danger',
                                'invalid' => 'bg-dark',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= ucfirst(esc($email['status'])) ?></span>
                        </dd>

                        <dt class="col-sm-4">Priority</dt>
                        <dd class="col-sm-8">
                            <?php
                            $priorityLabel = match ((int)$email['priority']) {
                                1 => '<span class="badge bg-danger">P1 - Critical</span>',
                                2 => '<span class="badge bg-warning text-dark">P2 - High</span>',
                                3 => '<span class="badge bg-primary">P3 - Normal</span>',
                                4 => '<span class="badge bg-secondary">P4 - Low</span>',
                                5 => '<span class="badge bg-light text-dark">P5 - Bulk</span>',
                                default => '<span class="badge bg-dark">Unknown</span>'
                            };
                            echo $priorityLabel;
                            ?>
                        </dd>

                        <dt class="col-sm-4">Attempts</dt>
                        <dd class="col-sm-8"><?= (int)$email['attempts'] ?></dd>

                        <dt class="col-sm-4">Created</dt>
                        <dd class="col-sm-8">
                            <small><?= esc($email['created_at'] ?? '–') ?></small>
                        </dd>

                        <dt class="col-sm-4">Scheduled</dt>
                        <dd class="col-sm-8">
                            <small><?= esc($email['scheduled_at'] ?? '–') ?></small>
                        </dd>

                        <dt class="col-sm-4">Sent At</dt>
                        <dd class="col-sm-8">
                            <small><?= esc($email['sent_at'] ?? '–') ?></small>
                        </dd>

                        <?php if (!empty($email['last_error'])): ?>
                            <dt class="col-sm-4 text-danger">Last Error</dt>
                            <dd class="col-sm-8">
                                <small class="text-danger">
                                    <?= esc(substr($email['last_error'], 0, 200)) ?>
                                    <?= strlen($email['last_error']) > 200 ? '...' : '' ?>
                                </small>
                            </dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>

            <!-- Recipient Info -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h5 class="fw-bold text-brand mb-3">Recipient</h5>
                    <p class="mb-1">
                        <strong><?= esc($email['to_name'] ?: 'No name') ?></strong>
                    </p>
                    <p class="text-muted mb-0">
                        <i class="bi bi-envelope me-1"></i>
                        <a href="mailto:<?= esc($email['to_email']) ?>"><?= esc($email['to_email']) ?></a>
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <?php if ($email['status'] === 'failed' || $email['status'] === 'invalid'): ?>
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-body">
                        <h5 class="fw-bold text-brand mb-3">Actions</h5>
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('admin/system/emails/retry/' . $email['id']) ?>"
                                class="btn btn-warning">
                                <i class="bi bi-arrow-repeat"></i> Retry Send
                            </a>
                            <a href="<?= base_url('admin/system/emails/delete/' . $email['id']) ?>"
                                class="btn btn-outline-danger"
                                onclick="return confirm('Delete this email?')">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column: Content -->
        <div class="col-lg-8">
            <!-- Subject -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold text-brand mb-2">Subject</h5>
                    <p class="mb-0"><?= esc($email['subject']) ?></p>
                </div>
            </div>

            <!-- HTML Preview -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="html-tab" data-bs-toggle="tab"
                                data-bs-target="#html-preview" type="button" role="tab">
                                <i class="bi bi-code-square me-1"></i> HTML Preview
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="text-tab" data-bs-toggle="tab"
                                data-bs-target="#text-preview" type="button" role="tab">
                                <i class="bi bi-file-text me-1"></i> Plain Text
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="source-tab" data-bs-toggle="tab"
                                data-bs-target="#source-preview" type="button" role="tab">
                                <i class="bi bi-filetype-html me-1"></i> HTML Source
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- HTML Preview in iframe -->
                        <div class="tab-pane fade show active" id="html-preview" role="tabpanel">
                            <iframe
                                srcdoc="<?= esc($email['body_html'], 'attr') ?>"
                                style="width: 100%; height: 600px; border: 1px solid #dee2e6; border-radius: 4px;"
                                sandbox="allow-same-origin"></iframe>
                        </div>

                        <!-- Plain Text -->
                        <div class="tab-pane fade" id="text-preview" role="tabpanel">
                            <pre class="bg-light p-3 rounded" style="max-height: 600px; overflow-y: auto; white-space: pre-wrap;"><?= esc($email['body_text'] ?? 'No plain text version available.') ?></pre>
                        </div>

                        <!-- HTML Source -->
                        <div class="tab-pane fade" id="source-preview" role="tabpanel">
                            <pre class="bg-dark text-light p-3 rounded" style="max-height: 600px; overflow-y: auto;"><code><?= esc($email['body_html']) ?></code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>