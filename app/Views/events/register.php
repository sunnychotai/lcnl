<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-1"><i class="bi bi-journal-check me-2"></i>Chopda Pujan Registration</h1>
    <p class="opacity-75 mb-0">Join us on 20 October @ 6pm at DLC</p>
  </div>
</section>

<div class="container py-4">
  <?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger shadow-sm">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>Please correct the highlighted errors.
      <ul class="mb-0 mt-2"><?php foreach($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="card shadow-sm border-0 auth-card no-hover">
        <div class="card-header bg-accent1 text-white">
          <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Register Now</h4>
        </div>
        <div class="card-body p-4">
          <form method="post" action="<?= base_url('events/register/submit') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
              <div class="col-sm-6">
                <label class="form-label fw-semibold">First Name*</label>
                <input type="text" name="first_name" value="<?= old('first_name') ?>" class="form-control" required>
              </div>
              <div class="col-sm-6">
                <label class="form-label fw-semibold">Surname*</label>
                <input type="text" name="last_name" value="<?= old('last_name') ?>" class="form-control" required>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Email*</label>
                <input type="email" name="email" value="<?= old('email') ?>" class="form-control" required>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Phone Number*</label>
                <input type="tel" name="phone" value="<?= old('phone') ?>" class="form-control" required placeholder="+44">
              </div>
              <div class="col-sm-6">
  <label class="form-label fw-semibold">No. of Participants (Max 2)*</label>
  <select name="num_participants" class="form-select" required>
    <option value="1" <?= old('num_participants') == 1 ? 'selected' : '' ?>>1</option>
    <option value="2" <?= old('num_participants') == 2 ? 'selected' : '' ?>>2</option>
  </select>
</div>

<div class="col-sm-6">
  <label class="form-label fw-semibold">No. of Guests</label>
  <select name="num_guests" class="form-select">
    <option value="0" <?= old('num_guests') == 0 ? 'selected' : '' ?>>0</option>
    <option value="1" <?= old('num_guests') == 1 ? 'selected' : '' ?>>1</option>
    <option value="2" <?= old('num_guests') == 2 ? 'selected' : '' ?>>2</option>
    <option value="3" <?= old('num_guests') == 3 ? 'selected' : '' ?>>3</option>
    <option value="4" <?= old('num_guests') == 4 ? 'selected' : '' ?>>4</option>
    <option value="5" <?= old('num_guests') == 5 ? 'selected' : '' ?>>5</option>
  </select>
</div>

              <div class="col-12">
                <label class="form-label fw-semibold">Notes (optional)</label>
                <textarea name="notes" class="form-control" rows="3"><?= old('notes') ?></textarea>
              </div>
              <div class="col-12">
                <button class="btn btn-accent btn-lg w-100 rounded-pill">
                  <i class="bi bi-check2-circle me-2"></i>Submit Registration
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="text-center mt-3">
        <a href="<?= base_url('/') ?>" class="text-decoration-none">
          <i class="bi bi-arrow-left-circle me-1"></i>Back to Site
        </a>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
