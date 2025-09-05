<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<div class="container py-5">
  <h2>Contact</h2>
  <p>Email us at <a href="mailto:info@lcnl.org.uk">info@lcnl.org.uk</a></p>
  <form class="mt-4">
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Message</label>
      <textarea class="form-control" rows="4"></textarea>
    </div>
    <button class="btn btn-brand">Send</button>
  </form>
</div>
<?= $this->endSection() ?>
