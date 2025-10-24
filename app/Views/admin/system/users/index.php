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
  <h2 class="mb-3">Manage Users</h2>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>

  <a href="<?= base_url('admin/system/users/create') ?>" class="btn btn-brand mb-3">Add User</a>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
        <tr>
          <td><?= $user['id'] ?></td>
          <td><?= esc($user['name']) ?></td>
          <td><?= esc($user['email']) ?></td>
          <td><?= esc($user['role']) ?></td>
          <td>
            <a href="<?= base_url('admin/system/users/edit/'.$user['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="<?= base_url('admin/system/users/delete/'.$user['id']) ?>" 
               class="btn btn-sm btn-danger"
               onclick="return confirm('Are you sure?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?= $this->endSection() ?>
