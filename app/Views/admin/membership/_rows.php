<?php if (!empty($rows)): ?>
    <?php foreach ($rows as $r): ?>
        <tr>
            <td class="text-muted small"><?= (int)$r['id'] ?></td>
            <td>
                <a class="fw-semibold text-decoration-none" href="<?= base_url('admin/membership/' . $r['id']) ?>">
                    <i class="bi bi-person-circle me-1 text-secondary"></i>
                    <?= esc(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?>
                </a>
            </td>
            <td>
                <div><?= esc($r['email']) ?></div>
                <div class="text-muted small"><?= esc($r['mobile'] ?? '-') ?></div>
            </td>
            <td class="text-muted small"><?= esc($r['city'] ?? '-') ?></td>
            <td>
                <span class="badge bg-<?=
                                        $r['status'] === 'active' ? 'success' : ($r['status'] === 'pending' ? 'warning text-dark' : 'secondary')
                                        ?>"><?= ucfirst($r['status']) ?></span>
            </td>
            <td class="text-muted small"><?= esc($r['created_at'] ?? '') ?></td>

            <td class="text-end">
                <div class="lcnl-action-group d-inline-flex">

                    <!-- View -->
                    <a href="<?= base_url('admin/membership/' . $r['id']) ?>" class="btn-action" title="View">
                        <i class="bi bi-eye"></i>
                    </a>

                    <!-- Edit -->
                    <a href="<?= base_url('admin/membership/' . $r['id'] . '/edit') ?>" class="btn-action" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>

                    <!-- Status Action -->
                    <?php if ($r['status'] === 'active'): ?>
                        <!-- show disable only -->
                        <form method="post" action="<?= base_url('admin/membership/' . $r['id'] . '/disable') ?>" class="d-inline">
                            <?= csrf_field() ?>
                            <button class="btn-action btn-action-danger" title="Disable">
                                <i class="bi bi-slash-circle"></i>
                            </button>
                        </form>
                    <?php else: ?>
                        <!-- show activate only -->
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
        <td colspan="7" class="text-center text-muted py-4">No members found.</td>
    </tr>
<?php endif; ?>