<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-midnight d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">
      <i class="bi bi-flower1 me-2"></i>Bereavement Support
    </h1>
    <p class="lead fs-6 mb-0">Compassionate assistance for the Lohana Community</p>
  </div>
</section>

<div class="container py-5">

  <!-- Row 1: Description (left) + Image (right) -->
  <div class="border-0 lcnl-card mb-4">
    <div class="card-body">
      <div class="row g-4 align-items-start">

        <!-- Description -->
        <div class="col">
          <h4 class="mb-3">
            <i class="bi bi-info-circle me-2"></i>About Bereavement Support
          </h4>
          <div class="fs-6">
            <p>
              The <strong>Bereavement Committee</strong> supports families with compassion and sensitivity, according
              to each family’s wishes. We can guide you through arrangements from the <em>Prathna</em> to the funeral.
              A dedicated team is available to perform the Prathna and, where desired, volunteers can support with
              <em>Bhajans</em>—free of charge.
            </p>

            <p>
              We currently have over <strong>3,250 registered recipients</strong> for bereavement emails. Between
              <strong>1 August and 31 October 2022</strong>, more than <strong>539,000 emails</strong> were sent
              (around <strong>11 emails per week</strong>). This free LCNL service is open to everyone, including
              non-Lohanas.
            </p>

            <div class="d-flex flex-wrap gap-2">
              <a href="mailto:bereavement@lcnl.org" class="btn btn-brand">
                <i class="bi bi-envelope-paper-heart me-2"></i>Email the Bereavement Team
              </a>
              <a href="<?= base_url('contact') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-chat-left-heart me-2"></i>Contact form
              </a>
            </div>
          </div>
        </div>

        <!-- Image -->
        <div class="col-md-auto order-md-last">
          <div class="event-img-wrapper" style="width:250px; max-width:100%;">
            <img
              src="<?= base_url('assets/img/shiva.png') ?>"
              alt="Shiva"
              class="img-fluid rounded d-block mx-auto"
            >
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Row 2: GDPR -->
  <div class="border-0 lcnl-card mb-4">
    <div class="card-body">
      <h4 class="mb-3">
        <i class="bi bi-shield-check me-2 text-brand"></i>GDPR Re-registration
      </h4>
      <div class="fs-6">
        <p class="mb-0">
          Many past recipients still need to re-register under GDPR. There’s no limit on how many family members
          can register. Please email
          <a href="mailto:bereavement@lcnl.org" class="fw-semibold text-brand">bereavement@lcnl.org</a>
          to be added.
        </p>
      </div>
    </div>
  </div>

  <!-- Row 3: Contacts -->
  <div class="border-0 lcnl-card mb-4">
    <div class="card-body">
      <h4 class="mb-3">
        <i class="bi bi-telephone-outbound-fill me-2 text-brand"></i>Contacts
      </h4>

      <div class="row g-4 fs-6">
        <div class="col-md-6">
          <h6 class="mb-1">Vinu Kotecha</h6>
          <p class="mb-2 text-muted"><strong>Chairman – Bereavement Committee</strong></p>
          <ul class="list-unstyled mb-0">
            <li class="mb-1">
              <i class="bi bi-envelope-fill me-2 text-brand"></i>
              <a href="mailto:vinodk52@aol.com" class="text-brand fw-semibold">vinodk52@aol.com</a>
            </li>
            <li class="mb-0">
              <i class="bi bi-telephone-fill me-2 text-brand"></i>
              <a href="tel:+447956847764" class="text-reset">+44 7956 847764</a>
            </li>
          </ul>
        </div>

        <div class="col-md-6">
          <h6 class="mb-1">Rajubhai Sawjani</h6>
          <p class="mb-2 text-muted"><strong>Bereavement Committee</strong></p>
          <ul class="list-unstyled mb-0">
            <li class="mb-1">
              <i class="bi bi-envelope-fill me-2 text-brand"></i>
              <a href="mailto:rtsawjani@btinternet.com" class="text-brand fw-semibold">rtsawjani@btinternet.com</a>
            </li>
            <li class="mb-0">
              <i class="bi bi-telephone-fill me-2 text-brand"></i>
              <a href="tel:+447941355358" class="text-reset">+44 7941 355358</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Row 4: FAQs -->
  <div class="border-0 lcnl-card mb-4">
    <div class="card-body">
      <h4 class="mb-3">
        <i class="bi bi-question-circle-fill me-2 text-brand"></i>Frequently Asked Questions
      </h4>
      <?= view('faqs/_accordion', ['faqs' => $faqs ?? []]) ?>
    </div>
  </div>

</div>

<?= $this->endSection() ?>
