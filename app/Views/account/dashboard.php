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
    <div class="alert alert-success"><?= esc($msg) ?></div>
  <?php endif; ?>
  <?php if ($err = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($err) ?></div>
  <?php endif; ?>

  <!-- Progress Bar -->
  <?php
    $totalTasks = count($tasks['todo']) + count($tasks['done']);
    $doneTasks  = count($tasks['done']);
    $progress   = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;
  ?>
  <div class="mb-4">
    <label class="fw-semibold mb-1">Profile Completion</label>
    <div class="progress" style="height: 20px;">
      <div class="progress-bar bg-brand fw-bold" role="progressbar" style="width: <?= $progress ?>%;">
        <?= $progress ?>%
      </div>
    </div>
  </div>

  <!-- Outstanding Tasks -->
  <div class="mb-4">
    <h3 class="fw-bold mb-3"><i class="bi bi-list-check me-2 text-brand"></i> Outstanding tasks</h3>
    <?php if (!empty($tasks['todo'])): ?>
      <div class="row g-3">
        <?php foreach ($tasks['todo'] as $t): ?>
          <div class="col-md-6">
            <div class="lcnl-card shadow-sm border-0 rounded-4 h-100 hover-card">
              <div class="card-body d-flex align-items-start gap-3">
                <i class="bi <?= esc($t['icon']) ?> fs-2 text-accent"></i>
                <div class="flex-grow-1">
                  <h5 class="fw-bold mb-1"><?= esc($t['title']) ?></h5>
                  <p class="text-muted small mb-2"><?= esc($t['desc']) ?></p>
                  <a href="<?= esc($t['url']) ?>" class="btn btn-brand btn-sm rounded-pill px-3">Go</a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-success"><i class="bi bi-check2-circle me-2"></i>All set! No outstanding tasks.</div>
    <?php endif; ?>
  </div>

  <!-- Completed -->
  <div class="mb-4">
    <h5 class="fw-bold mb-2"><i class="bi bi-check2-all me-2 text-success"></i> Completed</h5>
    <?php if (!empty($tasks['done'])): ?>
      <div class="row g-2">
        <?php foreach ($tasks['done'] as $t): ?>
          <div class="col-md-4">
            <a href="<?= esc($t['url']) ?>" class="text-decoration-none">
              <div class="p-3 rounded-4 border bg-light h-100 hover-card">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi <?= esc($t['icon']) ?> text-success"></i>
                  <span class="small text-dark"><?= esc($t['title']) ?></span>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-muted small">You’ll see completed items here.</p>
    <?php endif; ?>
  </div>

  <!-- Quick links -->
  <div class="row g-3 mb-5">
    <div class="col-md-6">
      <div class="lcnl-card shadow-sm border-0 hover-card">
        <a href="<?= route_to('account.profile.edit') ?>" class="stretched-link text-decoration-none text-dark">
          <div class="card-body d-flex align-items-center">
            <i class="bi bi-person-lines-fill text-brand fs-3 me-3"></i>
            <div>
              <h5 class="card-title mb-1">My Profile</h5>
              <p class="card-text text-muted small mb-0">Update your contact details and preferences.</p>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>

  <!-- Upcoming Events -->
  <div class="mt-5">
    <h3 class="fw-bold mb-3">
      <i class="bi bi-calendar-event me-2 text-brand"></i> Upcoming Events
    </h3>
    <?php if (!empty($events)): ?>
      <div class="row g-4">
        <?php foreach ($events as $ev): ?>
          <div class="col-md-4">
            <div class="card shadow-sm h-100 border-0 hover-card">
              <div class="card-body">
                <h5 class="card-title fw-bold"><?= esc($ev['title']) ?></h5>
                <p class="small text-muted mb-1">
                  <i class="bi bi-calendar me-1"></i>
                  <?= date('d M Y', strtotime($ev['event_date'])) ?>
                </p>
                <p class="small text-muted mb-3">
                  <i class="bi bi-geo-alt me-1"></i>
                  <?= esc($ev['location'] ?? 'LCNL Hall') ?>
                </p>
                <a href="<?= base_url('events/'.$ev['id']) ?>" class="btn btn-outline-brand btn-sm rounded-pill">
                  View Details
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-muted">No upcoming events right now.</p>
    <?php endif; ?>
  </div>

</div>

<?= $this->endSection() ?>

<style>
.hover-card {
  transition: transform .2s ease, box-shadow .2s ease;
}
.hover-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}
</style>
