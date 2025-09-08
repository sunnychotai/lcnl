<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<div class="position-relative w-100" style="height: 300px; overflow: hidden;">
    <img src="<?= base_url('assets/img/hero/contact.png') ?>" 
         alt="Bereavement Support Information" 
         class="img-fluid w-100 h-100" 
         style="object-fit: cover; object-position: center;">

    <!-- Semi-transparent dark layer -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.4);"></div>


</div>

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
