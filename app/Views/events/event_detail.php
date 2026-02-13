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

  <?php
  $img = !empty($event['image']) ? $event['image'] : '';
  if (!$img || !is_file(FCPATH . $img)) {
    $img = 'assets/img/lcnl-placeholder-320.png';
  }
  $modalId = 'eventImageModal';
  ?>

  <!-- Description + Image -->
  <div class="lcnl-card border-0 shadow-sm mb-4 overflow-hidden">
    <div class="card-body p-4 p-md-5">
      <div class="row g-4 align-items-start">

        <div class="col-lg-8">
          <div class="mb-4">
            <h2 class="mb-3 d-flex align-items-center">
              <span
                class="bg-brand text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3"
                style="width: 40px; height: 40px;">
                <i class="bi bi-info-circle"></i>
              </span>
              About this event
            </h2>
          </div>

          <div class="event-description fs-5 lh-lg text-secondary">
            <?= !empty($event['description'])
              ? nl2br(esc($event['description']))
              : '<p class="text-muted fst-italic">More details coming soon.</p>' ?>
          </div>
        </div>

        <div class="col-lg-4 order-lg-last">
          <a href="#" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>"
            class="d-block position-relative event-image-wrapper">
            <img src="<?= base_url($img) ?>" class="img-fluid rounded-3 shadow-sm w-100"
              style="transition: transform 0.3s ease;">
            <div class="position-absolute top-0 end-0 m-3">
              <span class="badge bg-dark bg-opacity-75">
                <i class="bi bi-zoom-in me-1"></i> Click to enlarge
              </span>
            </div>
          </a>
        </div>

      </div>
    </div>
  </div>

  <!-- Terms -->
  <div class="lcnl-card border-0 shadow-sm mb-4">
    <div class="card-body p-4 p-md-5">
      <div class="mb-4">
        <h2 class="mb-3 d-flex align-items-center">
          <span class="bg-brand text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3"
            style="width: 40px; height: 40px;">
            <i class="bi bi-file-earmark-text"></i>
          </span>
          Event Terms
        </h2>
      </div>

      <div class="fs-6 lh-lg text-secondary">
        <?= !empty($event['eventterms'])
          ? nl2br(esc($event['eventterms']))
          : '<p class="text-muted fst-italic">No special terms provided.</p>' ?>
      </div>
    </div>
  </div>

  <!-- Ticket + Contact -->
  <div class="row g-4 mb-4">

    <div class="col-lg-6">
      <div class="lcnl-card border-0 shadow-sm h-100">
        <div class="card-body p-4 p-md-5">
          <div class="mb-4">
            <h2 class="mb-3 d-flex align-items-center">
              <span
                class="bg-brand text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3"
                style="width: 40px; height: 40px;">
                <i class="bi bi-ticket-perforated"></i>
              </span>
              Ticket Information
            </h2>
          </div>

          <div class="fs-6 lh-lg text-secondary">
            <?= !empty($event['ticketinfo'])
              ? nl2br($event['ticketinfo'])
              : '<p class="text-muted fst-italic">Ticket details will be announced.</p>' ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="lcnl-card border-0 shadow-sm h-100">
        <div class="card-body p-4 p-md-5">
          <div class="mb-4">
            <h2 class="mb-3 d-flex align-items-center">
              <span
                class="bg-brand text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3"
                style="width: 40px; height: 40px;">
                <i class="bi bi-telephone-inbound"></i>
              </span>
              Contact Information
            </h2>
          </div>

          <div class="fs-6 lh-lg text-secondary">
            <?= !empty($event['contactinfo'])
              ? nl2br(esc($event['contactinfo']))
              : '<p class="text-muted fst-italic">For queries email <a href="mailto:info@lcnl.org" class="text-brand fw-semibold text-decoration-none">info@lcnl.org</a>.</p>' ?>
          </div>
        </div>
      </div>
    </div>

  </div>

  <?php
  // Registration flags
  $requiresReg = !empty($event['requires_registration']);
  $regOpen = !empty($event['registration_open']);

  // Limits (two types)
  $maxRegs = (int) ($event['max_registrations'] ?? 0); // registrations limit
  $maxHeads = (int) ($event['max_headcount'] ?? 0);     // headcount limit (regs + guests)
  
  // Current usage
  $currentRegs = (int) ($event['current_registrations'] ?? 0);
  $currentHeads = (int) ($event['current_headcount'] ?? 0);

  // Percentages (avoid division by zero)
  $regPercent = ($maxRegs > 0) ? min(100, round(($currentRegs / $maxRegs) * 100)) : null;
  $headPercent = ($maxHeads > 0) ? min(100, round(($currentHeads / $maxHeads) * 100)) : null;

  // Remaining
  $regsLeft = ($maxRegs > 0) ? max(0, $maxRegs - $currentRegs) : null;
  $headsLeft = ($maxHeads > 0) ? max(0, $maxHeads - $currentHeads) : null;

  // Full logic (either limit can close it)
  $isFull = !empty($event['is_full']);
  ?>

  <!-- ===============================
     REGISTRATION PANEL
================================ -->
  <?php if ($requiresReg): ?>
    <div class="lcnl-card shadow-sm border-0 mb-5 overflow-hidden">
      <div class="card-header bg-brand text-white py-4 border-0">
        <h2 class="mb-0 text-center text-white">
          <i class="bi bi-pencil-square me-2"></i>
          Event Registration
        </h2>
      </div>
      <div class="card-body p-4 p-md-5 text-center">

        <?php if (!$regOpen): ?>
          <div class="alert alert-secondary border-0 shadow-sm py-4 mb-0">
            <i class="bi bi-lock-fill me-2 fs-3 d-block mb-3"></i>
            <h5 class="mb-0">Registration is currently closed for this event.</h5>
          </div>

        <?php else: ?>

          <?php if ($maxRegs > 0): ?>
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Registrations</span>
                <span class="fw-bold fs-5">
                  <span class="text-brand"><?= $currentRegs ?></span>
                  <span class="text-muted">of</span>
                  <span><?= $maxRegs ?></span>
                </span>
              </div>
              <div class="progress shadow-sm" style="height: 16px;">
                <div class="progress-bar <?= $isFull ? 'bg-danger' : 'bg-brand' ?>" role="progressbar"
                  style="width: <?= $regPercent ?>%; transition: width 0.6s ease;" aria-valuenow="<?= $regPercent ?>"
                  aria-valuemin="0" aria-valuemax="100">
                  <span class="small fw-semibold"><?= $regPercent ?>%</span>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($maxHeads > 0): ?>
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Total Headcount</span>
                <span class="fw-bold fs-5">
                  <span class="text-brand"><?= $currentHeads ?></span>
                  <span class="text-muted">of</span>
                  <span><?= $maxHeads ?></span>
                </span>
              </div>
              <div class="progress shadow-sm" style="height: 16px;">
                <div class="progress-bar <?= $isFull ? 'bg-danger' : 'bg-brand' ?>" role="progressbar"
                  style="width: <?= $headPercent ?>%; transition: width 0.6s ease;" aria-valuenow="<?= $headPercent ?>"
                  aria-valuemin="0" aria-valuemax="100">
                  <span class="small fw-semibold"><?= $headPercent ?>%</span>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($isFull): ?>
            <div class="alert alert-danger border-0 shadow-sm py-4 mb-0">
              <i class="bi bi-x-octagon-fill fs-3 d-block mb-3"></i>
              <h5 class="mb-0">This event is now fully booked.</h5>
            </div>
          <?php else: ?>

            <?php if ($regsLeft !== null || $headsLeft !== null): ?>
              <div class="mb-4">
                <?php if ($regsLeft !== null): ?>
                  <span class="badge bg-light text-dark border border-2 border-brand me-2 px-3 py-2 fs-6">
                    <i class="bi bi-person-check me-2"></i><?= $regsLeft ?> registration<?= $regsLeft !== 1 ? 's' : '' ?> left
                  </span>
                <?php endif; ?>
                <?php if ($headsLeft !== null): ?>
                  <span class="badge bg-light text-dark border border-2 border-brand px-3 py-2 fs-6">
                    <i class="bi bi-people me-2"></i><?= $headsLeft ?> seat<?= $headsLeft !== 1 ? 's' : '' ?> left
                  </span>
                <?php endif; ?>
              </div>
            <?php endif; ?>

            <a href="<?= site_url('events/register/' . ($event['slug'] ?? '')) ?>"
              class="btn btn-lg btn-brand shadow px-5 py-3 fs-5" style="transition: all 0.3s ease;">
              <i class="bi bi-pencil-square me-2"></i>
              Register Now
            </a>

          <?php endif; ?>

        <?php endif; ?>

      </div>
    </div>
  <?php endif; ?>

  <!-- Back -->
  <div class="mt-5 text-center text-md-start">
    <a href="<?= base_url('events') ?>" class="btn btn-outline-secondary btn-lg px-4">
      <i class="bi bi-arrow-left me-2"></i> Back to Events
    </a>
  </div>

</div>

<!-- Image Modal -->
<div class="modal fade" id="<?= $modalId ?>" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-body p-0">
        <img src="<?= base_url($img) ?>" class="img-fluid w-100">
      </div>
      <div class="modal-footer justify-content-between py-3">
        <span class="text-muted"><?= esc($event['title']) ?></span>
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
          <i class="bi bi-x-lg me-2"></i>Close
        </button>
      </div>
    </div>
  </div>
</div>

<style>
  .event-image-wrapper:hover img {
    transform: scale(1.02);
  }

  .btn-brand:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
  }

  .event-description p {
    margin-bottom: 1rem;
  }

  .lcnl-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .lcnl-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
  }
</style>

<?= $this->endSection() ?>

