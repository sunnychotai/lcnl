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

  <!-- Validation errors -->
  <?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger shadow-sm alert-dismissible fade show">
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

        <div class="card-header bg-gradient py-3">
          <h3 class="mb-0">
            <i class="bi bi-calendar-check me-2"></i>
            Register for an Event
          </h3>
          <p class="mb-0 mt-1 small opacity-90">
            Fields marked with <span class="text-danger">*</span> are required
          </p>
        </div>

        <div class="card-body p-4 p-md-5">

          <?php if ($isMember): ?>
            <div class="alert alert-success small">
              <i class="bi bi-patch-check-fill me-2"></i>
              You are logged in as an LCNL member. Your details are pre-filled.
            </div>
          <?php endif; ?>

          <form method="post" action="<?= site_url('events/register/submit') ?>" id="registrationForm" novalidate>
            <?= csrf_field() ?>

            <!-- Honeypot -->
            <div style="position:absolute; left:-5000px">
              <input type="text" name="website">
              <input type="text" name="company">
            </div>

            <input type="hidden" name="form_time" value="<?= time() ?>">
            <input type="hidden" name="form_token" value="<?= $formToken ?>">
            <input type="hidden" name="field_order" id="fieldOrder">

            <!-- Event -->
            <div class="form-section mb-4">
              <h5 class="section-title mb-3">
                <i class="bi bi-calendar-event me-2"></i>Event Details
              </h5>

              <label class="form-label fw-semibold">
                Select Event <span class="text-danger">*</span>
              </label>

              <select name="event_name" class="form-select" required>
                <option value="" disabled selected>— Select an event —</option>
                <?php foreach (['Maha Shivratri 2026'] as $event): ?>
                  <option value="<?= esc($event) ?>" <?= old('event_name', $selectedEvent) === $event ? 'selected' : '' ?>>
                    <?= esc($event) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Membership -->
            <?php if ($isMember): ?>
              <input type="hidden" name="is_lcnl_member" value="1">
            <?php else: ?>
              <div class="form-section mb-4">
                <h5 class="section-title mb-3">
                  <i class="bi bi-person-badge-fill me-2"></i>Membership
                </h5>

                <label class="form-label fw-semibold">
                  Are you an LCNL member? <span class="text-danger">*</span>
                </label>

                <div class="d-flex gap-4">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="is_lcnl_member" value="1"
                      <?= old('is_lcnl_member') === '1' ? 'checked' : '' ?> required>
                    <label class="form-check-label">Yes</label>
                  </div>

                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="is_lcnl_member" value="0"
                      <?= old('is_lcnl_member') === '0' ? 'checked' : '' ?> required>
                    <label class="form-check-label">No</label>
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <!-- Personal details -->
            <div class="form-section mb-4">
              <h5 class="section-title mb-3">
                <i class="bi bi-person-fill me-2"></i>Your Details
              </h5>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">First Name *</label>
                  <input name="first_name" class="form-control"
                    value="<?= old('first_name', $isMember ? session()->get('member_first_name') : '') ?>" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">Surname *</label>
                  <input name="last_name" class="form-control"
                    value="<?= old('last_name', $isMember ? session()->get('member_last_name') : '') ?>" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">Email *</label>
                  <input type="email" name="email" class="form-control" value="<?= old('email', $memberEmail ?? '') ?>"
                    required>
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">Phone *</label>
                  <input name="phone" class="form-control"
                    value="<?= old('phone', $isMember ? session()->get('member_phone') : '') ?>" required>
                </div>
              </div>
            </div>

            <!-- Attendance -->
            <div class="form-section mb-4">
              <h5 class="section-title mb-3">
                <i class="bi bi-people-fill me-2"></i>Attendance Details
              </h5>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">
                    Number of participants (Pooja performers) <span class="text-danger">*</span>
                  </label>
                  <input type="number" name="num_participants" min="1" max="2" class="form-control"
                    value="<?= old('num_participants', 1) ?>" required>
                  <small class="text-muted">
                    Maximum of 2 people are allowed to perform the pooja
                  </small>
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">
                    Number of guests (Observers)
                  </label>
                  <input type="number" name="num_guests" min="0" max="10" class="form-control"
                    value="<?= old('num_guests', 0) ?>">
                  <small class="text-muted">
                    Guests will be seated around the room and will not sit at the pooja table
                  </small>
                </div>
              </div>
            </div>

            <!-- Notes -->
            <div class="form-section mb-4">
              <label class="form-label fw-semibold">Notes (optional)</label>
              <textarea name="notes" class="form-control" rows="3"><?= esc(old('notes')) ?></textarea>
            </div>

            <!-- Terms -->
            <div class="form-section mb-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="agreed_terms" value="1" id="agreeTerms" required>
                <label class="form-check-label fw-semibold" for="agreeTerms">
                  I understand that only two people may perform the pooja and all others will observe from seating
                  areas.
                  <span class="text-danger">*</span>
                </label>
              </div>
            </div>

            <!-- Human check -->
            <div class="form-section mb-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="humanConfirm" name="human_confirm" required>
                <label class="form-check-label fw-semibold">
                  I confirm I am a real person <span class="text-danger">*</span>
                </label>
              </div>
            </div>

            <button type="submit" id="submitBtn" class="btn btn-accent btn-lg w-100" disabled>
              <span id="submitText">Please wait <span id="countdown">3</span> seconds…</span>
              <span id="submitReady" style="display:none">Submit Registration</span>
            </button>

          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
  (function () {
    const btn = document.getElementById('submitBtn');
    const countdown = document.getElementById('countdown');
    const submitText = document.getElementById('submitText');
    const submitReady = document.getElementById('submitReady');
    let c = 3;

    const t = setInterval(() => {
      c--;
      countdown.textContent = c;
      if (c <= 0) {
        clearInterval(t);
        btn.disabled = false;
        submitText.style.display = 'none';
        submitReady.style.display = 'inline';
      }
    }, 1000);
  })();
</script>

<?= $this->endSection() ?>

