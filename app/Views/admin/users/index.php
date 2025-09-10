<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">
  <h2 class="mb-3">Manage Users</h2>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>

  <a href="<?= base_url('admin/users/create') ?>" class="btn btn-brand mb-3">Add User</a>

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
            <a href="<?= base_url('admin/users/edit/'.$user['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="<?= base_url('admin/users/delete/'.$user['id']) ?>" 
               class="btn btn-sm btn-danger"
               onclick="return confirm('Are you sure?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?= $this->endSection() ?>
