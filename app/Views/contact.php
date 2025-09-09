<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<div class="hero hero-rangoli-blue d-flex align-items-center justify-content-center">
  <div class="overlay"></div>
  <div class="container position-relative">
    <h1 class="text-white fw-bold">Contact Us</h1>
    <p class="text-white-75">We would love to hear from you</p>
  </div>
</div>

<!-- Page Content -->
<div class="container py-5">
  <div class="row g-5">
    
    <!-- Left: Contact Form -->
    <div class="col-lg-7">
      <div class="card shadow-sm border-0 p-4">
        <h3 class="fw-bold mb-3">Send us a message</h3>
        <form>
          <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Your Name</label>
            <input type="text" class="form-control" id="name" placeholder="Enter your name">
          </div>
          <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email address</label>
            <input type="email" class="form-control" id="email" placeholder="name@example.com">
          </div>
          <div class="mb-3">
            <label for="message" class="form-label fw-semibold">Message</label>
            <textarea class="form-control" id="message" rows="5" placeholder="Write your message here..."></textarea>
          </div>
          <button type="submit" class="btn btn-brand px-4">
            <i class="bi bi-send-fill me-2"></i>Send Message
          </button>
        </form>
      </div>
    </div>

    <!-- Right: Info & Links -->
    <div class="col-lg-5">
      <div class="d-flex flex-column gap-4">

      <!-- Social Media -->
<div class="card shadow-sm border-0 p-4">
  <h5 class="fw-bold mb-3">
    <i class="bi bi-people-fill me-2 text-brand"></i>Connect with us
  </h5>
  <p>Follow LCNL on social media for the latest updates:</p>
  <div class="d-flex gap-3 fs-4 socials">
    <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
    <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
    <a href="#" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
  </div>
</div>


        <!-- FAQs -->
        <div class="card shadow-sm border-0 p-4">
          <h5 class="fw-bold mb-3"><i class="bi bi-question-circle-fill me-2 text-brand"></i>FAQs</h5>
          <p>Have a question? Check our <a href="<?= base_url('faqs') ?>" class="fw-semibold text-brand">Frequently Asked Questions</a> page for quick answers.</p>
        </div>

        <!-- Email -->
        <div class="card shadow-sm border-0 p-4">
          <h5 class="fw-bold mb-3"><i class="bi bi-envelope-fill me-2 text-brand"></i>Email Us</h5>
          <p>Can’t find what you’re looking for? Drop us an email at:</p>
          <p class="mb-0"><a href="mailto:info@lcnl.org" class="fw-semibold text-brand">info@lcnl.org</a></p>
        </div>

      </div>
    </div>

  </div>
</div>

<?= $this->endSection() ?>
