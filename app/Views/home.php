<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero bg-brand d-flex align-items-center">
  <div class="container">
    <div class="row align-items-center g-4">
      
      <!-- Text column -->
      <div class="col">
        <h2 class="fw-bold text-white fs-2">Lohana Community of North London</h2>
        <p class="text-white-50 fs-5 mb-3">
          The Lohana Community North London (LCNL) has been bringing people together since 1976. 
          Serving over 2,300 families, we celebrate our culture, support charitable causes, and 
          create spaces where our community can thrive. Proud of our heritage, we move forward 
          together for future generations.
        </p>
      </div>

      <!-- Logo column -->
      <div class="col-lg-4">
        <img src="<?= base_url('assets/img/lcnl-logo.png') ?>" 
             class="img-fluid" alt="LCNL">
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
