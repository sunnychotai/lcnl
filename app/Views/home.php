<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-ruby d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 id="heroTitle" class="motto"></h1>
    <p id="heroSubtitle" class="lead fs-5 mb-0"></p>
  </div>
</section>

<style>
  #heroTitle,
  #heroSubtitle {
    opacity: 0;
    transition: opacity 0.8s ease-in-out, transform 0.8s ease-in-out;
    transform: translateY(10px);
  }

  #heroTitle.show,
  #heroSubtitle.show {
    opacity: 1;
    transform: translateY(0);
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const slides = [
      {
        title: `"WE MOVE <span class='script'>FORWARD</span> TOGETHER"`,
        subtitle: "... bringing people together since 1976. Proud of our heritage"
      },
      {
        title: `"CELEBRATING <span class='script'>COMMUNITY</span> & CULTURE"`,
        subtitle: "Uniting Lohanas across North London"
      },
      {
        title: `"SUPPORTING <span class='script'>FAMILIES</span> IN NEED"`,
        subtitle: "From bereavement care to cultural guidance, LCNL is here for everyone."
      }
    ];

    const titleEl = document.getElementById('heroTitle');
    const subEl = document.getElementById('heroSubtitle');
    let idx = 0;

    function showSlide(i) {
      titleEl.classList.remove('show');
      subEl.classList.remove('show');

      setTimeout(() => {
        titleEl.innerHTML = slides[i].title;
        subEl.innerHTML = slides[i].subtitle;

        titleEl.classList.add('show');
        subEl.classList.add('show');
      }, 800);
    }

    showSlide(idx);
    setInterval(() => {
      idx = (idx + 1) % slides.length;
      showSlide(idx);
    }, 5000);
  });
</script>

<?php if (!empty($upcomingEvents)): ?>
  <section class="container py-3">
    <h2 class="mb-2">
      <a href="<?= base_url('events') ?>" class="text-decoration-none text-dark">
        Upcoming Events
      </a>
    </h2>

    <div class="d-flex overflow-auto gap-3 pb-2">
      <?php foreach ($upcomingEvents as $event): ?>
        <a href="<?= base_url('events/' . $event['id']) ?>" class="text-decoration-none flex-shrink-0 event-card-link">
          <div class="card shadow-sm border-0 event-card">
            <div class="event-img-wrapper">
              <?php
              $imagePath = $event['image'] ?? '';
              $fullPath = FCPATH . ltrim($imagePath, '/');
              if (empty($imagePath) || !is_file($fullPath)) {
                $imagePath = 'assets/img/lcnl-placeholder-320.png';
              }
              ?>
              <img src="<?= base_url($imagePath) ?>" class="card-img-top" alt="<?= esc($event['title']) ?>">

              <div class="event-overlay">
                <h6 class="text-white mb-1"><?= esc($event['title']) ?></h6>
                <small class="text-light"><?= date('d M Y', strtotime($event['event_date'])) ?></small>
              </div>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </section>
<?php endif; ?>

<section class="py-3">
  <div class="container">
    <div class="row g-4 align-items-start">

      <!-- Left column -->
      <div class="col-md-8">
        <!-- Mahila Mandal Baby Clothing Drive -->
        <div class="lcnl-card rounded border-0 shadow-sm mb-4 p-3">

          <h4 class="fw-bold mb-3 text-brand">
            <i class="bi bi-heart-fill text-pink me-2"></i>
            Baby Clothing Donations 2025 Drive
          </h4>

          <img src="<?= base_url('assets/img/events/lcnl_mahila_charity.jpg') ?>"
            class="img-fluid rounded shadow-sm mb-3" alt="Mahila Mandal Baby Clothing Drive">

          <p class="mb-2">
            <strong>LCNL Mahila Mandal</strong> is supporting NHS England’s
            Premature Baby Unit at NWP by collecting new or gently used baby
            clothing items — bodysuits, sleepsuits, hats, and mitts — to bring
            warmth and comfort to premature babies in need.
          </p>

          <p class="mb-2">
            <strong>📅 Collection Dates:</strong> 1st Nov – 20th Dec 2025<br>
            <strong>👕 Note:</strong> Please wash, sort, and pack clothing by age before donating.
          </p>

          <p class="mb-2">
            Let’s come together as a community to spread comfort, love, and hope
            to these little ones. 💕
          </p>

          <p class="fw-bold mb-1">📦 Drop-off & Information:</p>
          <ul class="small mb-0">
            <li>Nainaben Raithatha — 07944 244 442</li>
            <li>Malini Vissandjee — 07941 651 866</li>
            <li>Sudhaben Badiani — 07809 096 053</li>
            <li>Archanaben Sodha — 07795 958 537</li>
            <li>Kalpanaben Sanghani — 07949 443 691</li>
          </ul>

        </div>

        <!-- Message from the President -->
        <div class="lcnl-card rounded border-0 shadow-sm">
          <h4 class="fw-bold mb-3">Message from the President</h4>

          <img src="<?= base_url('assets/img/committee/ronak-paw.jpg') ?>"
            class="mx-auto d-block d-md-inline float-md-start me-md-3 mb-2 rounded-circle shadow-sm"
            style="width:220px; height:220px; object-fit:cover; object-position: top;" alt="President Photo">

          <p>Jai Shree Krishna | Jai Shree Ram | Jai Jalaram</p>

          <p>It is an honour to serve as the youngest, and first UK-born, LCNL President as we mark our 50th year. This
            milestone is a reflection of the dedication of past presidents, committees and members.</p>

          <p>We have introduced a portfolio system where each Executive Committee member leads or supports an area of
            activity. This ensures events and services are well-managed and gives everyone the chance to contribute.</p>

          <p>Our focus will be to maintain LCNL’s cultural and religious programmes, while also introducing new
            initiatives that appeal to all generations—especially children and youth, who are key to our future.</p>

          <p>I encourage all members to take part, share ideas, and support our events. Together we can keep LCNL
            thriving for the next 50 years.</p>

          <p class="fw-bold mb-0">Ronak Paw</p>
          <p class="mb-0">LCNL President 2025 – 2027</p>
        </div>
      </div>

      <!-- Right column -->
      <div class="col-md-4 d-flex flex-column gap-3">

        <!-- Membership Card -->
        <?php if (empty($isLoggedIn)): ?>
          <div class="lcnl-card shadow-lg border-0">
            <div class="card-body text-center">
              <h3 class="fw-bold mb-3">
                <i class="bi bi-person-plus-fill text-success"></i> Register Now!
              </h3>
              <p class="text-muted mb-4">
                Create your LCNL membership in minutes. Enter your details,
                confirm your email, and start enjoying the benefits of our community.
              </p>
              <a href="<?= base_url('membership/register') ?>" class="btn btn-success btn-lg rounded-pill px-4">
                <i class="bi bi-pencil-square me-2"></i> Register Now
              </a>
            </div>
          </div>

        <?php else: ?>

          <div class="card shadow-lg border-0 rounded-4 mb-4">
            <div class="card-body text-center p-4">
              <div class="mb-3">
                <i class="bi bi-speedometer2 text-brand" style="font-size:3rem;"></i>
              </div>
              <h3 class="fw-bold mb-3">Your Dashboard</h3>
              <p class="text-muted mb-4">
                Access your profile and upcoming events from your member dashboard.
              </p>
              <a href="<?= route_to('account.dashboard') ?>" class="btn btn-brand btn-lg rounded-pill px-4">
                <i class="bi bi-speedometer2 me-2"></i> Go to Dashboard
              </a>
            </div>
          </div>

        <?php endif; ?>

        <!-- Events Card -->
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

        <!-- Membership Info -->
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
                <h5 class="card-title mb-1">Mahila Committee</h5>
                <p class="card-text text-muted small">Women-led programmes & activities.</p>
              </div>
            </div>
          </a>
        </div>

        <!-- YLS -->
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

        <!-- Youth -->
        <div class="card shadow-sm border-0">
          <a href="<?= base_url('youth') ?>" class="stretched-link text-decoration-none text-dark">
            <div class="card-body d-flex align-items-center">
              <i class="bi bi-controller text-warning fs-3 me-3"></i>
              <div>
                <h5 class="card-title mb-1">Lohana Youth Committee</h5>
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

