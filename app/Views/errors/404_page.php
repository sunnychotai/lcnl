<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<section class="py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 text-center">

        <p class="text-uppercase fw-bold text-accent1 mb-2" style="letter-spacing:.14em;font-size:.8rem;">
          Error 404
        </p>

        <h1 class="fw-bold text-brand mb-3">We couldn&rsquo;t find that page</h1>

        <p class="lead text-muted mb-5">
          The link may be out of date, or the page may have moved. Nothing is broken &mdash;
          here are the places people usually want.
        </p>

        <div class="row g-3 text-start mb-5">
          <div class="col-md-4">
            <a href="<?= base_url('events') ?>" class="d-block h-100 p-4 rounded border text-decoration-none">
              <i class="bi bi-calendar-event fs-3 text-brand d-block mb-2"></i>
              <span class="fw-bold d-block text-brand">Events</span>
              <small class="text-muted">What&rsquo;s coming up across the community</small>
            </a>
          </div>
          <div class="col-md-4">
            <a href="<?= base_url('membership') ?>" class="d-block h-100 p-4 rounded border text-decoration-none">
              <i class="bi bi-person-plus fs-3 text-brand d-block mb-2"></i>
              <span class="fw-bold d-block text-brand">Membership</span>
              <small class="text-muted">Join LCNL or sign in to your account</small>
            </a>
          </div>
          <div class="col-md-4">
            <a href="<?= base_url('contact') ?>" class="d-block h-100 p-4 rounded border text-decoration-none">
              <i class="bi bi-envelope fs-3 text-brand d-block mb-2"></i>
              <span class="fw-bold d-block text-brand">Contact us</span>
              <small class="text-muted">Tell us what you were looking for</small>
            </a>
          </div>
        </div>

        <a href="<?= base_url() ?>" class="btn btn-brand px-4">
          <i class="bi bi-house-door me-1"></i> Back to the homepage
        </a>

        <p class="mt-4 mb-0 text-muted small">
          If you followed a link from a message or email and keep landing here,
          please <a href="<?= base_url('contact') ?>">let us know</a> so we can fix it.
        </p>

        <?php if (!empty($debugMessage)): ?>
          <pre class="text-start bg-light border rounded p-3 mt-4 small text-muted"
               style="white-space:pre-wrap;"><?= esc($debugMessage) ?></pre>
        <?php endif; ?>

      </div>
    </div>
  </div>
</section>

<?= $this->endSection() ?>
