<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-2">
      <i class="bi bi-speedometer2 me-2"></i> Member Dashboard
    </h1>
    <p class="lead fs-6 mb-1">Welcome back, <?= esc($memberName) ?>.</p>

    <!-- Event Ticker -->
    <?php if (!empty($events)): ?>
      <p class="small fst-italic text-accent mt-2">
        <i class="bi bi-megaphone-fill me-1"></i>
        Next: <?= esc($events[0]['title']) ?> on <?= date('d M Y', strtotime($events[0]['event_date'])) ?>
      </p>
    <?php endif; ?>
  </div>
</section>

<div class="container py-4">
  <!-- Flash messages -->
  <?php if ($msg = session()->getFlashdata('message')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-2"></i><?= esc($msg) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if ($err = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i><?= esc($err) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- ========== MY PROFILE (Featured) ========== -->
  <div class="mb-5">
    <div class="featured-card lcnl-card shadow border-0 rounded-4 overflow-hidden">
      <div class="card-body p-4">
        <div class="row align-items-center g-3">
          <div class="col-md-8">
            <div class="d-flex align-items-start gap-3">
              <div class="icon-wrapper">
                <i class="bi bi-person-circle text-brand" style="font-size: 3rem;"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-2 text-brand">
                  <i class="bi bi-person-lines-fill me-2"></i>My Profile
                </h3>
                <p class="text-muted mb-3">Manage your personal information, contact details, and membership
                  preferences.</p>
                <a href="<?= route_to('account.profile.edit') ?>" class="btn btn-brand rounded-pill px-4">
                  <i class="bi bi-pencil-square me-2"></i>Edit Profile
                </a>
              </div>
            </div>
          </div>

          <!-- Progress Widget -->
          <div class="col-md-4">
            <?php
            $totalTasks = count($tasks['todo']) + count($tasks['done']);
            $doneTasks = count($tasks['done']);
            $progress = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;
            ?>
            <div class="text-center p-3 bg-light rounded-3">
              <div class="position-relative d-inline-block mb-2">
                <svg width="100" height="100">
                  <circle cx="50" cy="50" r="40" fill="none" stroke="#e9ecef" stroke-width="8"></circle>
                  <circle cx="50" cy="50" r="40" fill="none" stroke="var(--bs-primary)" stroke-width="8"
                    stroke-dasharray="<?= $progress * 2.51 ?> 251" stroke-linecap="round" transform="rotate(-90 50 50)">
                  </circle>
                </svg>
                <div class="position-absolute top-50 start-50 translate-middle">
                  <div class="fw-bold fs-4 text-brand"><?= $progress ?>%</div>
                </div>
              </div>
              <div class="small text-muted fw-semibold">Profile Complete</div>
              <div class="small text-muted"><?= $doneTasks ?>/<?= $totalTasks ?> tasks done</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========== OUTSTANDING TASKS ========== -->
  <div class="section-wrapper mb-5">
    <div class="section-header mb-4">
      <h2 class="fw-bold text-dark mb-1">
        <i class="bi bi-list-check me-2 text-brand"></i>Outstanding Tasks
      </h2>
      <p class="text-muted small mb-0">Complete these items to finish setting up your profile</p>
    </div>

    <?php if (!empty($tasks['todo'])): ?>
      <div class="row g-3">
        <?php foreach ($tasks['todo'] as $t): ?>
          <div class="col-lg-6">
            <div class="task-card lcnl-card shadow-sm border-0 rounded-4 h-100">
              <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                  <div class="task-icon flex-shrink-0">
                    <i class="bi <?= esc($t['icon']) ?> fs-2 text-accent"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h5 class="fw-bold mb-2"><?= esc($t['title']) ?></h5>
                    <p class="text-muted small mb-3"><?= esc($t['desc']) ?></p>
                    <a href="<?= esc($t['url']) ?>" class="btn btn-brand btn-sm rounded-pill px-4">
                      <i class="bi bi-arrow-right-circle me-2"></i>Complete Now
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-success border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 p-4">
        <i class="bi bi-check-circle-fill fs-1 text-success"></i>
        <div>
          <h5 class="fw-bold mb-1">All Caught Up!</h5>
          <p class="mb-0">You have no outstanding tasks. Great job!</p>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- ========== COMPLETED TASKS ========== -->
  <?php if (!empty($tasks['done'])): ?>
    <div class="section-wrapper mb-5">
      <div class="section-header mb-4">
        <h4 class="fw-bold text-dark mb-1">
          <i class="bi bi-check2-all me-2 text-success"></i>Completed Tasks
        </h4>
        <p class="text-muted small mb-0">You've completed these items</p>
      </div>

      <div class="row g-3">
        <?php foreach ($tasks['done'] as $t): ?>
          <div class="col-md-6 col-lg-4">
            <a href="<?= esc($t['url']) ?>" class="text-decoration-none">
              <div class="completed-task-card p-3 rounded-3 border bg-light h-100">
                <div class="d-flex align-items-center gap-3">
                  <i class="bi <?= esc($t['icon']) ?> fs-4 text-success"></i>
                  <span class="small fw-semibold text-dark"><?= esc($t['title']) ?></span>
                  <i class="bi bi-check-circle-fill text-success ms-auto"></i>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- ========== UPCOMING EVENTS ========== -->
  <div class="section-wrapper mb-5">
    <div class="section-header mb-4">
      <h2 class="fw-bold text-dark mb-1">
        <i class="bi bi-calendar-event me-2 text-brand"></i>Upcoming Events
      </h2>
      <p class="text-muted small mb-0">Mark your calendar for these events</p>
    </div>

    <?php if (!empty($events)): ?>
      <div class="row g-4">
        <?php foreach ($events as $ev): ?>
          <div class="col-md-6 col-lg-4">
            <div class="event-card card shadow-sm h-100 border-0 rounded-4 overflow-hidden">
              <div class="card-body p-4">
                <h5 class="card-title fw-bold mb-3"><?= esc($ev['title']) ?></h5>
                <div class="event-details mb-3">
                  <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-calendar3 text-brand"></i>
                    <span class="small text-muted">
                      <?= date('d M Y', strtotime($ev['event_date'])) ?>
                    </span>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-geo-alt text-brand"></i>
                    <span class="small text-muted">
                      <?= esc($ev['location'] ?? 'LCNL Hall') ?>
                    </span>
                  </div>
                </div>
                <a href="<?= base_url('events/' . $ev['id']) ?>" class="btn btn-outline-brand btn-sm rounded-pill w-100">
                  <i class="bi bi-eye me-2"></i>View Details
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-light border rounded-4 p-4">
        <p class="text-muted mb-0">
          <i class="bi bi-info-circle me-2"></i>No upcoming events scheduled at this time. Check back soon!
        </p>
      </div>
    <?php endif; ?>
  </div>

</div>

<?= $this->endSection() ?>

<style>
  /* Section Separation */
  .section-wrapper {
    padding: 2rem 0;
    border-bottom: 2px solid #f0f0f0;
  }

  .section-wrapper:last-child {
    border-bottom: none;
  }

  .section-header {
    position: relative;
    padding-left: 1rem;
    border-left: 4px solid var(--bs-primary);
  }

  /* Featured Card (My Profile) */
  .featured-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: 2px solid var(--bs-primary) !important;
    transition: transform .3s ease, box-shadow .3s ease;
  }

  .featured-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12) !important;
  }

  /* Task Cards */
  .task-card {
    transition: transform .2s ease, box-shadow .2s ease;
    border-left: 4px solid var(--bs-primary);
  }

  .task-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
  }

  .task-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(var(--bs-primary-rgb), 0.1);
    border-radius: 12px;
  }

  /* Completed Tasks */
  .completed-task-card {
    transition: all .2s ease;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
  }

  .completed-task-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
  }

  /* Event Cards */
  .event-card {
    transition: transform .2s ease, box-shadow .2s ease;
    border-top: 4px solid var(--bs-primary);
  }

  .event-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
  }

  .event-details i {
    font-size: 1.1rem;
  }

  /* Mobile Optimization */
  @media (max-width: 768px) {
    .section-wrapper {
      padding: 1.5rem 0;
    }

    .featured-card .row {
      flex-direction: column-reverse;
    }

    .task-icon {
      width: 50px;
      height: 50px;
    }

    .icon-wrapper i {
      font-size: 2.5rem !important;
    }
  }

  /* Progress Circle Animation */
  .progress-circle circle:last-child {
    transition: stroke-dasharray 0.6s ease;
  }

  /* Alerts */
  .alert {
    border-left: 4px solid;
  }

  .alert-success {
    border-left-color: #198754;
  }

  .alert-danger {
    border-left-color: #dc3545;
  }
</style>
