<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
use Config\Family as FamilyConfig;
$familyConfig = new FamilyConfig();
$relations = $familyConfig->relations;
$genders = $familyConfig->genders;
?>

<!-- HERO -->
<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
    <div class="container position-relative text-center text-white py-4">
        <h1 class="fw-bold display-6 mb-1">
            <i class="bi bi-people-fill me-2"></i> My Family
        </h1>
        <p class="mb-0 opacity-75">Manage family members linked to your account.</p>
    </div>
</section>

<div class="container py-4">

    <!-- FLASH MESSAGES -->
    <?php if ($msg = session()->getFlashdata('message')): ?>
        <div class="alert alert-success shadow-sm alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i><?= esc($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($err = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger shadow-sm alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i><?= esc($err) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($errors = session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger shadow-sm alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Please fix the following issues:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($errors as $e): ?>
                    <li><?= esc($e) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>


    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="page-title fw-bold text-brand mb-0">
            <i class="bi bi-people me-2"></i>Family Members
        </h2>
        <button class="btn btn-brand rounded-pill" data-bs-toggle="modal" data-bs-target="#addFamilyModal">
            <i class="bi bi-plus-circle me-1"></i> Add Family Member
        </button>
    </div>


    <!-- FAMILY TABLE -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Relation</th>
                            <th>Year</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Notes</th>
                            <th style="width:130px;"></th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (empty($family)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-people fs-4 d-block mb-2"></i>
                                    No family members added yet.
                                </td>
                            </tr>

                        <?php else: ?>
                            <?php foreach ($family as $f): ?>

                                <?php
                                $key = strtolower($f['relation']);
                                $meta = $relations[$key] ?? null;
                                $label = $meta['label'] ?? ucfirst($key);
                                $icon = $meta['icon'] ?? 'bi-person';

                                $age = $f['year_of_birth']
                                    ? (date('Y') - (int) $f['year_of_birth'])
                                    : null;
                                ?>

                                <tr>
                                    <td class="fw-semibold"><?= esc($f['name']) ?></td>

                                    <!-- RELATION BADGE -->
                                    <td>
                                        <span class="relation-badge relation-<?= esc($key) ?>">
                                            <i class="bi <?= esc($icon) ?> me-1"></i>
                                            <?= esc($label) ?>
                                        </span>
                                    </td>

                                    <td><?= esc($f['year_of_birth'] ?: '-') ?></td>
                                    <td class="text-muted small"><?= $age ?: '-' ?></td>
                                    <td><?= esc($f['gender'] ?: '-') ?></td>
                                    <td><?= esc($f['email'] ?: '-') ?></td>
                                    <td class="text-muted small"><?= esc($f['notes'] ?: '-') ?></td>

                                    <td class="text-end">

                                        <!-- EDIT -->
                                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#editFamilyModal" data-id="<?= (int) $f['id'] ?>"
                                            data-name="<?= esc($f['name']) ?>" data-relation="<?= esc($key) ?>"
                                            data-year="<?= esc($f['year_of_birth']) ?>" data-gender="<?= esc($f['gender']) ?>"
                                            data-email="<?= esc($f['email']) ?>" data-notes="<?= esc($f['notes']) ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <!-- DELETE -->
                                        <form action="<?= route_to('account.family.delete', $f['id']) ?>" method="post"
                                            class="d-inline" onsubmit="return confirm('Remove this family member?');">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>

                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>



<!-- ADD MODAL -->
<div class="modal fade" id="addFamilyModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= route_to('account.family.create') ?>" method="post" class="modal-content">
            <?= csrf_field() ?>

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>Add Family Member
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- Name -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Name</label>
                    <input name="name" class="form-control" required maxlength="120">
                </div>

                <!-- Relation -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Relation</label>
                    <select name="relation" class="form-select" required>
                        <option value="" disabled selected>Select…</option>

                        <?php foreach ($relations as $key => $meta): ?>
                            <option value="<?= esc($key) ?>">
                                <?= esc($meta['label']) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <!-- Year + Gender -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Year of Birth</label>
                        <select name="year_of_birth" class="form-select">
                            <option value="">—</option>
                            <?php foreach ($years as $y): ?>
                                <option value="<?= $y ?>"><?= $y ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">—</option>
                            <?php foreach ($genders as $g): ?>
                                <option value="<?= esc($g) ?>">
                                    <?= ucwords(str_replace('_', ' ', $g)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Email -->
                <div class="mt-3">
                    <label class="form-label fw-semibold">Email (optional)</label>
                    <input name="email" class="form-control" maxlength="120">
                </div>

                <!-- Notes -->
                <div class="mt-3">
                    <label class="form-label fw-semibold">Notes (optional)</label>
                    <input name="notes" class="form-control" maxlength="255">
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-accent rounded-pill">
                    <i class="bi bi-check2-circle me-1"></i>Save
                </button>
            </div>

        </form>
    </div>
</div>



<!-- EDIT MODAL -->
<div class="modal fade" id="editFamilyModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editFamilyForm" method="post" class="modal-content">
            <?= csrf_field() ?>

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>Edit Family Member
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" name="id" id="edit_id">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Name</label>
                    <input id="edit_name" name="name" class="form-control" required maxlength="120">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Relation</label>
                    <select id="edit_relation" name="relation" class="form-select" required>

                        <?php foreach ($relations as $key => $meta): ?>
                            <option value="<?= esc($key) ?>">
                                <?= esc($meta['label']) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Year of Birth</label>
                        <select id="edit_year" name="year_of_birth" class="form-select">
                            <option value="">—</option>
                            <?php foreach ($years as $y): ?>
                                <option value="<?= $y ?>"><?= $y ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Gender</label>
                        <select id="edit_gender" name="gender" class="form-select">
                            <option value="">—</option>
                            <?php foreach ($genders as $g): ?>
                                <option value="<?= esc($g) ?>"><?= ucwords(str_replace('_', ' ', $g)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input id="edit_email" name="email" class="form-control" maxlength="120">
                </div>

                <div class="mt-3">
                    <label class="form-label fw-semibold">Notes</label>
                    <input id="edit_notes" name="notes" class="form-control" maxlength="255">
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-accent rounded-pill">
                    <i class="bi bi-check2-circle me-1"></i>Update
                </button>
            </div>

        </form>
    </div>
</div>



<!-- JS: Populate Edit Modal -->
<script>
    document.addEventListener('DOMContentLoaded', () => {

        const editModal = document.getElementById('editFamilyModal');
        const form = document.getElementById('editFamilyForm');

        editModal.addEventListener('show.bs.modal', (e) => {
            const btn = e.relatedTarget;

            const id = btn.dataset.id;
            const name = btn.dataset.name;
            const relation = btn.dataset.relation;
            const year = btn.dataset.year;
            const gender = btn.dataset.gender;
            const email = btn.dataset.email;
            const notes = btn.dataset.notes;

            form.action = "<?= base_url(route_to('account.family.update', 0)) ?>".replace(/0$/, id);

            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_relation').value = relation;
            document.getElementById('edit_year').value = year;
            document.getElementById('edit_gender').value = gender;
            document.getElementById('edit_email').value = email || '';
            document.getElementById('edit_notes').value = notes || '';
        });

    });
</script>



<!-- RELATION BADGE STYLING -->
<style>
    .relation-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .25rem .65rem;
        border-radius: 50px;
        font-size: .78rem;
        font-weight: 600;
    }

    .relation-spouse {
        background: #fff1f3;
        color: #b71d44;
    }

    .relation-son,
    .relation-daughter {
        background: #eef7ff;
        color: #0a66c2;
    }

    .relation-mother,
    .relation-father {
        background: #f2fff4;
        color: #2a7b2e;
    }

    .relation-grandparent {
        background: #fff7e6;
        color: #ad6800;
    }

    .relation-sibling {
        background: #f6f0ff;
        color: #6f42c1;
    }

    .relation-other {
        background: #f1f3f5;
        color: #555;
    }
</style>

<?= $this->endSection() ?>

