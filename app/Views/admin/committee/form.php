<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      <div class="card shadow-sm border-0">
        <div class="card-header bg-brand text-white d-flex justify-content-between align-items-center">
          <h4 class="mb-0">
            <i class="bi bi-people-fill me-2"></i>
            <?= !empty($committee['id']) ? 'Edit Committee Member' : 'Add Committee Member' ?>
          </h4>
          <a href="<?= base_url('admin/committee') ?>" class="btn btn-outline-light btn-sm">
            <i class="bi bi-arrow-left"></i> Back
          </a>
        </div>

        <div class="card-body">

          <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                  <li><?= esc($error) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <!-- ✅ Use the $action passed in from controller -->
          <form action="<?= $action ?>" method="post">
            <?= csrf_field() ?>

            <!-- Firstname -->
            <div class="mb-3">
              <label class="form-label fw-semibold"><i class="bi bi-person me-1"></i> First Name</label>
              <input type="text" name="firstname" class="form-control"
                     value="<?= old('firstname', $committee['firstname'] ?? '') ?>" required>
            </div>

            <!-- Surname -->
            <div class="mb-3">
              <label class="form-label fw-semibold"><i class="bi bi-person-lines-fill me-1"></i> Surname</label>
              <input type="text" name="surname" class="form-control"
                     value="<?= old('surname', $committee['surname'] ?? '') ?>" required>
            </div>

            <!-- Email -->
            <div class="mb-3">
              <label class="form-label fw-semibold"><i class="bi bi-envelope me-1"></i> Email</label>
              <input type="email" name="email" class="form-control"
                     value="<?= old('email', $committee['email'] ?? '') ?>" required>
            </div>

            <!-- Role -->
            <div class="mb-3">
              <label class="form-label fw-semibold"><i class="bi bi-person-badge me-1"></i> Role</label>
              <input type="text" name="role" class="form-control"
                     value="<?= old('role', $committee['role'] ?? '') ?>">
            </div>

            <!-- Committee -->
            <div class="mb-3">
              <label class="form-label fw-semibold"><i class="bi bi-diagram-3 me-1"></i> Committee</label>
              <select name="committee" class="form-select" required>
                <option value="">-- Select --</option>
                <?php foreach ($committees as $c): ?>
                  <option value="<?= $c ?>"
                    <?= old('committee', $committee['committee'] ?? $selectedCommittee ?? '') === $c ? 'selected' : '' ?>>
                    <?= $c ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Display Order + Image -->
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold"><i class="bi bi-list-ol me-1"></i> Display Order</label>
                <input type="number" name="display_order" class="form-control"
                       value="<?= old('display_order', $committee['display_order'] ?? '') ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold"><i class="bi bi-image me-1"></i> Image URL</label>
                <input type="text" name="image" class="form-control"
                       value="<?= old('image', $committee['image'] ?? '/uploads/committee/') ?>" required>
              </div>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-end gap-2 mt-3">
              <a href="<?= base_url('admin/committee') ?>" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Cancel
              </a>
              <button type="submit" class="btn btn-success">
                <i class="bi bi-save"></i>
                <?= !empty($committee['id']) ? 'Update Member' : 'Save Member' ?>
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<?= $this->endSection() ?>
