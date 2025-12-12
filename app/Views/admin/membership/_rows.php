<?php if (!empty($rows)): ?>
    <?php foreach ($rows as $r): ?>
        <?php
        $isValid = (int) ($r['is_valid_email'] ?? 0);
        $email = trim($r['email'] ?? '');
        $mobile = trim($r['mobile'] ?? '');
        ?>
        <tr>

            <!-- ID -->
            <td class="text-muted small"><?= (int) $r['id'] ?></td>

            <!-- Name -->
            <td>
                <a class="fw-semibold text-decoration-none" href="<?= base_url('admin/membership/' . $r['id']) ?>">
                    <i class="bi bi-person-circle me-1 text-secondary"></i>
                    <?= esc(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?>
                </a>
            </td>

            <!-- Email -->
            <td>
                <?php if (!empty($email)): ?>
                    <span class="email-primary">
                        <?= esc($email) ?>
                    </span>
                <?php else: ?>
                    <span class="text-muted">—</span>
                <?php endif; ?>
            </td>
            <td class="text-center">

                <?php if (!empty($email)): ?>

                    <!-- Traffic light -->
                    <span class="email-dot <?= $isValid ? 'email-dot-valid' : 'email-dot-invalid' ?>"
                        title="<?= $isValid ? 'Email valid' : 'Email invalid' ?>">
                    </span>

                    <!-- Toggle -->
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-secondary ms-2 js-toggle-email-validity"
                        title="<?= $isValid ? 'Mark email invalid' : 'Mark email valid' ?>"
                        data-id="<?= (int) $r['id'] ?>"
                        data-email="<?= esc($email) ?>">

                        <i class="bi <?= $isValid ? 'bi-envelope-x' : 'bi-envelope-check' ?>"></i>
                    </button>


                <?php else: ?>
                    <span class="text-muted">—</span>
                <?php endif; ?>

            </td>



            <!-- Mobile -->
            <td class="text-muted small">
                <?= ($mobile && $mobile !== '0') ? esc($mobile) : '—' ?>
            </td>

            <!-- City -->
            <td class="text-muted small">
                <?= esc($r['city'] ?? '—') ?>
            </td>

            <!-- Status -->
            <td>
                <span class="badge
                    <?= $r['status'] === 'active'
                        ? 'bg-success'
                        : ($r['status'] === 'pending'
                            ? 'bg-warning text-dark'
                            : 'bg-secondary') ?>">
                    <?= ucfirst($r['status']) ?>
                </span>
            </td>

            <!-- Created -->
            <td class="text-muted small">
                <?= esc($r['created_at'] ?? '') ?>
            </td>

            <!-- Actions -->
            <td class="text-end">
                <div class="lcnl-action-group d-inline-flex">

                    <a href="<?= base_url('admin/membership/' . $r['id']) ?>" class="btn-action" title="View">
                        <i class="bi bi-eye"></i>
                    </a>

                    <a href="<?= base_url('admin/membership/' . $r['id'] . '/edit') ?>" class="btn-action" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>

                    <?php if ($r['status'] === 'active'): ?>
                        <form method="post" action="<?= base_url('admin/membership/' . $r['id'] . '/disable') ?>" class="d-inline">
                            <?= csrf_field() ?>
                            <button class="btn-action btn-action-danger" title="Disable">
                                <i class="bi bi-slash-circle"></i>
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="post" action="<?= base_url('admin/membership/' . $r['id'] . '/activate') ?>" class="d-inline">
                            <?= csrf_field() ?>
                            <button class="btn-action btn-action-filled" title="Activate">
                                <i class="bi bi-check2"></i>
                            </button>
                        </form>
                    <?php endif; ?>

                </div>
            </td>

        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="8" class="text-center text-muted py-4">
            No members found.
        </td>
    </tr>
<?php endif; ?>