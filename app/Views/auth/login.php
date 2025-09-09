<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <h2 class="mb-4 text-center">Admin Login</h2>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
      <?php endif; ?>

      <form action="<?= base_url('auth/attemptLogin') ?>" method="post">
        <?= csrf_field() ?>

        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
          <label>Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-brand w-100">Login</button>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
