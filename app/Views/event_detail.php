<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<div class="hero hero-rangoli-grey d-flex align-items-center justify-content-center">
  <div class="overlay"></div>
  <div class="container position-relative text-center text-white">

    <h1 class="fw-bold mb-2"><?= esc($event['title'] ?? 'Event') ?></h1>

    <?php
      // Format time range from time_from/time_to (24h format; change to 'g:ia' for 12h)
      $timeStr = '';
      $tf = !empty($event['time_from']) ? date('H:i', strtotime($event['time_from'])) : '';
      $tt = !empty($event['time_to'])   ? date('H:i', strtotime($event['time_to']))   : '';
      if ($tf || $tt) {
        $timeStr = $tf . ($tt ? '–' . $tt : '');
      }
    ?>
    <h5>
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
    </div>
</h4>
  </div>
</div>


<div class="container py-5">

  <?php
    // Resolve image (fallback)
    $img = $event['image'] ?? '';
    if (! $img) {
        $img = 'assets/img/lcnl-placeholder-320.png'; // adjust if different
    }
    $modalId = 'eventImageModal';
  ?>

  <!-- Row 1: Image + Description -->
  <div class="card shadow-sm border-0 no-hover colourful-card mb-4">
    <div class="card-body">
      <div class="row g-4 align-items-start">
        <!-- Image -->
        <div class="col-md-auto">
          <a href="#" class="d-inline-block" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>" title="Click to enlarge">
            <div class="event-img-wrapper" style="width:250px; max-width:100%;">
              <img
                src="<?= base_url($img) ?>"
                class="img-fluid rounded"
                alt="<?= esc($event['title'] ?? 'Event image') ?>"
              >
            </div>
          </a>
        </div>

        <!-- Description -->
        <div class="col">
          <h4 class="mb-3">
            <i class="bi bi-info-circle me-2"></i>About this event
          </h4>

          <?php if (!empty($event['description'])): ?>
            <div class="fs-6">
              <?= nl2br(esc($event['description'])) ?>
            </div>
          <?php else: ?>
            <p class="text-muted mb-0">More details coming soon.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Row 2: Terms -->
  <div class="card shadow-sm border-0 no-hover colourful-card mb-4">
    <div class="card-body">
      <h4 class="mb-3">
        <i class="bi bi-file-earmark-text me-2"></i>Event Terms
      </h4>

      <?php if (!empty($event['eventterms'])): ?>
        <div class="fs-6">
          <?= nl2br(esc($event['eventterms'])) ?>
        </div>
      <?php else: ?>
        <p class="text-muted mb-0">No special terms provided.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Row 3: Tickets + Contact -->
  <div class="row g-4">
    <div class="col-lg-6">
      <div class="card shadow-sm border-0 no-hover colourful-card mb-4">
        <div class="card-body">
          <h4 class="mb-3">
            <i class="bi bi-ticket-perforated me-2"></i>Ticket Information
          </h4>

          <?php if (!empty($event['ticketinfo'])): ?>
            <div class="fs-6">
              <?= nl2br(esc($event['ticketinfo'])) ?>
            </div>
          <?php else: ?>
            <p class="text-muted mb-0">Ticket details will be announced.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card shadow-sm border-0 no-hover colourful-card mb-4">
        <div class="card-body">
          <h4 class="mb-3">
            <i class="bi bi-telephone-inbound me-2"></i>Contact Information
          </h4>

          <?php if (!empty($event['contactinfo'])): ?>
            <div class="fs-6">
              <?= nl2br(esc($event['contactinfo'])) ?>
            </div>
          <?php else: ?>
            <p class="text-muted mb-0">
              For queries email <a href="mailto:info@lcnl.org">info@lcnl.org</a>.
            </p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Back link -->
  <div class="mt-4">
    <a href="<?= base_url('events') ?>" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Back to Events
    </a>
  </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0">
      <div class="modal-body p-0">
        <img
          src="<?= base_url($img) ?>"
          class="img-fluid w-100"
          alt="<?= esc($event['title'] ?? 'Event image enlarged') ?>"
        >
      </div>
      <div class="modal-footer justify-content-between">
        <span class="text-muted small"><?= esc($event['title'] ?? 'Event image') ?></span>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
