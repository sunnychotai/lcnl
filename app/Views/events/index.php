<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('partials/page_header', [
  'overlay'    => 'midnight',
  'heading'    => 'Events',
  'subheading' => 'Upcoming LCNL events',
]) ?>

<div class="container py-1">
  <?php if (!empty($groupedEvents)): ?>
    <?php foreach ($groupedEvents as $month => $events): ?>
      <h2 class="mb-1 mt-3"><?= esc($month) ?></h2>
      <div class="d-flex overflow-auto gap-3 pb-2">
        <?php foreach ($events as $event): ?>
          <a href="<?= base_url('events/'.$event['id']) ?>" 
             class="text-decoration-none flex-shrink-0 event-card-link">
            <div class="card shadow-sm border-0 event-card">
              <?php
  $imgPath = !empty($event['image']) ? $event['image'] : '';
  if (!$imgPath || !is_file(FCPATH . $imgPath)) {
      $imgPath = 'assets/img/lcnl-placeholder-320.png';
  }
?>
<div class="event-img-wrapper position-relative">
  <img src="<?= base_url($imgPath) ?>"
       class="card-img-top"
       alt="<?= esc($event['title']) ?>"
       loading="lazy" decoding="async">
  <div class="event-overlay">
    <h3 class="h6 text-white mb-1"><?= esc($event['title']) ?></h3>
    <small class="text-light">
      <?= date('d M Y', strtotime($event['event_date'])) ?>
      <?php if (!empty($event['time_from'])): ?>
        · <?= date('H:i', strtotime($event['time_from'])) ?>
      <?php endif; ?>
    </small>
  </div>
  <?php if (!empty($event['is_sold_out'])): ?>
    <div class="sold-out-banner">
      <i class="bi bi-x-circle-fill me-1"></i>SOLD OUT
    </div>
  <?php endif; ?>
</div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No upcoming events found.</p>
  <?php endif; ?>
</div>

<style>
  .sold-out-banner {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    padding: 6px 0;
    background: rgba(185, 28, 28, 0.92);
    color: #fff;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-align: center;
    text-transform: uppercase;
    z-index: 20;
  }
</style>

<?= $this->endSection() ?>
