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

          <form method="post" action="<?= site_url('events/register/submit') ?>" id="registrationForm" novalidate>
            <?= csrf_field() ?>

            <!-- Anti-spam measures (invisible to users) -->

            <!-- 1. Honeypot fields (bots will fill these) -->
            <div style="position: absolute; left: -5000px;" aria-hidden="true">
              <label>Please leave this field blank</label>
              <input type="text" name="website" value="" tabindex="-1" autocomplete="off">
            </div>

            <div style="position: absolute; left: -5000px;" aria-hidden="true">
              <label>Your company name</label>
              <input type="text" name="company" value="" tabindex="-1" autocomplete="off">
            </div>

            <!-- 2. Timestamp field (to detect too-fast submissions) -->
            <input type="hidden" name="form_time" id="formTime" value="<?= time() ?>">

            <!-- 3. Session token for double-submission prevention -->
            <input type="hidden" name="form_token" value="<?= $formToken ?>">

            <!-- 4. Field order validation (bots often submit in wrong order) -->
            <input type="hidden" name="field_order" id="fieldOrder" value="">

            <!-- Event selection -->
            <div class="form-section mb-4">
              <h5 class="section-title mb-3">
                <i class="bi bi-calendar-event me-2"></i>Event Details
              </h5>

              <label class="form-label fw-semibold">
                Select Event <span class="text-danger">*</span>
              </label>
              <select name="event_name" class="form-select" required data-field-order="1">
                <option value="" disabled <?= old('event_name', $selectedEvent) ? '' : 'selected' ?>>
                  — Select an event —
                </option>
                <?php
                $events = [
                  'New Year Bhajans 2026',
                ];
                foreach ($events as $event):
                  ?>
                  <option value="<?= esc($event) ?>" <?= old('event_name', $selectedEvent) === $event ? 'selected' : '' ?>>
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
                  <input name="first_name" class="form-control" data-field-order="2"
                    value="<?= old('first_name', $isMember ? session()->get('member_first_name') : '') ?>" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">
                    Surname <span class="text-danger">*</span>
                  </label>
                  <input name="last_name" class="form-control" data-field-order="3"
                    value="<?= old('last_name', $isMember ? session()->get('member_last_name') : '') ?>" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">
                    Email <span class="text-danger">*</span>
                  </label>
                  <input type="email" name="email" class="form-control" data-field-order="4"
                    value="<?= old('email', $memberEmail ?? '') ?>" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">
                    Phone <span class="text-danger">*</span>
                  </label>
                  <input name="phone" class="form-control" data-field-order="5"
                    value="<?= old('phone', $isMember ? session()->get('member_phone') : '') ?>" required>
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
                  <input type="number" name="num_participants" min="1" max="10" class="form-control"
                    data-field-order="6" value="<?= old('num_participants', 1) ?>" required>
                </div>
              </div>

              <input type="hidden" name="num_guests" value="0">
            </div>

            <!-- Notes -->
            <div class="form-section mb-4">
              <label class="form-label fw-semibold">Notes (optional)</label>
              <textarea name="notes" class="form-control" rows="3"
                data-field-order="7"><?= esc(old('notes') ?? '') ?></textarea>
            </div>

            <!-- Human confirmation checkbox (elderly-friendly) -->
            <div class="form-section mb-4">
              <div class="card border-2 border-primary bg-light">
                <div class="card-body py-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="human_confirm" id="humanConfirm" required
                      style="width: 1.5rem; height: 1.5rem;">
                    <label class="form-check-label fw-semibold ps-2" for="humanConfirm" style="font-size: 1.1rem;">
                      <i class="bi bi-person-check-fill text-primary me-2"></i>
                      I confirm that I am a real person submitting this form
                      <span class="text-danger">*</span>
                    </label>
                  </div>
                  <small class="text-muted d-block mt-2 ps-5">
                    Please tick this box to confirm you are human and not an automated system
                  </small>
                </div>
              </div>
            </div>

            <!-- Submit button - will be enabled after minimum time -->
            <div class="d-grid gap-2">
              <button type="submit" id="submitBtn" class="btn btn-accent btn-lg rounded-pill shadow-sm" disabled>
                <span id="submitText">
                  <i class="bi bi-hourglass-split me-2"></i>
                  Please wait <span id="countdown">3</span> seconds...
                </span>
                <span id="submitReady" style="display: none;">
                  <i class="bi bi-check2-circle me-2"></i>
                  Submit Registration
                </span>
              </button>
            </div>

            <!-- User-friendly message -->
            <div class="alert alert-info mt-3 small">
              <i class="bi bi-info-circle-fill me-2"></i>
              The submit button will activate in a moment. This helps us prevent automated spam submissions.
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

<script>
  // Anti-spam JavaScript measures
  (function () {
    'use strict';

    const form = document.getElementById('registrationForm');
    const submitBtn = document.getElementById('submitBtn');
    const countdownSpan = document.getElementById('countdown');
    const submitText = document.getElementById('submitText');
    const submitReady = document.getElementById('submitReady');

    // Track form load time
    const formLoadTime = Date.now();

    // Minimum time before submission (3 seconds)
    const MIN_TIME = 3000;

    // Track field interaction order
    const fieldOrder = [];

    // Countdown timer
    let countdown = 3;
    const timer = setInterval(function () {
      countdown--;
      countdownSpan.textContent = countdown;

      if (countdown <= 0) {
        clearInterval(timer);
        enableSubmitButton();
      }
    }, 1000);

    function enableSubmitButton() {
      submitBtn.disabled = false;
      submitText.style.display = 'none';
      submitReady.style.display = 'inline';
      submitBtn.classList.remove('btn-secondary');
      submitBtn.classList.add('btn-accent');
    }

    // Track field interaction order (helps detect bots)
    document.querySelectorAll('[data-field-order]').forEach(function (field) {
      field.addEventListener('focus', function () {
        const order = this.getAttribute('data-field-order');
        if (!fieldOrder.includes(order)) {
          fieldOrder.push(order);
        }
      });
    });

    // Validate on submit
    form.addEventListener('submit', function (e) {
      const now = Date.now();
      const timeTaken = now - formLoadTime;

      // Check 1: Was form filled too quickly? (less than 3 seconds)
      if (timeTaken < MIN_TIME) {
        e.preventDefault();
        alert('Please take your time to fill out the form carefully.');
        return false;
      }

      // Check 2: Was form filled suspiciously quickly? (less than 2 seconds total)
      if (timeTaken < 2000) {
        e.preventDefault();
        console.log('Bot detected: too fast');
        return false;
      }

      // Check 3: Human confirmation checkbox
      const humanConfirm = document.getElementById('humanConfirm');
      if (!humanConfirm.checked) {
        e.preventDefault();
        alert('Please confirm that you are a real person by checking the box.');
        humanConfirm.focus();
        return false;
      }

      // Check 4: Field order tracking (optional, helps detect automated fills)
      document.getElementById('fieldOrder').value = fieldOrder.join(',');

      // Check 5: Honeypot fields should be empty
      const website = document.querySelector('input[name="website"]').value;
      const company = document.querySelector('input[name="company"]').value;

      if (website || company) {
        e.preventDefault();
        console.log('Bot detected: honeypot filled');
        return false;
      }

      // All checks passed - disable button to prevent double submission
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';

      return true;
    });

    // Detect if JavaScript is disabled (store in hidden field)
    const jsEnabled = document.createElement('input');
    jsEnabled.type = 'hidden';
    jsEnabled.name = 'js_enabled';
    jsEnabled.value = '1';
    form.appendChild(jsEnabled);

  })();
</script>

<?= $this->endSection() ?>

