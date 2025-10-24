<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-cobalt d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-4 py-md-5">
    <h1 class="fw-bold display-5 mb-3">Table Tennis</h1>
    <p class="lead fs-5 mb-0">🏓 Community • Coaching • Competition</p>
  </div>
</section>

<div class="container py-5">

  <!-- Wide Image -->
  <div class="mb-5 text-center">
    <img src="/assets/img/services/tabletennis.png" 
         alt="Table Tennis Club" 
         class="img-fluid rounded-4 shadow-lg border border-light">
  </div>

  <!-- Training Times Highlight -->
  <div class="row justify-content-center mb-1">
    <div class="col-md-8">
      <div class="lcnl-card border-0 shadow-sm rounded-4 bg-light text-center p-4">
        <h4 class="fw-bold text-primary mb-3">
          <i class="bi bi-calendar-event-fill me-2"></i> Training & Practice Times
        </h4>
        <p class="fs-5 text-muted mb-2">
          <i class="bi bi-geo-alt-fill text-danger me-1"></i> <strong>RCT Sports Hall</strong>
        </p>
        <p class="fs-5 text-muted mb-0">
          <i class="bi bi-clock-fill text-success me-1"></i> <strong>Sunday mornings, 8.30 am – 1.00 pm</strong>
        </p>
        <p class="text-muted small mt-2">Guests and first-timers are welcome!</p>
      </div>
    </div>
  </div>

  <!-- Club Information -->
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="lcnl-card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-4 p-md-1">
          
          <!-- Title -->
          <h2 class="fw-bold mb-4 text-center text-primary">
            <i class="bi bi-trophy-fill me-2 text-danger"></i> 
            About the LCNL Table Tennis Club
          </h2>

          <!-- Intro -->
          <p class="fs-5 text-secondary mb-4">
            The <strong>LCNL Table Tennis Club</strong> is going from strength to strength and is thriving with 
            over <strong>100 members</strong>, including juniors and ladies. Active members play and practice at 
            <strong>RCT Sports Hall</strong> on Sunday mornings from <strong>8.30 am to 1.00 pm</strong>. 
            Guests and first-timers are always welcome!
          </p>

          <!-- Coaching -->
          <div class="mb-4">
            <h5 class="fw-bold text-success mb-2"><i class="bi bi-mortarboard-fill me-2"></i> Coaching</h5>
            <p class="fs-5 text-secondary mb-0">
              We offer <strong>professional coaching</strong> for all ages and abilities, and we encourage 
              more juniors to come forward to improve their skills and techniques.
            </p>
          </div>

          <!-- League -->
          <div class="mb-4">
            <h5 class="fw-bold text-warning mb-2"><i class="bi bi-people-fill me-2"></i> Leagues & Tournaments</h5>
            <p class="fs-5 text-secondary mb-0">
              The Club currently has <strong>5 teams</strong> in the Wembley & Harrow Leagues (Divisions 2, 4, 5, 6 and 7) 
              and competes in tournaments against other clubs — including the annual friendly against 
              Highfield Table Tennis Club in Wellingborough.
            </p>
          </div>

          <!-- Contact -->
          <div class="mb-4">
            <h5 class="fw-bold text-info mb-2"><i class="bi bi-envelope-fill me-2"></i> Get Involved</h5>
            <p class="fs-5 text-secondary mb-0">
              Interested in a trial session, playing for leisure or competition, or becoming a member?  
              Contact our committee at 
              <a href="mailto:tabletennis@lcnl.org" class="fw-bold text-decoration-none">tabletennis@lcnl.org</a>.  
              See regular updates on our 
              <a href="https://instagram.com/LCNL.tabletennis" target="_blank" class="fw-bold text-decoration-none">
                Instagram page @LCNL.tabletennis
              </a>.
            </p>
          </div>

          <!-- Signature -->
          <div class="border-top pt-3 mt-4 text-end">
            <p class="mb-0 fw-bold">Neil Morjaria</p>
            <p class="text-muted">President, LCNL Table Tennis Club</p>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- CTA -->
  <div class="text-center mt-5">
    <a href="mailto:tabletennis@lcnl.org" class="btn btn-lg btn-danger rounded-pill px-5 shadow">
      <i class="bi bi-person-plus-fill me-2"></i> Join the Club
    </a>
  </div>

</div>

<?= $this->endSection() ?>
