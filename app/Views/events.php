<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<div class="hero hero-rangoli-orange d-flex align-items-center justify-content-center">
  <div class="overlay"></div>
  <div class="container position-relative text-center">
    <h1 class="text-white fw-bold">Events</h1>
    <p class="text-white-75">Discover our upcoming events by month.</p>
  </div>
</div>

<div class="container py-5">
  <?php if (!empty($groupedEvents)): ?>
    <?php foreach ($groupedEvents as $month => $events): ?>
      <h2 class="mb-4 mt-5"><?= esc($month) ?></h2>
      <div class="d-flex overflow-auto gap-3 pb-2">
        <?php foreach ($events as $event): ?>
          <a href="<?= base_url('events/'.$event['id']) ?>" 
             class="text-decoration-none flex-shrink-0" 
             style="width: 280px;">
            <div class="card shadow-sm border-0 h-100 event-card">
              <?php if (!empty($event['image'])): ?>
                <div class="event-img-wrapper">
                  <img src="<?= base_url($event['image']) ?>" 
                       class="card-img-top" 
                       alt="<?= esc($event['title']) ?>">
                  <div class="event-overlay">
                    <h6 class="text-white mb-1"><?= esc($event['title']) ?></h6>
                    <small class="text-light">
                      <?= date('d M Y', strtotime($event['event_date'])) ?>
                      <?php if (!empty($event['time_from'])): ?>
                        · <?= date('H:i', strtotime($event['time_from'])) ?>
                      <?php endif; ?>
                    </small>
                  </div>
                </div>
              <?php else: ?>
                <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height:200px;">
                  <i class="bi bi-calendar-event fs-1 text-muted"></i>
                </div>
              <?php endif; ?>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No upcoming events found.</p>
  <?php endif; ?>
</div>

<?= $this->endSection() ?>
