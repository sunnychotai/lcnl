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

  <!-- =======================
       Quick Stats
  ======================== -->
  <h4 class="fw-bold mb-3">Quick Stats</h4>
  <div class="row g-4 mb-5">
    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center h-100 bg-light">
        <div class="card-body">
          <i class="bi bi-person-check-fill fs-2 text-success mb-2"></i>
          <h6 class="fw-bold mb-0">Active Members</h6>
          <p class="display-6 fw-bold mb-0"><?= (int)($stats['active_members'] ?? 0) ?></p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center h-100 bg-light">
        <div class="card-body">
          <i class="bi bi-hourglass-split fs-2 text-warning mb-2"></i>
          <h6 class="fw-bold mb-0">Pending Members</h6>
          <p class="display-6 fw-bold mb-0"><?= (int)($stats['pending_members'] ?? 0) ?></p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center h-100 bg-light">
        <div class="card-body">
          <i class="bi bi-envelope-paper-fill fs-2 text-primary mb-2"></i>
          <h6 class="fw-bold mb-0">Emails Sent</h6>
          <p class="display-6 fw-bold mb-0"><?= (int)($stats['emails_sent'] ?? 0) ?></p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center h-100 bg-light">
        <div class="card-body">
          <i class="bi bi-clock-history fs-2 text-dark mb-2"></i>
          <h6 class="fw-bold mb-0">Last Admin Login</h6>
          <p class="fw-bold mb-0 small"><?= esc($stats['last_login'] ?? '-') ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- =======================
       Admin Actions
  ======================== -->
  <h4 class="fw-bold mb-3">Admin Actions</h4>
  <div class="row g-4">

    <?php if (hasRole('ADMIN', 'WEBSITE')): ?>
      <!-- Committee -->
      <div class="col-md-3">
        <a href="<?= base_url('admin/content/committee') ?>" class="text-decoration-none">
          <div class="card shadow-sm h-100 text-center border-0 hover-card">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
              <i class="bi bi-people-fill fs-1 text-brand mb-3"></i>
              <h5 class="card-title text-dark mb-1">Committee</h5>
              <p class="text-muted small">Manage committee members</p>
            </div>
          </div>
        </a>
      </div>

      <!-- FAQs -->
      <div class="col-md-3">
        <a href="<?= base_url('admin/content/faqs') ?>" class="text-decoration-none">
          <div class="card shadow-sm h-100 text-center border-0 hover-card">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
              <i class="bi bi-question-circle-fill fs-1 text-brand mb-3"></i>
              <h5 class="card-title text-dark mb-1">FAQs</h5>
              <p class="text-muted small">Manage FAQs</p>
            </div>
          </div>
        </a>
      </div>
    <?php endif; ?>

    <?php if (hasRole('ADMIN')): ?>
      <!-- Users -->
      <div class="col-md-3">
        <a href="<?= base_url('admin/system/users') ?>" class="text-decoration-none">
          <div class="card shadow-sm h-100 text-center border-0 hover-card">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
              <i class="bi bi-person-fill-gear fs-1 text-brand mb-3"></i>
              <h5 class="card-title text-dark mb-1">Users</h5>
              <p class="text-muted small">Admin only: manage accounts</p>
            </div>
          </div>
        </a>
      </div>

      <!-- Emails -->
      <div class="col-md-3">
        <a href="<?= base_url('admin/system/emails') ?>" class="text-decoration-none">
          <div class="card shadow-sm h-100 text-center border-0 hover-card">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
              <i class="bi bi-envelope-fill fs-1 text-brand mb-3"></i>
              <h5 class="card-title text-dark mb-1">Emails</h5>
              <p class="text-muted small">View & retry queued emails</p>
            </div>
          </div>
        </a>
      </div>
    <?php endif; ?>

    <?php if (hasRole('ADMIN', 'MEMBERSHIP')): ?>
      <!-- Members -->
      <div class="col-md-3">
        <a href="<?= base_url('admin/membership/members') ?>" class="text-decoration-none">
          <div class="card shadow-sm h-100 text-center border-0 hover-card">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
              <i class="bi bi-person-badge-fill fs-1 text-brand mb-3"></i>
              <h5 class="card-title text-dark mb-1">Members</h5>
              <p class="text-muted small">Review & approve members</p>
            </div>
          </div>
        </a>
      </div>
    <?php endif; ?>


    <?php if (hasRole('ADMIN', 'EVENTS', 'WEBSITE')): ?>
      <!-- Events -->
      <div class="col-md-3">
        <a href="<?= base_url('admin/content/events') ?>" class="text-decoration-none">
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


    <?php if (hasRole('FINANCE')): ?>
      <!-- Finance (Inactive Dummy) -->
      <div class="col-md-3">
        <div class="card shadow-sm h-100 text-center border-0 hover-none bg-light-subtle opacity-75">
          <div class="card-body d-flex flex-column align-items-center justify-content-center position-relative">
            <i class="bi bi-cash-coin fs-1 text-secondary mb-3"></i>
            <h5 class="card-title text-muted mb-1">Finance (Coming Soon)</h5>
            <p class="text-muted small">Module under development</p>
            <span class="badge bg-secondary position-absolute top-0 end-0 m-2">Inactive</span>
          </div>
        </div>
      </div>
    <?php endif; ?>

  </div>
</div>

<?= $this->endSection() ?>