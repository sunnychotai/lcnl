<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$requiresReg = !empty($event['requires_registration']);
$capacity = (int) ($event['capacity'] ?? 0);
$current = (int) ($event['current_registrations'] ?? 0);
$isFull = $requiresReg && $capacity > 0 && $current >= $capacity;
$spotsLeft = ($capacity > 0) ? max(0, $capacity - $current) : null;
?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-moss d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">

    <h1 class="fw-bold display-6 mb-2">
      <i class="bi bi-calendar-event me-2"></i>
      <?= esc($event['title'] ?? 'Event') ?>
    </h1>

    <?php
    $timeStr = '';
    $tf = !empty($event['time_from']) ? date('H:i', strtotime($event['time_from'])) : '';
    $tt = !empty($event['time_to']) ? date('H:i', strtotime($event['time_to'])) : '';
    if ($tf || $tt) {
      $timeStr = $tf . ($tt ? '–' . $tt : '');
    }
    ?>

    <div class="event-meta d-flex justify-content-center flex-wrap gap-2 mt-2">

      <?php if (!empty($event['event_date'])): ?>
        <span class="badge badge-glass">
          <i class="bi bi-calendar-event me-1"></i>
          <?= date('D j M Y', strtotime($event['event_date'])) ?>
          <?php if ($timeStr): ?>
            · <i class="bi bi-clock ms-2 me-1"></i><?= $timeStr ?>
          <?php endif; ?>
        </span>
      <?php endif; ?>

      <?php if (!empty($event['location'])): ?>
        <span class="badge badge-brand">
          <i class="bi bi-geo-alt me-1"></i><?= esc($event['location']) ?>
        </span>
      <?php endif; ?>

      <?php if ($requiresReg): ?>
        <span class="badge <?= $isFull ? 'bg-danger' : 'bg-success' ?>">
          <i class="bi <?= $isFull ? 'bi-x-circle' : 'bi-check-circle' ?> me-1"></i>
          <?= $isFull ? 'Registration Closed' : 'Registration Open' ?>
        </span>
      <?php endif; ?>

    </div>

  </div>
</section>

<div class="container py-5">

  <!-- ===============================
     REGISTRATION PANEL
================================ -->
  <?php if ($requiresReg): ?>
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-body text-center">

        <?php if ($capacity > 0): ?>
          <div class="mb-3">
            <strong><?= $current ?></strong> of
            <strong><?= $capacity ?></strong> places taken
          </div>

          <div class="progress mb-3" style="height: 10px;">
            <div class="progress-bar <?= $isFull ? 'bg-danger' : 'bg-brand' ?>" role="progressbar"
              style="width: <?= min(100, ($current / $capacity) * 100) ?>%">
            </div>
          </div>
        <?php endif; ?>

        <?php if ($isFull): ?>
          <div class="alert alert-danger mb-0">
            <i class="bi bi-x-octagon-fill me-2"></i>
            This event is now fully booked.
          </div>
        <?php else: ?>
          <?php if ($spotsLeft !== null): ?>
            <p class="mb-3">
              <strong><?= $spotsLeft ?></strong> spots remaining
            </p>
          <?php endif; ?>

          <a href="<?= site_url('events/register/' . esc($event['slug'])) ?>" class="btn btn-lg btn-brand shadow-sm">
            <i class="bi bi-pencil-square me-2"></i>
            Register Now
          </a>
        <?php endif; ?>

      </div>
    </div>
  <?php endif; ?>


  <?php
  $img = !empty($event['image']) ? $event['image'] : '';
  if (!$img || !is_file(FCPATH . $img)) {
    $img = 'assets/img/lcnl-placeholder-320.png';
  }
  $modalId = 'eventImageModal';
  ?>

  <!-- Description + Image -->
  <div class="border-0 lcnl-card mb-4">
    <div class="card-body">
      <div class="row g-4 align-items-start">

        <div class="col">
          <h4 class="mb-3">
            <i class="bi bi-info-circle me-2"></i>About this event
          </h4>

          <?= !empty($event['description'])
            ? nl2br(esc($event['description']))
            : '<p class="text-muted">More details coming soon.</p>' ?>
        </div>

        <div class="col-md-auto order-md-last">
          <a href="#" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>">
            <div style="width:250px; max-width:100%;">
              <img src="<?= base_url($img) ?>" class="img-fluid rounded">
            </div>
          </a>
        </div>

      </div>
    </div>
  </div>

  <!-- Terms -->
  <div class="border-0 lcnl-card mb-4">
    <div class="card-body">
      <h4 class="mb-3">
        <i class="bi bi-file-earmark-text me-2"></i>Event Terms
      </h4>

      <?= !empty($event['eventterms'])
        ? nl2br(esc($event['eventterms']))
        : '<p class="text-muted">No special terms provided.</p>' ?>
    </div>
  </div>

  <!-- Ticket + Contact -->
  <div class="row g-4">

    <div class="col-lg-6">
      <div class="border-0 lcnl-card mb-4">
        <div class="card-body">
          <h4 class="mb-3">
            <i class="bi bi-ticket-perforated me-2"></i>Ticket Information
          </h4>

          <?= !empty($event['ticketinfo'])
            ? nl2br($event['ticketinfo'])
            : '<p class="text-muted">Ticket details will be announced.</p>' ?>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="border-0 lcnl-card mb-4">
        <div class="card-body">
          <h4 class="mb-3">
            <i class="bi bi-telephone-inbound me-2"></i>Contact Information
          </h4>

          <?= !empty($event['contactinfo'])
            ? nl2br(esc($event['contactinfo']))
            : '<p class="text-muted">For queries email <a href="mailto:info@lcnl.org">info@lcnl.org</a>.</p>' ?>
        </div>
      </div>
    </div>

  </div>

  <!-- Back -->
  <div class="mt-4">
    <a href="<?= base_url('events') ?>" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Back to Events
    </a>
  </div>

</div>

<!-- Image Modal -->
<div class="modal fade" id="<?= $modalId ?>" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0">
      <div class="modal-body p-0">
        <img src="<?= base_url($img) ?>" class="img-fluid w-100">
      </div>
      <div class="modal-footer justify-content-between">
        <span class="text-muted small"><?= esc($event['title']) ?></span>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

