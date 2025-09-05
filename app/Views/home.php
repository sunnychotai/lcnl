<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <h1 class="display-5 fw-bold text-white">Lohana Community of North London</h1>
        <p class="lead text-white-50 mb-4">Bringing our community together through culture, seva and celebration.</p>
        <a href="<?= base_url('members') ?>" class="btn btn-gold btn-lg me-2">Join LCNL</a>
        <a href="<?= base_url('donate') ?>" class="btn btn-outline-light btn-lg">Donate</a>
      </div>
      <div class="col-lg-5 d-none d-lg-block">
        <img src="<?= base_url('assets/img/lcnl-logo.png') ?>" class="w-100 rounded-3 shadow" alt="LCNL">
      </div>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <a class="card card-link h-100" href="<?= base_url('events') ?>">
          <div class="card-body">
            <h5 class="card-title">Events</h5>
            <p class="card-text text-muted">Navratri, Diwali & year-round programmes.</p>
          </div>
        </a>
      </div>
      <div class="col-md-4">
        <a class="card card-link h-100" href="<?= base_url('members') ?>">
          <div class="card-body">
            <h5 class="card-title">Membership</h5>
            <p class="card-text text-muted">Become a member and support LCNL.</p>
          </div>
        </a>
      </div>
      <div class="col-md-4">
        <a class="card card-link h-100" href="<?= base_url('gallery') ?>">
          <div class="card-body">
            <h5 class="card-title">Gallery</h5>
            <p class="card-text text-muted">Photos & videos from our events.</p>
          </div>
        </a>
      </div>
    </div>
  </div>
</section>

<section class="py-5 bg-light">
  <div class="container">
    <h3 class="mb-3">Upcoming Events</h3>
    <div class="list-group list-group-flush">
      <?php foreach(($events ?? []) as $e): ?>
        <div class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <div class="fw-semibold"><?= esc($e['title']) ?></div>
            <div class="small text-muted"><?= date('j M Y', strtotime($e['date'])) ?> · <?= esc($e['venue']) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?= $this->endSection() ?>
