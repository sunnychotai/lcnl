<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero bg-brand d-flex align-items-center">
  <div class="container text-lg-start">
    <div class="glass-box text-start">
  <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
    <div class="carousel-inner text-white">

      <!-- Intro -->
      <div class="carousel-item active">
        <h3 class="fw-bold">
          <i class="bi bi-newspaper me-2"></i>
          Lohana Community of North London
        </h3>
        <p>
          The Lohana Community North London (LCNL) has been bringing people together since 1976. 
          Serving over 2,300 families, we celebrate our culture, support charitable causes, and 
          create spaces where our community can thrive. Proud of our heritage, we move forward 
          together for future generations.
        </p>
      </div>

      <!-- News items -->
      <div class="carousel-item">
        <p><i class="bi bi-ticket-perforated me-2"></i>Navratri Garba tickets available now!</p>
      </div>
      <div class="carousel-item">
        <p><i class="bi bi-stars me-2"></i>Join us for Diwali celebrations on 1st Nov.</p>
      </div>
      <div class="carousel-item">
        <p><i class="bi bi-people-fill me-2"></i>Over 2,300 families are part of LCNL.</p>
      </div>

    </div>
  </div>
</div>
  </div>
</section>






<?php if (!empty($upcomingEvents)): ?>
  <section class="container py-3">
<h2 class="mb-4">
  <a href="<?= base_url('events') ?>" class="text-decoration-none text-dark">
    Upcoming Events
  </a>
</h2>
    <div class="d-flex overflow-auto gap-3 pb-2">
      <?php foreach ($upcomingEvents as $event): ?>
        <a href="<?= base_url('events/'.$event['id']) ?>" 
           class="text-decoration-none flex-shrink-0" 
           style="width: 280px;">
          <div class="card shadow-sm border-0 h-100 event-card">
            <?php if (!empty($event['image'])): ?>
              <div class="event-img-wrapper">
                <img src="<?= base_url($event['image']) ?>" 
                     class="card-img-top" 
                     alt="<?= esc($event['title']) ?>">
                <div class="event-overlay">
                  <h6 class="text-white mb-1"><?= esc($event['title']) ?></h6>
                  <small class="text-light">
                    <?= date('d M Y', strtotime($event['event_date'])) ?>
                    
                  </small>
                </div>
              </div>
            <?php endif; ?>
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
        <div class="p-4 bg-light rounded shadow-sm">
          <h4 class="fw-bold mb-3">Message from the President</h4>

<img src="<?= base_url('assets/img/committee/ronak-paw.jpg') ?>" 
     class="float-md-start me-3 mb-2 rounded-circle shadow-sm" 
     style="width:220px; height:220px; object-fit:cover; object-position: top;" 
     alt="President Photo">

<p>Jai Shree Krishna | Jai Shree Ram | Jai Jalaram</p>

<p>It is an honour to serve as the youngest, and first UK-born, LCNL President as we mark our 50th year. This milestone is a reflection of the dedication of past presidents, committees and members.</p>

<p>We have introduced a portfolio system where each Executive Committee member leads or supports an area of activity. This ensures events and services are well-managed and gives everyone the chance to contribute.</p>

<p>Our focus will be to maintain LCNL’s cultural and religious programmes, while also introducing new initiatives that appeal to all generations—especially children and youth, who are key to our future.</p>

<p>I encourage all members to take part, share ideas, and support our events. Together we can keep LCNL thriving for the next 50 years.</p>

<p class="fw-bold mb-0">Ronak Paw</p>
<p class="mb-0">LCNL President 2025 – 2027</p>
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

  <!-- <div class="card shadow-sm border-0">
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
        <h5 class="card-title mb-1">Mahila Committee</h5>
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
