<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero bg-brand d-flex align-items-center">
  <div class="container">
    <div class="row align-items-center g-4 text-center text-lg-start">
      
      <!-- Text column -->
      <div class="col-lg-8">
        <h2 class="fw-bold text-white fs-2">Lohana Community of North London</h2>
        <p class="text-white-50 fs-5 mb-3">
          The Lohana Community North London (LCNL) has been bringing people together since 1976. 
          Serving over 2,300 families, we celebrate our culture, support charitable causes, and 
          create spaces where our community can thrive. Proud of our heritage, we move forward 
          together for future generations.
        </p>
      </div>

      <!-- Logo column -->
      <div class="col-lg-4 text-center text-lg-end">
        <img src="<?= base_url('assets/img/lcnl-logo.png') ?>" 
             class="img-fluid" 
             style="max-height:120px;" 
             alt="LCNL">
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
          
          <!-- President photo floated left -->
         <img src="<?= base_url('uploads/committee/ronak-paw.jpg') ?>" 
     class="float-start me-3 mb-2 rounded-circle shadow-sm" 
     style="width:250px; height:250px; object-fit:cover; object-position: top;" 
     alt="President Photo">

          <p>Jai Shree Krishna | Jai Shree Ram | Jai Jalaram</p>

          <p>To be the youngest LCNL President is one thing but to be the first LCNL President
          that was born in the UK is also testament to the hard work all those before me
          have put in. During my two-year term we look forward to celebrating 50 years of
          LCNL and again that’s all down to the work of all the committees, presidents and
          support from the members over the years.</p>

          <p>I am blessed to have such a good team around me but this year I have decided
          to run things in a slightly different way. We have 25 or so portfolios that put on
          various events and run various services for the community and I asked all of the
          Executive Committee to say where they would like to help out and lead. We then
          split everyone into the various portfolios and appointed a lead for each portfolio.</p>

          <p>Over time you will hear from the various heads and portfolios about the events
          they are putting on and the work they are doing. This way of working, I hope,
          will bring more togetherness and a drive from within from all of the Executive
          Committee members.</p>

          <p>As a committee we will look to continue with the religious and cultural events
          that have been the backbone of the LCNL for years but also look to improve
          them in our own ways too. We will also focus on bringing more events with more
          variety that will appeal to all age groups too. A particular focus will be on children
          because If we do not engage children in our events, it will be difficult for LCNL to thrive for another 50 years. So, it is vital to attract them to more events and their
          parents too.</p>

          <p>I also want to make sure that the members of the community – all of you – feel
          part of LCNL and therefore attend more events and enjoy them. I make a personal
          request to you all to support all the events we do from Navratri to mehfil nights
          to bingo to golf, please keep supporting every event and feel free to contact me if
          you have any issues or even have ideas for any events.</p>

          <p>Lastly, I would like to thank the Executive Committee and all of our sponsors,
          yajman, well wishers and supporters without whom none of the events and work
          LCNL does would be possible. A special thanks to Dipen Tanna for putting this
          News & Events together too.</p>

          <p>I look forward to seeing you all at the upcoming events!</p>

          <p class="fw-bold mb-0">Ronak Paw</p>
          <p class="mb-0">LCNL President 2025 - 2027</p>
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

<!-- Mahila Mandal -->
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
