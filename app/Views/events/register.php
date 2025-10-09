<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero -->
<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-1"><i class="bi bi-journal-check me-2"></i>Chopda Pujan Registration</h1>
    <p class="opacity-75 mb-0">Join us on 20 October 2025 @ 6PM at DLC</p>
  </div>
</section>

<!-- Registration Form -->
<div class="container py-5">
  <?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger shadow-sm">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>Please correct the highlighted errors.
      <ul class="mb-0 mt-2"><?php foreach($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <div class="row justify-content-center g-4">
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
                  <?php for($i=0; $i<=5; $i++): ?>
                    <option value="<?= $i ?>" <?= old('num_guests') == $i ? 'selected' : '' ?>><?= $i ?></option>
                  <?php endfor; ?>
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

    <!-- Right: Event Info + Next Steps -->
    <div class="col-lg-5">
      <div class="card shadow-sm border-0 h-100 no-hover">
        <div class="card-header bg-brand text-white">
          <h5 class="mb-0"><i class="bi bi-info-circle-fill me-2"></i>Next Steps & Event Terms</h5>
        </div>
        <div class="card-body p-4">
          <p class="mb-3">Once you have submitted your registration, please follow the steps below to complete your booking:</p>

          <ol class="mb-4 ps-3">
            <li>Make a payment of <strong>£25 per pooja</strong> to LCNL (details below).</li>
            <li>Use the payment reference format: <strong>CP–FirstnameSurname</strong></li>
            <li>Once payment is received, your registration will be confirmed by email.</li>
          </ol>

          <div class="bg-light border rounded-3 p-3 mb-4">
            <h6 class="fw-bold mb-2"><i class="bi bi-bank me-2 text-accent"></i>Bank Details</h6>
            <p class="mb-1"><strong>Account Name:</strong> Lohana Community North London</p>
            <p class="mb-1"><strong>Sort Code:</strong> 40-23-13</p>
            <p class="mb-1"><strong>Account Number:</strong> 2149 7995</p>
          </div>

          <h6 class="fw-bold mb-2"><i class="bi bi-list-check me-2 text-accent"></i>Event Terms</h6>
          <ul class="small mb-3 ps-3">
            <li>All pooja samagri will be provided — please bring your own <strong>book</strong> and <strong>swaroop of Lord Ganesh</strong>.</li>
            <li>Each pooja is priced at <strong>£25 per Yajman</strong> (maximum of 2 Yajmans per pooja).</li>
            <li>Guests are welcome to attend and observe the ceremony from the side seating area.</li>
            <li>Registration must be completed online prior to the event.</li>
            <li>Payments must be made in advance to confirm your place.</li>
          </ul>

          <div class="alert alert-warning small mb-0">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Please note: spaces are limited and confirmed only upon receipt of payment.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .auth-card { border-left: 6px solid var(--brand); border-radius: var(--radius); }
  .card-header.bg-accent1 { background-color: var(--accent1); }
  .card-header.bg-brand { background-color: var(--brand); }
</style>

<?= $this->endSection() ?>
