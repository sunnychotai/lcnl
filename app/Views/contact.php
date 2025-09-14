<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2"><i class="bi bi-envelope-paper-heart me-2"></i> Contact Us</h1>
    <p class="lead fs-6 mb-0">We’d love to hear from you</p>
  </div>
</section>

<!-- Page Content -->
<div class="container py-5">

  <!-- Alerts -->
  <?php if ($success = session()->getFlashdata('success')): ?>
    <div class="alert alert-success shadow-sm border-0">
      <i class="bi bi-check-circle-fill me-2"></i><?= esc($success) ?>
    </div>
  <?php endif; ?>

  <?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger shadow-sm border-0">
      <div class="d-flex">
        <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
        <div>
          <strong class="d-block mb-1">Please fix the following:</strong>
          <ul class="mb-0">
            <?php foreach ((array)$errors as $err): ?>
              <li><?= esc($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div class="row g-5">

    <!-- Left: Contact Form -->
    <div class="col-lg-7">
      <div class="constitution-box">
        <h3 class="fw-bold mb-3"><i class="bi bi-send-fill me-2 text-brand"></i>Send us a message</h3>

        <form action="<?= base_url('contact/send') ?>" method="post" autocomplete="on">
          <?= csrf_field() ?>

          <!-- Honeypot (basic) -->
          <div class="d-none" aria-hidden="true">
            <label>Leave this field empty</label>
            <input type="text" name="website" tabindex="-1" autocomplete="off">
          </div>

          <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Your Name</label>
            <input
              type="text"
              class="form-control"
              id="name"
              name="name"
              placeholder="Enter your name"
              value="<?= old('name') ?>"
              required
            >
          </div>

          <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email address</label>
            <input
              type="email"
              class="form-control"
              id="email"
              name="email"
              placeholder="name@example.com"
              value="<?= old('email') ?>"
              required
            >
            <div class="form-text">We’ll only use this to reply to your message.</div>
          </div>

          <div class="mb-3">
            <label for="subject" class="form-label fw-semibold">Subject</label>
            <input
              type="text"
              class="form-control"
              id="subject"
              name="subject"
              placeholder="What’s this about?"
              value="<?= old('subject') ?>"
            >
          </div>

          <div class="mb-3">
            <label for="message" class="form-label fw-semibold">Message</label>
            <textarea
              class="form-control"
              id="message"
              name="message"
              rows="6"
              placeholder="Write your message here..."
              required
            ><?= old('message') ?></textarea>
          </div>

          <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-brand px-4">
              <i class="bi bi-send-check me-2"></i>Send Message
            </button>
            <small class="text-muted">By sending, you agree to our <a href="<?= base_url('privacy') ?>" class="text-brand fw-semibold">Privacy Policy</a>.</small>
          </div>
        </form>
      </div>
    </div>

    <!-- Right: Info & Links -->
    <div class="col-lg-5">
      <div class="d-flex flex-column gap-4">

        <!-- Reach Us -->
        <div class="constitution-box">
          <h5 class="fw-bold mb-3">
            <i class="bi bi-telephone-outbound-fill me-2 text-brand"></i>Reach us directly
          </h5>
          <ul class="list-unstyled mb-0">
            <li class="mb-2">
              <i class="bi bi-envelope-fill me-2 text-brand"></i>
              <a href="mailto:info@lcnl.org" class="fw-semibold text-brand">info@lcnl.org</a>
            </li>
            <!-- Add phone/address if/when available
            <li class="mb-2">
              <i class="bi bi-geo-alt-fill me-2 text-brand"></i>
              <span>Address line, City, Postcode</span>
            </li>
            -->
            <li class="mb-0">
              <i class="bi bi-clock-fill me-2 text-brand"></i>
              <span>We aim to respond within 2–3 working days.</span>
            </li>
          </ul>
        </div>

        <!-- Social Media -->
        <div class="constitution-box">
          <h5 class="fw-bold mb-3">
            <i class="bi bi-people-fill me-2 text-brand"></i>Connect with us
          </h5>
          <p class="mb-3">Follow LCNL for the latest updates:</p>
          <div class="d-flex align-items-center gap-3 socials fs-4">
            <a href="https://www.facebook.com/groups/lcnlmahajan/" class="text-reset" aria-label="Facebook" target="_blank" rel="noopener">
              <i class="bi bi-facebook"></i>
            </a>
            <a href="https://www.instagram.com/lcnlmahajan/" class="text-reset" aria-label="Instagram" target="_blank" rel="noopener">
              <i class="bi bi-instagram"></i>
            </a>
            <a href="https://vimeo.com/lcnl" class="text-reset" aria-label="Vimeo" target="_blank" rel="noopener">
              <i class="bi bi-vimeo"></i>
            </a>
          </div>
        </div>

        <!-- FAQs -->
        <div class="constitution-box">
          <h5 class="fw-bold mb-2">
            <i class="bi bi-question-circle-fill me-2 text-brand"></i>FAQs
          </h5>
          <p class="mb-0">Have a question? Check our
            <a href="<?= base_url('faqs') ?>" class="fw-semibold text-brand">Frequently Asked Questions</a>
            for quick answers.
          </p>
        </div>

      </div>
    </div>

  </div>
</div>

<?= $this->endSection() ?>
