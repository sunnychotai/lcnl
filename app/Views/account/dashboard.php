<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-2">
      <i class="bi bi-speedometer2 me-2"></i> Member Dashboard
    </h1>
    <p class="lead fs-6 mb-1">Welcome back, <?= esc($memberName) ?>.</p>

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
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0">
      <i class="bi bi-check-circle me-2"></i><?= esc($msg) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if ($err = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0">
      <i class="bi bi-exclamation-triangle me-2"></i><?= esc($err) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- MY PROFILE -->
  <div class="mb-5">
    <div class="card border-0 shadow-sm rounded-4 lcnl-card">
      <div class="card-body p-4">
        <div class="row align-items-center g-3">
          <div class="col-md-8">
            <div class="d-flex align-items-start gap-3">
              <div class="flex-shrink-0">
                <i class="bi bi-person-circle text-brand" style="font-size: 3rem;"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-2 text-brand">
                  <i class="bi bi-person-lines-fill me-2"></i>My Profile
                </h3>
                <p class="text-muted mb-3">Manage your personal information, contact details, and membership preferences.</p>
                <a href="<?= route_to('account.profile.edit') ?>" class="btn btn-brand btn-pill px-4">
                  <i class="bi bi-pencil-square me-2"></i>Edit Profile
                </a>
              </div>
            </div>
          </div>

          <!-- Progress Widget -->
          <div class="col-md-4 text-center">
            <?php
            $totalTasks = count($tasks['todo']) + count($tasks['done']);
            $doneTasks = count($tasks['done']);
            $progress = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;
            ?>
            <div class="p-3 bg-light rounded-3 border">
              <div class="position-relative d-inline-block mb-2">
                <svg width="100" height="100">
                  <circle cx="50" cy="50" r="40" fill="none" stroke="#e9ecef" stroke-width="8"></circle>
                  <circle cx="50" cy="50" r="40" fill="none"
                    stroke="var(--brand)"
                    stroke-width="8"
                    stroke-dasharray="<?= $progress * 2.51 ?> 251"
                    stroke-linecap="round"
                    transform="rotate(-90 50 50)"></circle>
                </svg>
                <div class="position-absolute top-50 start-50 translate-middle">
                  <div class="fw-bold fs-3 text-brand"><?= $progress ?>%</div>
                </div>
              </div>
              <div class="small fw-semibold text-brand">Profile Complete</div>
              <div class="small text-muted"><?= $doneTasks ?>/<?= $totalTasks ?> tasks done</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- OUTSTANDING TASKS -->
  <div class="section-wrapper mb-5">
    <div class="section-header mb-4">
      <h2 class="fw-bold text-brand mb-1"><i class="bi bi-list-check me-2"></i>Outstanding Tasks</h2>
      <p class="text-muted small mb-0">Complete these items to finish setting up your profile</p>
    </div>

    <?php if (!empty($tasks['todo'])): ?>
      <div class="row g-3">
        <?php foreach ($tasks['todo'] as $t): ?>
          <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 lcnl-card border-start border-4 border-brand">
              <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                  <div class="task-icon flex-shrink-0">
                    <i class="bi <?= esc($t['icon']) ?> fs-2 text-accent1"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h5 class="fw-bold mb-2 text-dark"><?= esc($t['title']) ?></h5>
                    <p class="text-muted small mb-3"><?= esc($t['desc']) ?></p>
                    <a href="<?= esc($t['url']) ?>" class="btn btn-brand btn-pill btn-sm px-4">
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

  <!-- COMPLETED TASKS -->
  <?php if (!empty($tasks['done'])): ?>
    <div class="section-wrapper mb-5">
      <div class="section-header mb-4">
        <h4 class="fw-bold text-brand mb-1"><i class="bi bi-check2-all me-2 text-success"></i>Completed Tasks</h4>
        <p class="text-muted small mb-0">You've completed these items</p>
      </div>
      <div class="row g-3">
        <?php foreach ($tasks['done'] as $t): ?>
          <div class="col-md-6 col-lg-4">
            <a href="<?= esc($t['url']) ?>" class="text-decoration-none">
              <div class="completed-task-card p-3 rounded-4 border bg-light h-100">
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

  <!-- UPCOMING EVENTS -->
  <div class="section-wrapper mb-5">
    <div class="section-header mb-4">
      <h2 class="fw-bold text-brand mb-1"><i class="bi bi-calendar-event me-2"></i>Upcoming Events</h2>
      <p class="text-muted small mb-0">Mark your calendar for these events</p>
    </div>

    <?php if (!empty($events)): ?>
      <div class="row g-4">
        <?php foreach ($events as $ev): ?>
          <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden lcnl-card border-top border-4 border-brand">
              <div class="card-body p-4">
                <h5 class="fw-bold mb-3 text-dark"><?= esc($ev['title']) ?></h5>
                <div class="small text-muted mb-3">
                  <i class="bi bi-calendar3 text-brand me-1"></i>
                  <?= date('d M Y', strtotime($ev['event_date'])) ?><br>
                  <i class="bi bi-geo-alt text-brand me-1"></i><?= esc($ev['location'] ?? 'LCNL Hall') ?>
                </div>
                <a href="<?= base_url('events/' . $ev['id']) ?>" class="btn btn-outline-brand btn-pill btn-sm w-100">
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