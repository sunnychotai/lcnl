<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<div class="container py-4">
  <h2 class="mb-3">Member</h2>
  <pre class="bg-light p-3 rounded"><?= esc(print_r($member ?? [], true)) ?></pre>
</div>
<?= $this->endSection() ?>
