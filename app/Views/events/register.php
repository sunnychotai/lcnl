<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero -->
<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-1">
      <i class="bi bi-journal-check me-2"></i>
      <?= esc(old('event_name', $selectedEvent) ?? 'Event Registration') ?>
    </h1>
    <p class="mb-0 opacity-75">Please complete the form below to register</p>
  </div>
</section>

<div class="container py-5">

  <!-- Flash success -->
  <?php if ($msg = session()->getFlashdata('message')): ?>
    <div class="alert alert-success shadow-sm alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i><?= esc($msg) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Validation errors -->
  <?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger shadow-sm alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <strong>Please fix the following issues:</strong>
      <ul class="mb-0 mt-2">
        <?php foreach ($errors as $err): ?>
          <li><?= esc($err) ?></li>
        <?php endforeach; ?>
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

      <div class="card shadow-lg border-0 auth-card">

        <!-- Card header -->
        <div class="card-header bg-gradient py-3">
          <h3 class="mb-0 d-flex align-items-center">
            <i class="bi bi-calendar-check me-2"></i>
            Register for an Event
          </h3>
          <p class="mb-0 mt-1 small opacity-90">
            Fields marked with <span class="text-danger">*</span> are required
          </p>
        </div>

        <!-- Card body -->
        <div class="card-body p-4 p-md-5">

          <?php if ($isMember): ?>
            <div class="alert alert-info">
              <i class="bi bi-info-circle me-2"></i>
              Your details are pre-filled from your membership record.
              You may update them for this registration if needed.
            </div>
          <?php endif; ?>

          <form method="post" action="<?= site_url('events/register/submit') ?>" novalidate>
            <?= csrf_field() ?>

            <!-- Event selection -->
            <div class="form-section mb-4">
              <h5 class="section-title mb-3">
                <i class="bi bi-calendar-event me-2"></i>Event Details
              </h5>

              <label class="form-label fw-semibold">
                Select Event <span class="text-danger">*</span>
              </label>
              <select name="event_name" class="form-select" required>
                <option value="" disabled <?= old('event_name', $selectedEvent) ? '' : 'selected' ?>>
                  — Select an event —
                </option>
                <?php
                // TODO: Replace with DB-driven events table
                $events = [
                  'New Year Bhajans 2026',
                ];
                foreach ($events as $event):
                ?>
                  <option value="<?= esc($event) ?>"
                    <?= old('event_name', $selectedEvent) === $event ? 'selected' : '' ?>>
                    <?= esc($event) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Personal info -->
            <div class="form-section mb-4">
              <h5 class="section-title mb-3">
                <i class="bi bi-person-fill me-2"></i>Your Details
              </h5>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">
                    First Name <span class="text-danger">*</span>
                  </label>
                  <input
                    name="first_name"
                    class="form-control"
                    value="<?= old('first_name', $isMember ? session()->get('member_first_name') : '') ?>"
                    required>
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">
                    Surname <span class="text-danger">*</span>
                  </label>
                  <input
                    name="last_name"
                    class="form-control"
                    value="<?= old('last_name', $isMember ? session()->get('member_last_name') : '') ?>"
                    required>
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">
                    Email <span class="text-danger">*</span>
                  </label>
                  <input
                    type="email"
                    name="email"
                    class="form-control"
                    value="<?= old('email', $memberEmail ?? '') ?>"
                    required>
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">
                    Phone <span class="text-danger">*</span>
                  </label>
                  <input
                    name="phone"
                    class="form-control"
                    value="<?= old('phone', $isMember ? session()->get('member_phone') : '') ?>"
                    required>
                </div>
              </div>
            </div>

            <!-- Participation -->
            <div class="form-section mb-4">
              <h5 class="section-title mb-3">
                <i class="bi bi-people-fill me-2"></i>Attendance
              </h5>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">
                    Number of Participants <span class="text-danger">*</span>
                  </label>
                  <input
                    type="number"
                    name="num_participants"
                    min="1"
                    max="10"
                    class="form-control"
                    value="<?= old('num_participants', 1) ?>"
                    required>
                </div>
              </div>

              <!-- Guests hidden for now -->
              <input type="hidden" name="num_guests" value="0">
            </div>

            <!-- Notes -->
            <div class="form-section mb-4">
              <label class="form-label fw-semibold">Notes (optional)</label>
              <textarea
                name="notes"
                class="form-control"
                rows="3"><?= esc(old('notes') ?? '') ?></textarea>
            </div>

            <!-- Submit -->
            <div class="d-grid gap-2">
              <button class="btn btn-accent btn-lg rounded-pill shadow-sm">
                <i class="bi bi-check2-circle me-2"></i>
                Submit Registration
              </button>
            </div>

          </form>
        </div>

        <!-- Footer -->
        <div class="card-footer bg-light text-center py-3">
          <a href="<?= base_url('/') ?>" class="btn btn-link text-decoration-none">
            <i class="bi bi-arrow-left-circle me-1"></i>Back to homepage
          </a>
        </div>

      </div>

    </div>
  </div>
</div>

<?= $this->endSection() ?>