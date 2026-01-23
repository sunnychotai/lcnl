<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-ruby d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2"><i class="bi bi-people-fill me-2"></i> About Us</h1>
    <p class="lead fs-5 mb-0">About the Lohana Community North London</p>
  </div>
</section>

<!-- Page Content -->
<div class="container py-1">

  <!-- Committee Image -->
  <div class="mb-2 text-center">
    <!-- Small thumbnail, clickable -->
    <img src="<?= base_url('assets/img/committee/lcnl-ec-large.jpg') ?>" alt="LCNL Executive Committee"
      class="img-fluid rounded shadow committee-img" style="max-width: 100%;" data-bs-toggle="modal"
      data-bs-target="#committeeModal">
    <!-- Subtext caption -->
    <p class="mt-1 fw-semibold text-muted">LCNL Executive Committee 2025-7</p>
  </div>
</div>

<section class="py-0">
  <div class="container">
    <div class="row g-4 align-items-start">

      <!-- Left column -->
      <div class="col-md-8">
        <div class="lcnl-card border-0">
          <p>The Lohana Community North London (LCNL) was founded in 1976 as an offshoot of the Lohana Union. Over the
            years, it has grown into a prominent voluntary organisation serving thousands of families across North
            London and Middlesex.</p>

          <p>Our aim is to promote charitable causes, advance Hindu religion and culture, support education, and provide
            relief to those in need. We achieve this through our charitable trust, Mahila Committee, Sports Club, Young
            Lohana Society, senior citizens’ groups, and a wide range of subcommittees.</p>

          <p>LCNL connects with over 2,300 families through regular News & Events, the Raghuvanshi Diwali Magazine, and
            annual festivals. We come together for Navratri, Diwali, Janmashtami, Hanuman Jayanti, and many more
            religious and cultural celebrations.</p>

          <p>We are proud of our contributions to local and international charities, the establishment of the RCT Sports
            Centre in Harrow, and most recently, the acquisition of a new community centre to serve future generations.
          </p>

          <p>Rooted in a long tradition of unity and service, LCNL continues to move forward together, honouring our
            history while building for the future.</p>

          <p>There are various affiliated commitees under the LCNL, these are:</p>
          <ul>
            <li>Mahila Committee</li>
            <li>Young Lohana Society</li>
            <li>Lohana Youth Club</li>
            <li>Senior Mens</li>
            <li>Senior Ladies</li>
            <li>Raghuvanshi Charitable Trust</li>
            <li>Lohana Charity Foundation</li>
          </ul>


          <div class="container text-left my-3">
            <blockquote class="blockquote">
              <p class="mb-0 fs-5 fw-bold text-brand">-- “We Move Forward Together”</p>
            </blockquote>
          </div>

        </div>

        <!-- Constitution Download Section -->
        <div class="lcnl-card border-0container my-4">

          <h5 class="fw-bold text-brand mb-2">
            <i class="bi bi-file-earmark-text me-2"></i> LCNL Constitution
          </h5>
          <p class="mb-3 text-muted">Read our guiding principles and values as outlined in the latest LCNL Constitution.
          </p>
          <a href="<?= base_url('assets/documents/LCNL Constitution February 2023.pdf') ?>" class="btn btn-brand btn-sm"
            target="_blank">
            <i class="bi bi-download me-1"></i> Download Constitution (PDF)
          </a>

        </div>


      </div>

      <!-- Right column (stacked cards) -->
      <div class="col-md-4 d-flex flex-column gap-3">

        <div class="card shadow-sm border-0">
          <a href="<?= base_url('events') ?>" class="stretched-link text-decoration-none text-dark">
            <div class="card-body d-flex align-items-center">
              <i class="bi bi-calendar-event-fill text-brand fs-3 me-3"></i>
              <div>
                <h5 class="card-title mb-1">Events</h5>
                <p class="card-text text-muted small">Navratri, Diwali & year-round programmes.</p>
              </div>
            </div>
          </a>
        </div>

        <div class="card shadow-sm border-0">
          <a href="<?= base_url('membership') ?>" class="stretched-link text-decoration-none text-dark">
            <div class="card-body d-flex align-items-center">
              <i class="bi bi-person-badge-fill text-accent fs-3 me-3"></i>
              <div>
                <h5 class="card-title mb-1">Membership</h5>
                <p class="card-text text-muted small">Become a member and support LCNL.</p>
              </div>
            </div>
          </a>
        </div>

        <!-- Gallery
        <div class="card shadow-sm border-0">
          <a href="<?= base_url('gallery') ?>" class="stretched-link text-decoration-none text-dark">
            <div class="card-body d-flex align-items-center">
              <i class="bi bi-images text-success fs-3 me-3"></i>
              <div>
                <h5 class="card-title mb-1">Gallery</h5>
                <p class="card-text text-muted small">Photos & videos from our events.</p>
              </div>
            </div>
          </a>
        </div>
-->

        <!-- Bereavement -->
        <div class="card shadow-sm border-0">
          <a href="<?= base_url('bereavement') ?>" class="stretched-link text-decoration-none text-dark">
            <div class="card-body d-flex align-items-center">
              <i class="bi bi-flower1 text-danger fs-3 me-3"></i>
              <div>
                <h5 class="card-title mb-1">Bereavement</h5>
                <p class="card-text text-muted small">Support, notices & community prayers.</p>
              </div>
            </div>
          </a>
        </div>

        <!-- Mahila Committee -->
        <div class="card shadow-sm border-0">
          <a href="<?= base_url('mahila') ?>" class="stretched-link text-decoration-none text-dark">
            <div class="card-body d-flex align-items-center">
              <i class="bi bi-gem text-pink fs-3 me-3"></i>
              <div>
                <h5 class="card-title mb-1">Mahila Mandal</h5>
                <p class="card-text text-muted small">Women-led programmes & activities.</p>
              </div>
            </div>
          </a>
        </div>

        <!-- Young Lohana Society (YLS) -->
        <div class="card shadow-sm border-0">
          <a href="<?= base_url('yls') ?>" class="stretched-link text-decoration-none text-dark">
            <div class="card-body d-flex align-items-center">
              <i class="bi bi-people-fill text-primary fs-3 me-3"></i>
              <div>
                <h5 class="card-title mb-1">Young Lohana Society</h5>
                <p class="card-text text-muted small">Youth events, networking & socials.</p>
              </div>
            </div>
          </a>
        </div>

        <!-- Youth Committee -->
        <div class="card shadow-sm border-0">
          <a href="<?= base_url('youth') ?>" class="stretched-link text-decoration-none text-dark">
            <div class="card-body d-flex align-items-center">
              <i class="bi bi-controller text-warning fs-3 me-3"></i>
              <div>
                <h5 class="card-title mb-1">Youth Committee</h5>
                <p class="card-text text-muted small">Activities & events for 13–18 year olds.</p>
              </div>
            </div>
          </a>
        </div>

      </div>
    </div>
  </div>
</section>

<?= $this->endSection() ?>

