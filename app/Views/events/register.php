<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-1">
      <i class="bi bi-journal-check me-2"></i>
      <?= esc($event['title']) ?>
    </h1>
    <p class="mb-0 opacity-75">Please complete the form below to register</p>
  </div>
</section>

<div class="container py-5">

  <?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger shadow-sm alert-dismissible fade show">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <strong>Error:</strong>
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
            Register for <?= esc($event['title']) ?>
          </h3>

          <?php if ($capacity > 0): ?>
            <p class="mb-0 mt-1 small opacity-90">
              <?= $currentTotal ?> / <?= $capacity ?> registered
            </p>
          <?php endif; ?>
        </div>

        <div class="card-body p-4 p-md-5">

          <?php if ($isMember): ?>
            <div class="alert alert-success small">
              <i class="bi bi-patch-check-fill me-2"></i>
              You are logged in as an LCNL member. Your details are pre-filled.
            </div>
          <?php endif; ?>

          <?php if ($isFull): ?>

            <div class="alert alert-danger text-center shadow-sm">
              <i class="bi bi-x-octagon-fill me-2"></i>
              <strong>Registrations Closed</strong>
              <p class="mb-0 mt-2">
                Sorry, this event has reached maximum capacity.
              </p>
            </div>

          <?php else: ?>

<form method="post" action="<?= site_url('events/register/submit') ?>" novalidate>
  <?= csrf_field() ?>

  <input type="hidden" name="event_id" value="<?= esc($event['id']) ?>">
  <input type="hidden" name="form_time" value="<?= time() ?>">
  <input type="hidden" name="form_token" value="<?= esc($formToken) ?>">

  <!-- Personal Details -->
  <div class="form-section mb-4">
    <h5 class="section-title mb-3">
      <i class="bi bi-person-fill me-2"></i>Your Details
    </h5>

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold">First Name *</label>
        <input 
          name="first_name" 
          class="form-control"
          value="<?= old('first_name', $memberData['first_name'] ?? '') ?>" 
          required>
      </div>

      <div class="col-md-6">
        <label class="form-label fw-semibold">Surname *</label>
        <input 
          name="last_name" 
          class="form-control"
          value="<?= old('last_name', $memberData['last_name'] ?? '') ?>" 
          required>
      </div>

      <div class="col-md-6">
        <label class="form-label fw-semibold">Email *</label>
        <input 
          type="email" 
          name="email" 
          class="form-control"
          value="<?= old('email', $memberData['email'] ?? '') ?>"
          <?= $isMember ? 'readonly' : '' ?>
          required>
      </div>

      <div class="col-md-6">
        <label class="form-label fw-semibold">Phone *</label>
        <input 
          name="phone" 
          class="form-control"
          value="<?= old('phone', $memberData['phone'] ?? '') ?>" 
          required>
      </div>
    </div>
  </div>

  <!-- Attendance -->
  <div class="form-section mb-4">
    <h5 class="section-title mb-3">
      <i class="bi bi-people-fill me-2"></i>Attendance Details
    </h5>

    <input type="hidden" name="num_participants" value="1">

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold">
          Number of guests
        </label>

        <select name="num_guests" class="form-select">
          <?php for ($i = 0; $i <= 10; $i++): ?>
            <option value="<?= $i ?>" <?= (string) old('num_guests', 0) === (string) $i ? 'selected' : '' ?>>
              <?= $i ?>
            </option>
          <?php endfor; ?>
        </select>
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
      <input 
        class="form-check-input" 
        type="checkbox" 
        name="agreed_terms" 
        value="1" 
        <?= old('agreed_terms') ? 'checked' : '' ?>
        required>
      <label class="form-check-label fw-semibold">
        I agree to the event terms.
      </label>
    </div>
  </div>

  <button type="submit" class="btn btn-accent btn-lg w-100">
    Submit Registration
  </button>

</form>


          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

