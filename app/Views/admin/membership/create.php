<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero -->
<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
    <div class="container position-relative text-center text-white py-4">
        <h1 class="fw-bold display-6 mb-1">
            <i class="bi bi-person-plus-fill me-2"></i>Membership Admin: Add Member
        </h1>
        <p class="mb-0 opacity-75">Manually create an LCNL member account</p>
    </div>
</section>

<div class="container py-5">

    <?php if ($msg = session()->getFlashdata('message')): ?>
        <div class="alert alert-success shadow-sm alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i><?= esc($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger shadow-sm alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">

            <div class="card shadow-lg border-0 auth-card">
                <div class="card-header bg-gradient py-3">
                    <h3 class="mb-0 d-flex align-items-center">
                        <i class="bi bi-person-lines-fill me-2"></i>
                        Member Details
                    </h3>
                    <p class="mb-0 mt-1 small opacity-90">
                        Fields marked with <span class="text-danger">*</span> are required
                    </p>
                </div>

                <div class="card-body p-4 p-md-5">

                    <form method="post" action="<?= base_url('admin/membership/store') ?>" novalidate>
                        <?= csrf_field() ?>

                        <!-- PERSONAL INFORMATION -->
                        <div class="form-section">
                            <h5 class="section-title mb-3">
                                <i class="bi bi-person-badge me-2"></i>Personal Information
                            </h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">First Name *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="bi bi-person-fill text-brand"></i></span>
                                        <input name="first_name" class="form-control" required
                                            value="<?= esc(old('first_name')) ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Surname *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="bi bi-person-fill text-brand"></i></span>
                                        <input name="last_name" class="form-control" required
                                            value="<?= esc(old('last_name')) ?>">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Email *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="bi bi-envelope-fill text-brand"></i></span>
                                        <input name="email" type="email" class="form-control" required
                                            value="<?= esc(old('email')) ?>">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Mobile</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="bi bi-telephone-fill text-brand"></i></span>
                                        <input name="mobile" class="form-control" value="<?= esc(old('mobile')) ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Date of Birth</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="bi bi-calendar-date text-brand"></i></span>
                                        <input type="date" name="date_of_birth" class="form-control"
                                            value="<?= esc(old('date_of_birth')) ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Gender</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="bi bi-gender-ambiguous text-brand"></i></span>
                                        <select name="gender" class="form-select">
                                            <option value="">— Select —</option>
                                            <option value="male" <?= old('gender') === 'male' ? 'selected' : '' ?>>Male
                                            </option>
                                            <option value="female" <?= old('gender') === 'female' ? 'selected' : '' ?>>
                                                Female</option>
                                            <option value="other" <?= old('gender') === 'other' ? 'selected' : '' ?>>Other
                                            </option>
                                            <option value="prefer_not_to_say" <?= old('gender') === 'prefer_not_to_say' ? 'selected' : '' ?>>Prefer not to say</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ADDRESS -->
                        <div class="form-section mt-4">
                            <h5 class="section-title mb-3">
                                <i class="bi bi-house-door-fill me-2"></i>Address Details
                            </h5>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Address Line 1 *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="bi bi-geo-alt-fill text-brand"></i></span>
                                        <input name="address1" class="form-control" required
                                            value="<?= esc(old('address1')) ?>">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Address Line 2</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="bi bi-geo-fill text-brand"></i></span>
                                        <input name="address2" class="form-control" value="<?= esc(old('address2')) ?>">
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <label class="form-label fw-semibold">City *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="bi bi-building text-brand"></i></span>
                                        <input name="city" class="form-control" required
                                            value="<?= esc(old('city')) ?>">
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label fw-semibold">Postcode *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="bi bi-mailbox text-brand"></i></span>
                                        <input name="postcode" class="form-control" required maxlength="12"
                                            value="<?= esc(old('postcode')) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ADMIN CONTROLS -->
                        <div class="form-section mt-4">
                            <h5 class="section-title mb-3">
                                <i class="bi bi-gear-fill me-2"></i>Admin Settings
                            </h5>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Status *</label>
                                    <select name="status" class="form-select">
                                        <option value="pending" selected>Pending</option>
                                        <option value="active">Active</option>
                                        <option value="disabled">Disabled</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Membership Type *</label>
                                    <select name="membership_type" class="form-select">
                                        <option value="standard" selected>Standard</option>
                                        <option value="life">Life</option>
                                    </select>
                                </div>

                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="send_activation" value="1"
                                            checked>
                                        <label class="form-check-label">
                                            Send activation email
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SUBMIT -->
                        <div class="d-grid gap-2 mt-4">
                            <button class="btn btn-accent btn-lg rounded-pill shadow-sm" type="submit">
                                <i class="bi bi-check2-circle me-2"></i>Create Member
                            </button>
                        </div>

                    </form>

                </div>

                <div class="card-footer bg-light text-center py-3">
                    <a href="<?= base_url('admin/membership') ?>" class="fw-semibold text-decoration-none">
                        <i class="bi bi-arrow-left-circle me-1"></i>Back to members
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>

