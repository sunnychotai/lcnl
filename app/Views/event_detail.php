<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">
  <div class="row g-4 align-items-start">
    
    <!-- Image column -->
    <div class="col-md-5">
      <?php if (!empty($event['image'])): ?>
  <!-- Thumbnail (clickable) -->
  <a href="#" data-bs-toggle="modal" data-bs-target="#eventImageModal">
    <img src="<?= base_url($event['image']) ?>" 
         class="img-fluid rounded shadow-sm w-100 bg-light" 
         style="max-height:350px; object-fit:contain; cursor: zoom-in;" 
         alt="<?= esc($event['title']) ?>">
  </a>

  <!-- Modal for enlarged image -->
  <div class="modal fade" id="eventImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content bg-transparent border-0">
        <img src="<?= base_url($event['image']) ?>" 
             class="img-fluid rounded shadow" 
             alt="<?= esc($event['title']) ?>">
      </div>
    </div>
  </div>


      <?php else: ?>
        <div class="bg-light d-flex align-items-center justify-content-center rounded shadow-sm" 
             style="height:300px;">
          <i class="bi bi-calendar-event fs-1 text-muted"></i>
        </div>
      <?php endif; ?>
    </div>

    <!-- Text column -->
    <div class="col-md-7">
      <h2 class="fw-bold mb-3"><?= esc($event['title']) ?></h2>
      <p class="text-muted fs-5 mb-3">
        <i class="bi bi-calendar3 me-2"></i>
        <?= date('l, d M Y', strtotime($event['event_date'])) ?>
        <?php if (!empty($event['time_from'])): ?>
          · <?= date('H:i', strtotime($event['time_from'])) ?> – <?= date('H:i', strtotime($event['time_to'])) ?>
        <?php endif; ?>
      </p>

      <div class="fs-6 mb-4">
        <?= nl2br(esc($event['description'])) ?>
      </div>

      <div class="d-flex flex-wrap gap-3">
        <?php if (!empty($event['location'])): ?>
          <span class="badge bg-brand fs-6">
            <i class="bi bi-geo-alt-fill me-1"></i> <?= esc($event['location']) ?>
          </span>
        <?php endif; ?>

        <?php if (!empty($event['committee'])): ?>
          <span class="badge bg-accent text-dark fs-6">
            <i class="bi bi-people-fill me-1"></i> <?= esc($event['committee']) ?>
          </span>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php if (!empty($upcomingEvents)): ?>
  <section class="container py-5">
    <h3 class="mb-4">More Upcoming Events</h3>
    <div class="d-flex overflow-auto gap-3 pb-2">
      <?php foreach ($upcomingEvents as $e): ?>
        <a href="<?= base_url('events/'.$e['id']) ?>" 
           class="text-decoration-none flex-shrink-0" 
           style="width: 280px;">
          <div class="card shadow-sm border-0 h-100 event-card">
            <?php if (!empty($e['image'])): ?>
              <div class="event-img-wrapper">
                <img src="<?= base_url($e['image']) ?>" 
                     class="card-img-top" 
                     alt="<?= esc($e['title']) ?>">
                <div class="event-overlay">
                  <h6 class="text-white mb-1"><?= esc($e['title']) ?></h6>
                  <small class="text-light">
                    <?= date('d M Y', strtotime($e['event_date'])) ?>
                    <?php if (!empty($e['time_from'])): ?>
                      · <?= date('H:i', strtotime($e['time_from'])) ?>
                    <?php endif; ?>
                  </small>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </section>
<?php endif; ?>

<?= $this->endSection() ?>
