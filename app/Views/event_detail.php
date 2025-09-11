<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">

  <!-- Title -->
  <div class="mb-4">
    <h1 class="fw-bold mb-1"><?= esc($event['title'] ?? 'Event') ?></h1>
    <?php if (!empty($event['date']) || !empty($event['location'])): ?>
      <div class="text-muted">
        <?php if (!empty($event['date'])): ?>
          <i class="bi bi-calendar-event me-1"></i><?= esc($event['date']) ?>
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

  <!-- Row 1: Image + Description -->
  <div class="row g-4 align-items-start">
    <div class="col-lg-5">
      <div class="card shadow-sm border-0">
        <?php
          // Resolve image URL (fallback if missing)
          $img = $event['image'] ?? '';
          if (!$img) {
              $img = 'assets/img/events/placeholder-event.jpg'; // adjust to your placeholder
          }
        ?>
        <img src="<?= base_url($img) ?>"
             class="card-img-top"
             alt="<?= esc($event['title'] ?? 'Event image') ?>">
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card shadow-sm border-0">
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
    <div class="card shadow-sm border-0">
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
      <div class="card shadow-sm border-0 h-100">
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
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <h4 class="mb-3">
            <i class="bi bi-telephone-inbound me-2"></i>Contact Information
          </h4>

          <?php if (!empty($event['contactinfo'])): ?>
            <div class="fs-6">
              <?= nl2br(esc($event['contactinfo'])) ?>
            </div>
          <?php else: ?>
            <p class="text-muted mb-0">For queries email <a href="mailto:info@lcnl.org">info@lcnl.org</a>.</p>
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

<?= $this->endSection() ?>
