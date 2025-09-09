<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">
  <h2 class="mb-4">
    <?= !empty($committee['id']) ? 'Edit Committee Member' : 'Add Committee Member' ?>
  </h2>

  <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
      <ul>
        <?php foreach (session()->getFlashdata('errors') as $error): ?>
          <li><?= esc($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- ✅ Use the $action passed in from controller -->
  <form action="<?= $action ?>" method="post">
    <?= csrf_field() ?>

    <div class="mb-3">
      <label>First Name</label>
      <input type="text" name="firstname" class="form-control"
             value="<?= old('firstname', $committee['firstname'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
      <label>Surname</label>
      <input type="text" name="surname" class="form-control"
             value="<?= old('surname', $committee['surname'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control"
             value="<?= old('email', $committee['email'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
      <label>Role</label>
      <input type="text" name="role" class="form-control"
             value="<?= old('role', $committee['role'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label>Committee</label>
      <select name="committee" class="form-control" required>
        <option value="">-- Select --</option>
        <?php foreach ($committees as $c): ?>
          <option value="<?= $c ?>"
            <?= old('committee', $committee['committee'] ?? $selectedCommittee ?? '') === $c ? 'selected' : '' ?>>
            <?= $c ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label>Display Order</label>
      <input type="number" name="display_order" class="form-control"
             value="<?= old('display_order', $committee['display_order'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
      <label>Image URL</label>
      <input type="text" name="image" class="form-control"
             value="<?= old('image', $committee['image'] ?? '/uploads/committee/') ?>" required>
    </div>

    <button type="submit" class="btn btn-brand">
      <?= !empty($committee['id']) ? 'Update' : 'Save' ?>
    </button>
    <a href="<?= base_url('admin/committee') ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?= $this->endSection() ?>
