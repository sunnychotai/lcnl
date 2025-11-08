<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0 text-brand d-flex align-items-center">
            <i class="bi bi-pencil-square me-2"></i>
            Edit Member #<?= (int)$m['id'] ?>
        </h1>
        <a href="<?= base_url('admin/membership/' . $m['id']) ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <?php if ($err = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger shadow-sm border-0 mb-4"><?= esc($err) ?></div>
    <?php endif; ?>

    <!-- Summary Banner -->
    <div class="alert bg-light border-start border-4 border-brand shadow-sm mb-4 py-3 px-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="fw-semibold text-brand fs-5">
                <?= esc($m['first_name'] . ' ' . $m['last_name']) ?>
            </div>
            <div>
                <span class="badge-lcnl-id">LCNL<?= (int)$m['id'] ?></span>
            </div>

            <div>
                <span class="badge bg-<?=
                                        $m['status'] === 'active' ? 'success' : ($m['status'] === 'pending' ? 'warning text-dark' : 'secondary')
                                        ?> px-3 py-2"><?= ucfirst($m['status']) ?></span>
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
                        <label class="form-label small text-muted">First Name</label>
                        <input name="first_name" class="form-control" value="<?= esc($m['first_name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Last Name</label>
                        <input name="last_name" class="form-control" value="<?= esc($m['last_name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= esc($m['email']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Mobile</label>
                        <input name="mobile" class="form-control" value="<?= esc($m['mobile']) ?>">
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
                        <label class="form-label small text-muted">Address Line 1</label>
                        <input name="address1" class="form-control" value="<?= esc($m['address1']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Address Line 2</label>
                        <input name="address2" class="form-control" value="<?= esc($m['address2']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">City</label>
                        <input name="city" class="form-control" value="<?= esc($m['city']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Postcode</label>
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
                        <label class="form-label small text-muted">Status</label>
                        <select name="status" class="form-select">
                            <?php foreach (['pending', 'active', 'disabled'] as $s): ?>
                                <option value="<?= $s ?>" <?= $m['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="d-flex gap-2 mt-3">
            <button class="btn btn-brand text-white px-4">
                <i class="bi bi-save me-1"></i> Save Changes
            </button>
            <a href="<?= base_url('admin/membership/' . $m['id']) ?>" class="btn btn-outline-secondary px-4">Cancel</a>
        </div>

    </form>
</div>

<?= $this->endSection() ?>