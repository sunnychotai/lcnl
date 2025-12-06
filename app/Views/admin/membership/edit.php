<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h4 mb-0 text-brand d-flex align-items-center">
            <i class="bi bi-pencil-square me-2 fs-4"></i>
            Edit Member
        </h1>
        <a href="<?= base_url('admin/membership/' . $m['id']) ?>" class="btn btn-outline-brand btn-pill">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <?php if ($err = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger shadow-sm border-0 mb-4"><?= esc($err) ?></div>
    <?php endif; ?>

    <!-- Summary Banner -->
    <div class="alert bg-light border-start border-4 border-brand shadow-sm mb-4 py-3 px-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="fw-semibold text-brand fs-5">
                <?= esc($m['first_name'] . ' ' . $m['last_name']) ?>
            </div>
            <div>
                <span class="badge-lcnl-id">LCNL<?= (int)$m['id'] ?></span>
            </div>
            <div>
                <span class="badge bg-<?= $m['status'] === 'active' ? 'success' : ($m['status'] === 'pending' ? 'warning text-dark' : 'secondary') ?> px-3 py-2">
                    <?= ucfirst($m['status']) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- EDIT FORM -->
    <form method="post" action="<?= base_url('admin/membership/' . $m['id'] . '/update') ?>" class="needs-validation" novalidate>
        <?= csrf_field() ?>

        <!-- Basic Info -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-brand text-white py-2 px-3 fw-semibold">
                <i class="bi bi-person-lines-fill me-2"></i> Basic Information
            </div>
            <div class="card-body bg-white p-4">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="detail-label">First Name</label>
                        <input name="first_name" class="form-control" value="<?= esc($m['first_name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="detail-label">Last Name</label>
                        <input name="last_name" class="form-control" value="<?= esc($m['last_name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="detail-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= esc($m['email']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="detail-label">Mobile</label>
                        <input name="mobile" class="form-control" value="<?= esc($m['mobile']) ?>">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control"
                                value="<?= esc(old('date_of_birth', $m['date_of_birth'] ?? '')) ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Gender</label>
                            <?php $g = old('gender', $m['gender'] ?? ''); ?>
                            <select name="gender" class="form-select">
                                <option value="">— Select —</option>
                                <option value="male" <?= $g === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= $g === 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="other" <?= $g === 'other' ? 'selected' : '' ?>>Other</option>
                                <option value="prefer_not_to_say" <?= $g === 'prefer_not_to_say' ? 'selected' : '' ?>>Prefer not to say</option>
                            </select>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- Address -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-light fw-semibold py-2 px-3 text-brand border-bottom">
                <i class="bi bi-geo-alt-fill me-2"></i> Address
            </div>
            <div class="card-body p-4 bg-white">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="detail-label">Address Line 1</label>
                        <input name="address1" class="form-control" value="<?= esc($m['address1']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="detail-label">Address Line 2</label>
                        <input name="address2" class="form-control" value="<?= esc($m['address2']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="detail-label">City</label>
                        <input name="city" class="form-control" value="<?= esc($m['city']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="detail-label">Postcode</label>
                        <input name="postcode" class="form-control" value="<?= esc($m['postcode']) ?>">
                    </div>
                </div>

            </div>
        </div>

        <!-- Status -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-light fw-semibold py-2 px-3 text-brand border-bottom">
                <i class="bi bi-toggles2 me-2"></i> Status
            </div>
            <div class="card-body p-4 bg-white">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="detail-label">Status</label>
                        <select name="status" class="form-select">
                            <?php foreach (['pending', 'active', 'disabled'] as $s): ?>
                                <option value="<?= $s ?>" <?= $m['status'] === $s ? 'selected' : '' ?>>
                                    <?= ucfirst($s) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

            </div>
        </div>

        <!-- Actions -->
        <div class="d-flex gap-2 mt-3">
            <button class="btn btn-brand btn-pill">
                <i class="bi bi-save"></i> Save Changes
            </button>
            <a href="<?= base_url('admin/membership/' . $m['id']) ?>" class="btn btn-outline-secondary btn-pill">
                Cancel
            </a>
        </div>

    </form>

    <!-- ======================================================
         FAMILY MEMBERS SECTION
    ====================================================== -->
    <div class="card mt-4 border-0 shadow-sm rounded-4">
        <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-people me-1"></i> Family Members</span>
            <button class="btn btn-sm btn-brand" id="btnAddFamily">
                <i class="bi bi-plus-circle me-1"></i> Add Family Member
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0" id="familyTable">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Relation</th>
                            <th>Year of Birth</th>
                            <th>Age</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Notes</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($family as $f):
                            $age = (!empty($f['year_of_birth'])) ? (date('Y') - (int)$f['year_of_birth']) : null;
                        ?>
                            <tr data-id="<?= (int)$f['id'] ?>"
                                data-name="<?= esc($f['name']) ?>"
                                data-relation="<?= esc($f['relation']) ?>"
                                data-yob="<?= esc($f['year_of_birth']) ?>"
                                data-email="<?= esc($f['email']) ?>"
                                data-gender="<?= esc($f['gender']) ?>"
                                data-notes="<?= esc($f['notes']) ?>">

                                <td><?= esc($f['name']) ?></td>
                                <td><span class="relation-badge relation-<?= esc($f['relation']) ?>"><?= esc($f['relation']) ?></span></td>
                                <td><?= esc($f['year_of_birth']) ?: '-' ?></td>
                                <td class="text-muted small"><?= $age ?: '-' ?></td>
                                <td><?= esc($f['email']) ?: '-' ?></td>
                                <td><?= esc($f['gender']) ?: '-' ?></td>
                                <td class="text-muted small"><?= esc($f['notes']) ?: '-' ?></td>

                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary btn-edit-family">
                                        <i class="bi bi-pencil me-1"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-delete-family">
                                        <i class="bi bi-trash me-1"></i> Delete
                                    </button>
                                </td>

                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($family)): ?>
                            <tr id="noFamilyRow">
                                <td colspan="8" class="text-center text-muted py-4">No family members yet.</td>
                            </tr>
                        <?php endif; ?>

                    </tbody>

                </table>
            </div>
        </div>

    </div>

    <!-- Add/Edit Family Modal -->
    <div class="modal fade" id="familyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="familyForm" class="modal-content">
                <?= csrf_field() ?>
                <input type="hidden" name="family_id" id="family_id">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus me-2"></i>
                        <span id="modalTitleText">Add Family Member</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name</label>
                        <input name="name" id="fam_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Relation</label>
                        <select name="relation" id="fam_relation" class="form-select">
                            <option>Spouse</option>
                            <option>Child</option>
                            <option>Parent</option>
                            <option>Sibling</option>
                            <option>Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Year of Birth</label>
                        <input name="year_of_birth" id="fam_yob" type="number" min="1900" max="<?= date('Y') ?>" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" id="fam_email" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Gender</label>
                        <select name="gender" id="fam_gender" class="form-select">
                            <option value="">— Select —</option>
                            <option>Male</option>
                            <option>Female</option>
                            <option>Other</option>
                            <option>Prefer not to say</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <input name="notes" id="fam_notes" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand btn-pill">
                        <i class="bi bi-save me-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .relation-badge {
            font-size: .775rem;
            border-radius: 999px;
            padding: .15rem .5rem;
            display: inline-block;
        }

        .relation-Spouse {
            background: #fff1f3;
            color: #ba1b42;
        }

        .relation-Child {
            background: #eef8ff;
            color: #0a66c2;
        }

        .relation-Parent {
            background: #f5fff2;
            color: #2e7d32;
        }

        .relation-Sibling {
            background: #fff7e6;
            color: #ad6800;
        }

        .relation-Other {
            background: #f4f4f5;
            color: #444;
        }

        .toast-fixed {
            position: fixed;
            right: 16px;
            bottom: 16px;
            z-index: 1080;
            min-width: 260px;
        }
    </style>

    <!-- Toasts -->
    <div class="toast align-items-center text-white bg-success border-0 toast-fixed" id="okToast" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="okToastBody">Saved.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    <div class="toast align-items-center text-white bg-danger border-0 toast-fixed" id="errToast" style="bottom: 72px;">
        <div class="d-flex">
            <div class="toast-body" id="errToastBody">Error.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const memberId = <?= (int)$m['id'] ?>;

            let CSRF = {
                name: '<?= csrf_token() ?>',
                hash: '<?= csrf_hash() ?>'
            };

            const okToast = new bootstrap.Toast(document.getElementById('okToast'));
            const errToast = new bootstrap.Toast(document.getElementById('errToast'));

            function showOK(msg) {
                document.getElementById('okToastBody').textContent = msg;
                okToast.show();
            }

            function showERR(msg) {
                document.getElementById('errToastBody').textContent = msg;
                errToast.show();
            }

            function refreshCsrf(data) {
                if (data?.csrf) {
                    CSRF.name = data.csrf.tokenName;
                    CSRF.hash = data.csrf.tokenHash;
                }
            }

            const modalEl = document.getElementById('familyModal');
            const familyModal = new bootstrap.Modal(modalEl);

            // Open ADD modal
            document.getElementById('btnAddFamily').addEventListener('click', () => {
                document.getElementById('modalTitleText').textContent = 'Add Family Member';
                document.getElementById('family_id').value = '';
                document.getElementById('familyForm').reset();
                familyModal.show();
            });

            // Open EDIT modal
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.btn-edit-family')) return;

                const row = e.target.closest('tr');

                document.getElementById('modalTitleText').textContent = 'Edit Family Member';
                document.getElementById('family_id').value = row.dataset.id;
                document.getElementById('fam_name').value = row.dataset.name;
                document.getElementById('fam_relation').value = row.dataset.relation;
                document.getElementById('fam_yob').value = row.dataset.yob;
                document.getElementById('fam_email').value = row.dataset.email;
                document.getElementById('fam_gender').value = row.dataset.gender;
                document.getElementById('fam_notes').value = row.dataset.notes;

                familyModal.show();
            });

            // Submit ADD/EDIT form
            document.getElementById('familyForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const form = e.target;
                const familyId = document.getElementById('family_id').value;
                const isEdit = familyId !== '';

                const payload = {
                    member_id: memberId,
                    name: form.name.value.trim(),
                    relation: form.relation.value,
                    year_of_birth: form.year_of_birth.value || null,
                    email: form.email.value.trim() || null,
                    gender: form.gender.value || null,
                    notes: form.notes.value.trim() || null
                };
                if (isEdit) payload.id = familyId;

                const url = isEdit ?
                    "<?= base_url('admin/membership/family/update') ?>" :
                    "<?= base_url('admin/membership/family/add') ?>";

                fetch(url, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            [CSRF.name]: CSRF.hash
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(r => r.json())
                    .then(data => {
                        refreshCsrf(data);

                        if (!data.success) {
                            showERR(data.message || "Failed to save.");
                            return;
                        }

                        // Editing existing row
                        if (isEdit) {
                            const row = document.querySelector(`tr[data-id="${familyId}"]`);
                            const r = payload;

                            const age = r.year_of_birth ?
                                new Date().getFullYear() - parseInt(r.year_of_birth) :
                                '-';

                            row.dataset.name = r.name;
                            row.dataset.relation = r.relation;
                            row.dataset.yob = r.year_of_birth || '';
                            row.dataset.email = r.email || '';
                            row.dataset.gender = r.gender || '';
                            row.dataset.notes = r.notes || '';

                            row.innerHTML = `
                            <td>${r.name}</td>
                            <td><span class="relation-badge relation-${r.relation}">${r.relation}</span></td>
                            <td>${r.year_of_birth || '-'}</td>
                            <td class="text-muted small">${age}</td>
                            <td>${r.email || '-'}</td>
                            <td>${r.gender || '-'}</td>
                            <td class="text-muted small">${r.notes || '-'}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary btn-edit-family">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger btn-delete-family">
                                    <i class="bi bi-trash me-1"></i> Delete
                                </button>
                            </td>
                        `;

                            showOK("Family member updated.");
                        }

                        // Adding new row
                        else {
                            const r = data.row;
                            const age = r.year_of_birth ?
                                new Date().getFullYear() - parseInt(r.year_of_birth) :
                                '-';

                            const tr = `
                        <tr data-id="${data.id}"
                            data-name="${r.name || ''}"
                            data-relation="${r.relation || ''}"
                            data-yob="${r.year_of_birth || ''}"
                            data-email="${r.email || ''}"
                            data-gender="${r.gender || ''}"
                            data-notes="${r.notes || ''}">

                            <td>${r.name}</td>
                            <td><span class="relation-badge relation-${r.relation}">${r.relation}</span></td>
                            <td>${r.year_of_birth || '-'}</td>
                            <td class="text-muted small">${age}</td>
                            <td>${r.email || '-'}</td>
                            <td>${r.gender || '-'}</td>
                            <td class="text-muted small">${r.notes || '-'}</td>

                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary btn-edit-family">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger btn-delete-family">
                                    <i class="bi bi-trash me-1"></i> Delete
                                </button>
                            </td>
                        </tr>`;

                            const tbody = document.querySelector('#familyTable tbody');
                            const noFamilyRow = document.getElementById('noFamilyRow');
                            if (noFamilyRow) noFamilyRow.remove();

                            tbody.insertAdjacentHTML('beforeend', tr);

                            showOK("Family member added.");
                        }

                        familyModal.hide();
                        form.reset();
                    })
                    .catch(() => showERR("Network error."));
            });

            // DELETE family member
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.btn-delete-family')) return;

                const row = e.target.closest('tr');
                const id = row.dataset.id;

                if (!confirm("Delete this family member?")) return;

                fetch("<?= base_url('admin/membership/family/delete') ?>", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            [CSRF.name]: CSRF.hash
                        },
                        body: JSON.stringify({
                            id
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        refreshCsrf(data);

                        if (!data.success) {
                            showERR(data.message || "Failed to delete.");
                            return;
                        }

                        row.remove();

                        const tbody = document.querySelector('#familyTable tbody');
                        if (tbody.children.length === 0) {
                            tbody.innerHTML = `
                            <tr id="noFamilyRow">
                                <td colspan="8" class="text-center text-muted py-4">No family members yet.</td>
                            </tr>`;
                        }

                        showOK("Deleted.");
                    })
                    .catch(() => showERR("Network error deleting."));
            });

        });
    </script>

</div>

<?= $this->endSection() ?>