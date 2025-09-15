<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">
      <i class="bi bi-speedometer2 me-2"></i> Member Dashboard
    </h1>
    <p class="lead fs-6 mb-0">Welcome back, <?= esc($memberName) ?>.</p>
  </div>
</section>

<div class="container py-4">
  <?php if ($msg = session()->getFlashdata('message')): ?>
    <div class="alert alert-success"><?= esc($msg) ?></div>
  <?php endif; ?>
  <?php if ($err = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($err) ?></div>
  <?php endif; ?>

  <!-- Outstanding Tasks -->
  <div class="mb-4">
    <h3 class="fw-bold mb-3"><i class="bi bi-list-check me-2 text-brand"></i> Outstanding tasks</h3>
    <?php if (!empty($tasks['todo'])): ?>
      <div class="row g-3">
        <?php foreach ($tasks['todo'] as $t): ?>
          <div class="col-md-6">
            <div class="lcnl-card shadow-sm border-0 rounded-4 h-100">
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
              <div class="p-3 rounded-4 border bg-light h-100">
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
  <div class="row g-3">
    <div class="col-md-6">
      <div class="lcnl-card shadow-sm border-0">
        <a href="<?= route_to('account.household') ?>" class="stretched-link text-decoration-none text-dark">
          <div class="card-body d-flex align-items-center">
            <i class="bi bi-people-fill text-brand fs-3 me-3"></i>
            <div>
              <h5 class="card-title mb-1">My Household</h5>
              <p class="card-text text-muted small mb-0">Manage your family members and invites.</p>
            </div>
          </div>
        </a>
      </div>
    </div>

    <div class="col-md-6">
      <div class="lcnl-card shadow-sm border-0">
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

</div>

<?= $this->endSection() ?>
