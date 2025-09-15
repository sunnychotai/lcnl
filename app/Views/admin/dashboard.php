<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-ocean d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">Administration Dashboard</h1>
    <p class="lead fs-5 mb-0">LCNL Site Admin</p>
  </div>
</section>

<div class="container py-5">
 
<?php $role = session()->get('admin_role'); ?>

<div class="row g-4">

<?php if (in_array($role, haystack: ['ADMIN', 'WEBSITE'])): ?>
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
  <?php endif; ?>
  
<?php if (in_array($role, haystack: ['ADMIN', 'WEBSITE'])): ?>
  <div class="col-md-3">
    <a href="<?= base_url('admin/events') ?>" class="text-decoration-none">
      <div class="card shadow-sm h-100 text-center border-0 hover-card">
        <div class="card-body d-flex flex-column align-items-center justify-content-center">
          <i class="bi bi-calendar-event fs-1 text-brand mb-3"></i>
          <h5 class="card-title text-dark mb-1">Events</h5>
          <p class="text-muted small">Manage community events</p>
        </div>
      </div>
    </a>
  </div>
  <?php endif; ?>

  <!-- Only for ADMIN -->
  <?php if ($role === 'ADMIN'): ?>
  <div class="col-md-3">
    <a href="<?= base_url('admin/users') ?>" class="text-decoration-none">
      <div class="card shadow-sm h-100 text-center border-0 hover-card">
        <div class="card-body d-flex flex-column align-items-center justify-content-center">
          <i class="bi bi-people-fill fs-1 text-brand mb-3"></i>
          <h5 class="card-title text-dark mb-1">Manage Users</h5>
          <p class="text-muted small">Admin only: add, edit, remove accounts</p>
        </div>
      </div>
    </a>
  </div>
  <?php endif; ?>

  <!-- ADMIN or WEBSITE -->
  <?php if (in_array($role, ['ADMIN', 'WEBSITE'])): ?>
  <div class="col-md-3">
    <a href="<?= base_url('admin/faqs') ?>" class="text-decoration-none">
      <div class="card shadow-sm h-100 text-center border-0 hover-card">
        <div class="card-body d-flex flex-column align-items-center justify-content-center">
          <i class="bi bi-question-circle-fill fs-1 text-brand mb-3"></i>
          <h5 class="card-title text-dark mb-1">Manage FAQs</h5>
          <p class="text-muted small">Add, edit, reorder FAQs</p>
        </div>
      </div>
    </a>
  </div>
  <?php endif; ?>

<?php if (in_array($role, ['ADMIN', 'MEMBERSHIP'])): ?>
  <div class="card shadow-sm border-0">
  <a href="<?= route_to('admin.members.index') ?>" class="stretched-link text-decoration-none text-dark">
    <div class="card-body d-flex align-items-center">
      <i class="bi bi-person-badge-fill text-brand fs-3 me-3"></i>
      <div>
        <h5 class="card-title mb-1">Membership Admin</h5>
        <p class="card-text text-muted small">Review pending registrations & activate members.</p>
      </div>
    </div>
  </a>
</div>

<div class="col-md-6 col-lg-4">
  <div class="card shadow-sm border-0">
    <a href="<?= base_url('admin/members?status=pending') ?>" class="stretched-link text-decoration-none text-dark">
      <div class="card-body d-flex align-items-center">
        <i class="bi bi-people-fill text-primary fs-3 me-3"></i>
        <div>
          <h5 class="card-title mb-1">Members</h5>
          <p class="card-text text-muted small mb-0">Review & activate new registrations.</p>
        </div>
      </div>
    </a>
  </div>
</div>

  <?php endif; ?>

</div>
 
</div>

<?= $this->endSection() ?>
