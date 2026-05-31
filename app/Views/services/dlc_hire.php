<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero -->
<section class="hero-lcnl-watermark d-flex align-items-center justify-content-center"
  style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); min-height: 280px;">
  <div class="container position-relative text-center text-white py-4">
    <span class="badge mb-3 px-3 py-2 fw-semibold" style="background:rgba(255,193,7,0.2); color:#ffc107; border:1px solid rgba(255,193,7,0.4); font-size:0.8rem; letter-spacing:0.05em;">WEEKDAY ENTERTAINMENT PACKAGE</span>
    <h1 class="fw-bold display-5 mb-2">Dhamecha Lohana Centre</h1>
    <p class="lead mb-3 opacity-90">A venue at the heart of our community — now available to hire</p>
    <p class="fs-5 fw-semibold" style="color:#ffc107;">Event packages from just <strong>£19 per person</strong></p>
  </div>
</section>

<div class="container py-5">

  <div class="row g-4">

    <!-- Left: main content -->
    <div class="col-lg-8">

      <!-- About the centre -->
      <div class="lcnl-card rounded border-0 shadow-sm mb-4 p-4">
        <h3 class="fw-bold mb-3">About the Centre</h3>
        <p>
          The <strong>Dhamecha Lohana Centre (DLC)</strong> is the home of the Lohana Community North London (LCNL)
          and a venue that plays a central role in many of our community events. It is a facility we can all be proud of.
        </p>
        <p class="mb-0">
          The DLC Directors, LCF Trustees, and the LCNL Executive Committee have been working hard to enhance the
          centre's facilities. <strong>Recent improvements include:</strong>
        </p>
        <ul class="mt-3 mb-0">
          <li class="mb-2">A new <strong>professional lighting rig</strong> in the JV Gokal Hall (top floor)</li>
          <li>New <strong>screens, sound system, microphones, and Zoom cameras</strong> in the Kantaria Hall (ground floor)</li>
        </ul>
        <p class="mt-3 mb-0 text-muted">
          Further upgrades and improvements are being explored to continue enhancing the experience for all users of the centre.
        </p>
      </div>

      <!-- Weekday package -->
      <div class="lcnl-card rounded border-0 shadow-sm mb-4 p-4">
        <div class="d-flex align-items-center gap-3 mb-3">
          <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-stars text-white fs-5"></i>
          </div>
          <h3 class="fw-bold mb-0">Weekday Entertainment Package</h3>
        </div>
        <p>
          To increase usage and generate valuable revenue for our community, the management team has launched an
          <strong>exceptional weekday entertainment package</strong>. Whether you're hosting a birthday celebration,
          anniversary dinner, corporate event, or family gathering — DLC offers a professional, fully equipped venue
          at an outstanding price.
        </p>
        <div class="rounded-4 p-4 text-center mb-3" style="background:linear-gradient(135deg,#fefce8,#fef9c3);border:2px solid #f59e0b;">
          <p class="mb-1 text-muted small fw-semibold text-uppercase" style="letter-spacing:.05em;">Packages starting from</p>
          <p class="display-5 fw-bold mb-1" style="color:#d97706;">£19 <span class="fs-4">per person</span></p>
          <p class="mb-0 text-muted small">Weekday hire — contact us for full package details</p>
        </div>
        <p class="mb-0">
          Hiring out the centre generates valuable revenue that directly benefits our community.
          Please see the flyer below for full details on what's included.
        </p>
      </div>

      <!-- PDF flyer -->
      <div class="lcnl-card rounded border-0 shadow-sm mb-4 p-4">
        <h4 class="fw-bold mb-3"><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>Package Flyer</h4>
        <p class="text-muted mb-3">Download or view the full details of the weekday entertainment package below.</p>

        <a href="<?= base_url('assets/img/services/DLCWeekdayEntertainmentPackage-May2026.pdf') ?>"
          target="_blank" class="btn btn-danger rounded-pill px-4 mb-3">
          <i class="bi bi-download me-2"></i>Download Flyer (PDF)
        </a>

        <div class="ratio mt-2" style="--bs-aspect-ratio:141.4%;">
          <iframe src="<?= base_url('assets/img/services/DLCWeekdayEntertainmentPackage-May2026.pdf') ?>"
            class="rounded-3 border" style="min-height:600px;">
            <p>Your browser does not support PDFs.
              <a href="<?= base_url('assets/img/services/DLCWeekdayEntertainmentPackage-May2026.pdf') ?>">Download the PDF</a>.
            </p>
          </iframe>
        </div>
      </div>

    </div>

    <!-- Right: contact + links -->
    <div class="col-lg-4">

      <!-- Book now -->
      <div class="rounded-4 shadow-sm mb-4 p-4 text-white" style="background:linear-gradient(135deg,#1a1a2e,#0f3460);">
        <h4 class="fw-bold mb-3">Make a Booking</h4>
        <p class="opacity-90 mb-3">
          For further information or to make a booking, please contact the DLC Manager directly.
        </p>
        <div class="d-flex align-items-center gap-3 mb-3">
          <div style="width:44px;height:44px;border-radius:10px;background:rgba(255,193,7,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-person-fill" style="color:#ffc107;font-size:1.2rem;"></i>
          </div>
          <div>
            <p class="mb-0 fw-semibold">Devendra</p>
            <p class="mb-0 opacity-75 small">Manager, Dhamecha Lohana Centre Ltd</p>
          </div>
        </div>
        <a href="tel:07593571990" class="btn w-100 fw-semibold rounded-pill mb-2"
          style="background:#ffc107;color:#1a1a2e;">
          <i class="bi bi-telephone-fill me-2"></i>07593 571 990
        </a>
        <a href="https://dlchall.org/" target="_blank" rel="noopener"
          class="btn btn-outline-light w-100 rounded-pill fw-semibold">
          <i class="bi bi-box-arrow-up-right me-2"></i>Visit dlchall.org
        </a>
      </div>

      <!-- What's available -->
      <div class="lcnl-card rounded border-0 shadow-sm mb-4 p-4">
        <h5 class="fw-bold mb-3">Available Spaces</h5>
        <div class="d-flex gap-3 mb-3">
          <div style="width:36px;height:36px;border-radius:8px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-building text-success"></i>
          </div>
          <div>
            <p class="fw-semibold mb-0">JV Gokal Hall</p>
            <p class="text-muted small mb-0">Top floor — new professional lighting rig</p>
          </div>
        </div>
        <div class="d-flex gap-3">
          <div style="width:36px;height:36px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-display text-primary"></i>
          </div>
          <div>
            <p class="fw-semibold mb-0">Kantaria Hall</p>
            <p class="text-muted small mb-0">Ground floor — screens, sound system, Zoom cameras</p>
          </div>
        </div>
      </div>

      <!-- LCNL message -->
      <div class="lcnl-card rounded border-0 shadow-sm p-4">
        <h5 class="fw-bold mb-2">From the LCNL Executive Committee</h5>
        <p class="text-muted small mb-0">
          "We look forward to welcoming you to the Dhamecha Lohana Centre. Hiring the venue helps generate
          valuable revenue that directly supports our community programmes."
        </p>
      </div>

    </div>
  </div>
</div>

<?= $this->endSection() ?>
