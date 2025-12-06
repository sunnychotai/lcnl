<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero -->
<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
    <div class="container position-relative text-center text-white py-4">
        <h1 class="fw-bold display-6 mb-1"><i class="bi bi-people-fill me-2"></i> My Family</h1>
        <p class="mb-0 opacity-75">Manage family members linked to your account.</p>
    </div>
</section>

<div class="container py-4">

    <!-- Flash -->
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
            <ul class="mb-0 mt-2"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="page-title fw-bold text-brand mb-0"><i class="bi bi-people me-2"></i>Family Members</h2>
        <button class="btn btn-brand rounded-pill" data-bs-toggle="modal" data-bs-target="#addFamilyModal">
            <i class="bi bi-plus-circle me-1"></i> Add Family Member
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Name</th>
                            <th>Relation</th>
                            <th>Year of Birth</th>
                            <th>Gender</th>
                            <th>Email</th> <!-- NEW -->
                            <th>Notes</th>
                            <th style="width:130px;"></th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (empty($family)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No family members added yet.</td>
                            </tr>

                            <?php else: foreach ($family as $f): ?>
                                <tr>
                                    <td class="fw-semibold"><?= esc($f['name']) ?></td>
                                    <td><?= esc($f['relation']) ?></td>
                                    <td><?= esc($f['year_of_birth'] ?? '-') ?></td>
                                    <td><?= esc($f['gender'] ?? '-') ?></td>
                                    <td><?= esc($f['email'] ?? '-') ?></td> <!-- NEW -->
                                    <td><?= esc($f['notes'] ?? '-') ?></td>

                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary me-1"
                                            data-bs-toggle="modal" data-bs-target="#editFamilyModal"
                                            data-id="<?= (int)$f['id'] ?>"
                                            data-name="<?= esc($f['name']) ?>"
                                            data-relation="<?= esc($f['relation']) ?>"
                                            data-year="<?= esc($f['year_of_birth']) ?>"
                                            data-gender="<?= esc($f['gender']) ?>"
                                            data-email="<?= esc($f['email']) ?>"
                                            data-notes="<?= esc($f['notes']) ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <form action="<?= route_to('account.family.delete', $f['id']) ?>" method="post"
                                            class="d-inline" onsubmit="return confirm('Remove this family member?');">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                        <?php endforeach;
                        endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Add Modal -->
<div class="modal fade" id="addFamilyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= route_to('account.family.create') ?>" method="post" class="modal-content">
            <?= csrf_field() ?>

            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Add Family Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Name</label>
                    <input type="text" name="name" class="form-control" required maxlength="120">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Relation</label>
                    <select name="relation" class="form-select" required>
                        <option value="" disabled selected>Choose...</option>
                        <?php foreach ($relations as $r): ?>
                            <option value="<?= esc($r) ?>"><?= esc($r) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

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
                                <option value="<?= esc($g) ?>"><?= ucwords(str_replace('_', ' ', $g)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- NEW EMAIL FIELD -->
                <div class="mt-3">
                    <label class="form-label fw-semibold">Email (optional)</label>
                    <input type="email" name="email" class="form-control" maxlength="120" placeholder="name@example.com">
                </div>

                <div class="mt-3">
                    <label class="form-label fw-semibold">Notes (optional)</label>
                    <input type="text" name="notes" class="form-control" maxlength="255" placeholder="Any useful notes">
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-accent rounded-pill" type="submit">
                    <i class="bi bi-check2-circle me-1"></i>Save
                </button>
            </div>

        </form>
    </div>
</div>


<!-- Edit Modal -->
<div class="modal fade" id="editFamilyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editFamilyForm" action="#" method="post" class="modal-content">
            <?= csrf_field() ?>

            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Family Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Name</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required maxlength="120">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Relation</label>
                    <select name="relation" id="edit_relation" class="form-select" required>
                        <?php foreach ($relations as $r): ?>
                            <option value="<?= esc($r) ?>"><?= esc($r) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Year of Birth</label>
                        <select name="year_of_birth" id="edit_year" class="form-select">
                            <option value="">—</option>
                            <?php foreach ($years as $y): ?>
                                <option value="<?= $y ?>"><?= $y ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Gender</label>
                        <select name="gender" id="edit_gender" class="form-select">
                            <option value="">—</option>
                            <?php foreach ($genders as $g): ?>
                                <option value="<?= esc($g) ?>"><?= ucwords(str_replace('_', ' ', $g)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- NEW EMAIL FIELD -->
                <div class="mt-3">
                    <label class="form-label fw-semibold">Email (optional)</label>
                    <input type="email" name="email" id="edit_email" class="form-control" maxlength="120">
                </div>

                <div class="mt-3">
                    <label class="form-label fw-semibold">Notes (optional)</label>
                    <input type="text" name="notes" id="edit_notes" class="form-control" maxlength="255">
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-accent rounded-pill" type="submit">
                    <i class="bi bi-check2-circle me-1"></i>Update
                </button>
            </div>

        </form>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editModal = document.getElementById('editFamilyModal');
        const form = document.getElementById('editFamilyForm');

        editModal.addEventListener('show.bs.modal', function(event) {
            const btn = event.relatedTarget;

            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const relation = btn.getAttribute('data-relation');
            const year = btn.getAttribute('data-year');
            const gender = btn.getAttribute('data-gender');
            const email = btn.getAttribute('data-email'); // NEW
            const notes = btn.getAttribute('data-notes');

            form.action = "<?= base_url(route_to('account.family.update', 0)) ?>".replace(/0$/, id);

            document.getElementById('edit_name').value = name;
            document.getElementById('edit_relation').value = relation;
            document.getElementById('edit_year').value = year;
            document.getElementById('edit_gender').value = gender;
            document.getElementById('edit_email').value = email || ''; // NEW
            document.getElementById('edit_notes').value = notes || '';
        });
    });
</script>

<?= $this->endSection() ?>