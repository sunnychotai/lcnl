<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

  <!-- Title -->
  <div class="mb-4">
    <h1 class="fw-bold mb-1"><?= esc($event['title'] ?? 'Event') ?></h1>
    <?php if (!empty($event['event_date']) || !empty($event['location'])): ?>
      <div class="text-muted">
        <?php if (!empty($event['event_date'])): ?>
          <i class="bi bi-calendar-event me-1"></i><?= esc($event['event_date']) ?>
        <?php endif; ?>
        <?php if (!empty($event['time'])): ?>
          · <i class="bi bi-clock me-1"></i><?= esc($event['time']) ?>
        <?php endif; ?>
        <?php if (!empty($event['location'])): ?>
          · <i class="bi bi-geo-alt me-1"></i><?= esc($event['location']) ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

  <?php
    // Resolve image (with fallback)
    $img = $event['image'] ?? '';
    if (! $img) {
        $img = 'assets/img/events/placeholder-event.jpg'; // adjust if different
    }
    $modalId = 'eventImageModal';
  ?>

  <!-- Row 1: Small image + Description -->
  <div class="row g-4 align-items-start">
    <div class="col-auto">
      <div class="card shadow-sm border-0" title="Click to enlarge">
        <a href="#" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>">
          <div class="event-img-wrapper" style="width:250px;">
            <img
              src="<?= base_url($img) ?>"
              class="img-fluid rounded-top"
              alt="<?= esc($event['title'] ?? 'Event image') ?>"
            >
          </div>
        </a>
      </div>
    </div>

    <div class="col-lg-8 col-md-7">
      <div class="card shadow-sm border-0 no-hover">
        <div class="card-body">
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

  <!-- Row 2: Full-width Terms -->
  <div class="mt-4">
    <div class="card shadow-sm border-0 no-hover">
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
  </div>

  <!-- Row 3: Ticket Info (left) + Contact Info (right) -->
  <div class="row g-4 mt-1">
    <div class="col-lg-6">
      <div class="card shadow-sm border-0 no-hover h-100">
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
      <div class="card shadow-sm border-0 no-hover h-100">
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

<!-- Image Modal (click-to-expand) -->
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
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          Close
        </button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
