<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<div class="hero hero-rangoli-grey d-flex align-items-center justify-content-center">
  <div class="overlay"></div>
  <div class="container position-relative">
    <h1 class="text-white fw-bold">Administration</h1>
    <p class="text-white-75"><pre><?php print_r(session()->get()); ?></pre></p>
  </div>
</div>

<div class="container py-5">
 
<div class="row g-4">
  <!-- Committee -->
  <div class="col-md-3">
    <a href="<?= base_url('admin/committee') ?>" class="text-decoration-none">
      <div class="card shadow-sm h-100 text-center border-0 hover-card">
        <div class="card-body d-flex flex-column align-items-center justify-content-center">
          <div class="mb-3">
            <i class="bi bi-people-fill fs-1 text-brand"></i>
          </div>
          <h5 class="card-title text-dark mb-1">Manage Committee</h5>
          <p class="text-muted small">View, add, edit, and delete members</p>
        </div>
      </div>
    </a>
  </div>

  <!-- Events -->
  <div class="col-md-3">
    <a href="<?= base_url('admin/events') ?>" class="text-decoration-none">
      <div class="card shadow-sm h-100 text-center border-0 hover-card">
        <div class="card-body d-flex flex-column align-items-center justify-content-center">
          <div class="mb-3">
            <i class="bi bi-calendar-event-fill fs-1 text-accent"></i>
          </div>
          <h5 class="card-title text-dark mb-1">Manage Events</h5>
          <p class="text-muted small">Add, edit, clone, and remove events</p>
        </div>
      </div>
    </a>
  </div>
</div>




  
</div>

<?= $this->endSection() ?>
