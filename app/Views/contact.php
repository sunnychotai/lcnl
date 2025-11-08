<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Add the enhanced CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/lcnl-contact.css') ?>">

<!-- =========================
     HERO
     ========================= -->
<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center text-center text-white py-5">
  <div class="container position-relative">
    <h1 class="fw-bold display-5 mb-3">
      <i class="bi bi-envelope-paper-heart me-2"></i> Contact Us
    </h1>
    <p class="lead fs-5 opacity-90 mb-0">We'd love to hear from you — get in touch below.</p>
  </div>
</section>

<!-- =========================
     MAIN CONTENT
     ========================= -->
<div class="container py-5">

  <!-- Alerts -->
  <?php if ($success = session()->getFlashdata('success')): ?>
    <div class="alert alert-success border-0 shadow-soft mb-4 d-flex align-items-center">
      <i class="bi bi-check-circle-fill fs-5 me-2"></i>
      <div><?= esc($success) ?></div>
    </div>
  <?php endif; ?>

  <?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger border-0 shadow-soft mb-4">
      <div class="d-flex">
        <i class="bi bi-exclamation-triangle-fill fs-5 me-2 mt-1"></i>
        <div>
          <strong class="d-block mb-1">Please fix the following:</strong>
          <ul class="ps-3 mb-0 small">
            <?php foreach ((array)$errors as $err): ?>
              <li><?= esc($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div class="row g-5 align-items-stretch">

    <!-- LEFT: CONTACT FORM -->
    <div class="col-lg-7">
      <div class="lcnl-card border-0 p-4 h-100 shadow-soft">
        <h3 class="fw-bold mb-4">
          <i class="bi bi-send-fill me-2 text-brand"></i> Send us a message
        </h3>

        <form action="<?= base_url('contact/send') ?>" method="post" class="needs-validation" novalidate>
          <?= csrf_field() ?>

          <!-- Honeypot -->
          <div class="d-none" aria-hidden="true">
            <label>Leave this field empty</label>
            <input type="text" name="website" tabindex="-1" autocomplete="off">
          </div>

          <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Your Name <span class="text-danger">*</span></label>
            <input type="text" id="name" name="name"
              class="form-control form-control-lg"
              placeholder="Enter your full name"
              value="<?= old('name') ?>" required>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email address <span class="text-danger">*</span></label>
            <input type="email" id="email" name="email"
              class="form-control form-control-lg"
              placeholder="name@example.com"
              value="<?= old('email') ?>" required>
            <div class="form-text">We'll only use this to reply to your message.</div>
          </div>

          <div class="mb-3">
            <label for="subject" class="form-label fw-semibold">Subject</label>
            <input type="text" id="subject" name="subject"
              class="form-control form-control-lg"
              placeholder="What's this about?"
              value="<?= old('subject') ?>">
          </div>

          <div class="mb-4">
            <label for="message" class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
            <textarea id="message" name="message"
              rows="6"
              class="form-control"
              placeholder="Write your message here..."
              required><?= old('message') ?></textarea>
          </div>

          <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
            <button type="submit" class="btn btn-brand btn-lg px-4 shadow-sm">
              <i class="bi bi-send-check me-2"></i> Send Message
            </button>
            <small class="text-muted">By sending, you agree to our
              <a href="<?= base_url('privacy') ?>" class="text-brand fw-semibold">Privacy Policy</a>.
            </small>
          </div>
        </form>
      </div>
    </div>

    <!-- RIGHT: CONTACT DETAILS -->
    <div class="col-lg-5">
      <div class="d-flex flex-column gap-4">

        <!-- Contact Info -->
        <div class="lcnl-card shadow-soft">
          <h5 class="fw-bold mb-3 text-brand">
            <i class="bi bi-telephone-outbound-fill me-2"></i> Reach us directly
          </h5>
          <ul class="list-unstyled mb-0 fs-6">
            <li class="mb-2">
              <i class="bi bi-envelope-fill me-2 text-accent1"></i>
              <a href="mailto:info@lcnl.org" class="fw-semibold text-brand">info@lcnl.org</a>
            </li>
            <li class="mb-2">
              <i class="bi bi-clock-fill me-2 text-accent1"></i>
              <span>We aim to respond within 2–3 working days.</span>
            </li>
          </ul>
        </div>

        <!-- Social Media -->
        <div class="lcnl-card shadow-soft">
          <h5 class="fw-bold mb-3 text-brand">
            <i class="bi bi-people-fill me-2"></i> Connect with us
          </h5>
          <p class="text-muted small mb-3">Follow LCNL for the latest news, events and updates:</p>
          <div class="d-flex align-items-center gap-4 fs-4 socials">
            <a href="https://www.facebook.com/groups/lcnlmahajan/" class="text-reset" target="_blank" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="https://www.instagram.com/lcnlmahajan/" class="text-reset" target="_blank" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="https://vimeo.com/lcnl" class="text-reset" target="_blank" aria-label="Vimeo"><i class="bi bi-vimeo"></i></a>
          </div>
        </div>

        <!-- FAQs -->
        <div class="lcnl-card shadow-soft">
          <h5 class="fw-bold mb-2 text-brand">
            <i class="bi bi-question-circle-fill me-2"></i> FAQs
          </h5>
          <p class="mb-0">Have a question? Visit our
            <a href="<?= base_url('faqs') ?>" class="fw-semibold text-brand">Frequently Asked Questions</a>
            for quick answers.
          </p>
        </div>

      </div>
    </div>

  </div>
</div>

<?= $this->endSection() ?>