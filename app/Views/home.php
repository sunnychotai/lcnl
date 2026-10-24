<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php $legacy = config('Legacy50'); ?>

<!-- Hero: the Golden Jubilee. Static on purpose: the old rotating tagline
     was rarely read, and with nothing moving no pause control is needed. -->
<section class="hero-lcnl-watermark hero-overlay-ruby home-hero" aria-labelledby="heroTitle">
  <div class="container position-relative py-4 py-lg-5">
    <div class="home-hero-inner">

      <div class="home-hero-emblem" aria-hidden="true">
        <span class="home-hero-num">50</span>
        <span class="home-hero-years">1976 &ndash; 2026</span>
      </div>

      <div class="home-hero-copy">
        <p class="home-hero-eyebrow">Golden Jubilee</p>
        <h1 id="heroTitle" class="home-hero-title">
          Celebrating <span class="script">50 years</span> of LCNL
        </h1>
        <p class="home-hero-sub">
          &ldquo;We move forward together&rdquo; &mdash; bringing the Lohana community of
          North London together since 1976.
        </p>

        <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-2 mt-3">
          <?php if ($legacy->isOpen()): ?>
            <a href="<?= base_url($legacy->slug) ?>" class="btn home-hero-btn rounded-pill px-4">
              <i class="bi bi-heart-fill me-2" aria-hidden="true"></i>Support the Legacy 50 appeal
            </a>
          <?php endif; ?>
          <a href="<?= base_url('aboutus') ?>" class="btn btn-outline-light rounded-pill px-4">
            Our story
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Spotlights: each partial renders one equal-width column, or nothing. -->
<section class="container pt-4 pb-2">
  <div class="row g-4">
    <?= $this->include('chopda/_home_banner') ?>
    <?= $this->include('partials/_media_spotlight') ?>
  </div>
</section>
<?= $this->include('legacy50/_home_banner') ?>

<!-- Explore LCNL: the main routes into the site, one row on desktop. -->
<?php
$exploreTiles = [
  ['events',      'bi-calendar-event-fill', 'text-brand',   'Events',              'Navratri, Diwali & year-round programmes'],
  ['bereavement', 'bi-flower1',             'text-danger',  'Bereavement',         'Support, notices & community prayers'],
  ['mahila',      'bi-gem',                 'text-pink',    'Mahila Committee',    'Women-led programmes & activities'],
  ['yls',         'bi-people-fill',         'text-primary', 'Young Lohana Society', 'Youth events, networking & socials'],
  ['youth',       'bi-controller',          'text-warning', 'Lohana Youth Club',   'Activities for 13–18 year olds'],
  ['aboutus',     'bi-clock-history',       'text-accent1', 'About LCNL',          'Our story since 1976'],
];
?>
<section class="container py-3" aria-labelledby="explore-lcnl">
  <h2 id="explore-lcnl" class="mb-3">Explore LCNL</h2>
  <div class="row g-3">
    <?php foreach ($exploreTiles as [$path, $icon, $tone, $title, $blurb]): ?>
      <div class="col-6 col-md-4 col-xl-2">
        <a href="<?= base_url($path) ?>" class="home-tile">
          <i class="bi <?= $icon ?> <?= $tone ?>" aria-hidden="true"></i>
          <span class="home-tile-title"><?= esc($title) ?></span>
          <span class="home-tile-blurb"><?= esc($blurb) ?></span>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php // Upcoming Events temporarily hidden (Oct 2026); set to true to bring it back.
$showUpcomingEvents = false; ?>
<?php if ($showUpcomingEvents && !empty($upcomingEvents)): ?>
  <section class="container py-3">
    <h2 class="mb-3">
      <a href="<?= base_url('events') ?>" class="text-decoration-none text-dark">
        Upcoming Events
      </a>
    </h2>

    <div class="row g-3 justify-content-center">
      <?php foreach ($upcomingEvents as $event): ?>

        <?php
        $imagePath = $event['image'] ?? '';
        $fullPath = FCPATH . ltrim($imagePath, '/');

        if (empty($imagePath) || !is_file($fullPath)) {
          $imagePath = 'assets/img/lcnl-placeholder-320.png';
        }
        ?>

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
          <a href="<?= base_url('events/' . $event['id']) ?>"
            class="text-decoration-none event-card-link d-block h-100">

            <div class="card shadow-sm border-0 event-card h-100">

              <div class="event-img-wrapper position-relative">
                <img src="<?= base_url($imagePath) ?>"
                  class="card-img-top"
                  alt="<?= esc($event['title']) ?>"
                  loading="lazy" decoding="async">

                <div class="event-overlay">
                  <h3 class="h6 text-white mb-1">
                    <?= esc($event['title']) ?>
                  </h3>

                  <small class="text-light">
                    <?= date('d M Y', strtotime($event['event_date'])) ?>
                  </small>
                </div>

                <?php if (!empty($event['is_sold_out'])): ?>
                  <div class="sold-out-banner">
                    <i class="bi bi-x-circle-fill me-1"></i>SOLD OUT
                  </div>
                <?php endif; ?>
              </div>

            </div>
          </a>
        </div>

      <?php endforeach; ?>
    </div>
  </section>
<?php endif; ?>

<?= $this->include('gallery/_home_latest') ?>

<section class="py-3">
  <div class="container">
    <div class="row g-4">

      <!-- Message from the President -->
      <div class="col-lg-8">
        <article class="home-president h-100" aria-labelledby="president-message">
          <header class="home-president-head">
            <img src="<?= base_url('assets/img/committee/ronak-paw.jpg') ?>"
              class="home-president-photo" alt="Ronak Paw, LCNL President"
              width="112" height="112" loading="lazy" decoding="async">
            <div>
              <p class="home-president-eyebrow">Jai Shree Krishna &middot; Jai Shree Ram &middot; Jai Jalaram</p>
              <h2 id="president-message" class="h4 fw-bold mb-1">Message from the President</h2>
              <p class="mb-0 text-muted">Ronak Paw &middot; LCNL President 2025&ndash;2027</p>
            </div>
          </header>

          <blockquote class="home-president-quote">Together we can keep LCNL thriving for the next 50 years.</blockquote>

          <p>It is an honour to serve as one of the youngest, and first UK-born, LCNL President as we mark our 50th
            year. This milestone is a reflection of the dedication of past presidents, committees and members.</p>

          <p>We have introduced a portfolio system where each Executive Committee member leads or supports an area of
            activity. This ensures events and services are well-managed and gives everyone the chance to contribute.</p>

          <p>Our focus will be to maintain LCNL’s cultural and religious programmes, while also introducing new
            initiatives that appeal to all generations&mdash;especially children and youth, who are key to our future.</p>

          <p class="mb-0">I encourage all members to take part, share ideas, and support our events.</p>

          <p class="home-president-sign">Ronak Paw</p>
        </article>
      </div>

      <div class="col-lg-4 d-flex flex-column gap-4">

        <!-- The page's one membership call to action -->
        <?php if (empty($isLoggedIn)): ?>
          <div class="home-join">
            <i class="bi bi-person-plus-fill home-join-icon" aria-hidden="true"></i>
            <h2 class="h4 fw-bold mb-2">Join LCNL</h2>
            <p class="mb-4">
              Be part of our next 50 years. Membership takes a few minutes &mdash; enter your details,
              confirm your email and you&rsquo;re in.
            </p>
            <a href="<?= base_url('membership/register') ?>" class="btn btn-brand btn-lg rounded-pill px-4 w-100">
              Become a member
            </a>
            <a href="<?= base_url('membership') ?>" class="d-inline-block mt-3 small fw-semibold home-join-link">
              Why join? See membership benefits <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
          </div>
        <?php else: ?>
          <div class="home-join">
            <i class="bi bi-speedometer2 home-join-icon" aria-hidden="true"></i>
            <h2 class="h4 fw-bold mb-2">Your Dashboard</h2>
            <p class="mb-4">Access your profile and upcoming events from your member dashboard.</p>
            <a href="<?= route_to('account.dashboard') ?>" class="btn btn-brand btn-lg rounded-pill px-4 w-100">
              Go to Dashboard
            </a>
          </div>
        <?php endif; ?>

        <!-- DLC Hall Hire Banner -->
        <a href="<?= base_url('dlc-hire') ?>" class="text-decoration-none d-block flex-grow-1">
          <div class="rounded-4 shadow-sm overflow-hidden h-100" style="background:linear-gradient(135deg,#1a1a2e 0%,#0f3460 100%);">
            <div class="p-4 text-white position-relative">
              <div class="position-absolute top-0 end-0 p-3 opacity-25">
                <i class="bi bi-stars" style="font-size:4rem;"></i>
              </div>
              <span class="badge mb-2 px-2 py-1 fw-semibold" style="background:rgba(255,193,7,0.2);color:#ffc107;border:1px solid rgba(255,193,7,0.4);font-size:0.7rem;letter-spacing:.05em;">DLC HALL HIRE</span>
              <h2 class="h5 fw-bold mb-1">Host Your Event at DLC</h2>
              <p class="mb-3 opacity-90 small">Fully equipped halls with professional lighting, sound &amp; screens — available to hire on weekdays.</p>
              <div class="d-flex align-items-center justify-content-between">
                <span class="fw-bold fs-5" style="color:#ffc107;">From £19 <span class="fw-normal fs-6 opacity-90">per person</span></span>
                <span class="btn btn-sm rounded-pill fw-semibold px-3" style="background:#ffc107;color:#1a1a2e;">Find Out More</span>
              </div>
            </div>
          </div>
        </a>

      </div>
    </div>
  </div>
</section>
<style>
  .sold-out-banner {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    padding: 6px 0;
    background: rgba(185, 28, 28, 0.92);
    color: #fff;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-align: center;
    text-transform: uppercase;
    z-index: 20;
  }
</style>

<?= $this->endSection() ?>

