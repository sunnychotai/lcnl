<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-ocean d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">User Administration</h1>
    <p class="lead fs-5 mb-0">LCNL Site Admin</p>
  </div>
</section>
<div class="container py-4">
  <h2><?= isset($user) ? 'Edit User' : 'Add User' ?></h2>

  <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
      <ul>
        <?php foreach (session()->getFlashdata('errors') as $error): ?>
          <li><?= esc($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" action="<?= $action ?>">
    <?= csrf_field() ?>

    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control"
             value="<?= old('name', $user['name'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control"
             value="<?= old('email', $user['email'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Role</label>
      <select name="role" class="form-select" required>
        <option value="">-- Select Role --</option>
        <?php $roles = ['ADMIN', 'WEBSITE', 'MEMBERSHIP']; ?>
        <?php foreach ($roles as $role): ?>
          <option value="<?= $role ?>" <?= old('role', $user['role'] ?? '') == $role ? 'selected' : '' ?>>
            <?= $role ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Password <?= isset($user) ? '(Leave blank to keep current)' : '' ?></label>
      <input type="password" name="password" class="form-control">
    </div>

    <button type="submit" class="btn btn-brand">Save</button>
    <a href="<?= base_url('admin/system/users') ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?= $this->endSection() ?>
