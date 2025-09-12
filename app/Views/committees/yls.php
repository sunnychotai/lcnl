<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-cobalt d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">Young Lohana Society</h1>
    <p class="lead fs-5 mb-0">Supporting and empowering the youth of our community.</p>
  </div>
</section>

<div class="container py-5">

  <!-- Tabs Navigation -->
  <ul class="nav nav-tabs justify-content-center" id="ylsTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="about-tab" data-bs-toggle="tab" data-bs-target="#about" type="button" role="tab">
        About
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" role="tab">
        Upcoming Events
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="committee-tab" data-bs-toggle="tab" data-bs-target="#committee" type="button" role="tab">
        Committee
      </button>
    </li>
  </ul>

  <!-- Tabs Content -->
  <div class="tab-content py-4" id="ylsTabsContent">

    <!-- About Tab -->
<div class="tab-pane fade show active" id="about" role="tabpanel">
  <div class="">
    <h2>About Us</h2>
    
    <div class="text-center mb-4">
      <img src="<?= base_url('assets/img/committee/yls-logo.png') ?>" 
           alt="YLS Logo" 
           class="img-fluid" 
           style="max-height:120px;">
    </div>

    <p>
      We would like to take this opportunity to introduce ourselves and how we were formed.
    </p>

    <p>
      With no official youth community in North London, LCNL invited a group of youngsters, many of whom had never met each other, to come together to discuss the future of young Lohanas. Many came to learn about the Lohana community, others were keen on the free pizza, but it was a great opportunity to meet fellow like minded Lohanas and discuss and brainstorm ideas about serving the young Lohanas in North London. So the “Young Lohana Society (YLS) was formed!
    </p>

    <p>
      The members of the committee are diverse allowing us to reach out to a wider audience. The youngest committee member is aged 18 and the eldest are in their 30s! Some of the members only moved to London in the last few years whilst others have been brought up here.
    </p>

    <p>
      So, what is our aim and purpose? To put it quite simply, our aim is to bring the Lohana youth together as well as preserving and strengthening our sense of belongingness within the community by promoting and encouraging the young lohanas to actively integrate and participate in community events. YLS is a youth organisation launched to capture and cater for the younger generation within the Lohana community.
    </p>

    <p>
      We will hold a variety of events ranging from religious, cultural and social including sports days, summer BBQ/Fete, mehfil night, quiz night, parties and many more.
    </p>

    <p>
      The LCNL motto is “we move forward together”, for the YLS it is simply a case of us all “growing up together”. Whether you’re a young Lohana or whether you’ve been part of the LCNL for years I hope you’ll take an active part in our future: whether it is suggesting ideas, attending functions, or even coming up to say hello to us!
    </p>

    <p>
      We look forward to seeing you all soon!
    </p>

    <a href="<?= base_url('events') ?>" class="btn btn-brand mt-3">
      View Upcoming Events
    </a>
  </div>
</div>


<!-- Events Tab -->
<div class="tab-pane fade" id="events" role="tabpanel">
  <h3 class="mb-4">Upcoming YLS Events</h3>
  
  <div class="container">
    <?php if (!empty($groupedEvents)): ?>
      <?php foreach ($groupedEvents as $month => $events): ?>
        <h2 class="mb-4 mt-5"><?= esc($month) ?></h2>
        <div class="d-flex overflow-auto gap-3 pb-2">
          <?php foreach ($events as $event): ?>
            <?php if (in_array($event['committee'], ['YLS'])): ?>
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
                          <?php if (!empty($event['time_from'])): ?>
                            · <?= date('H:i', strtotime($event['time_from'])) ?>
                          <?php endif; ?>
                        </small>
                      </div>
                    </div>
                  <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height:200px;">
                      <i class="bi bi-calendar-event fs-1 text-muted"></i>
                    </div>
                  <?php endif; ?>
                </div>
              </a>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No upcoming events found.</p>
    <?php endif; ?>
  </div>
</div>


    <!-- Committee Tab -->
   <div class="tab-pane fade" id="committee" role="tabpanel">
  <h3 class="mb-4 text-center">Committee Members</h3>
  <div class="row g-4">
    <?php foreach ($members as $m): ?>
      <div class="col-md-3 col-sm-6">
        <div class="card h-100 text-center">
          <?php 
            $basePath = 'assets/img/committee/';
            $filename = basename($m['image'] ?? ''); // only keep filename
            $imagePath = $basePath . $filename;

            if (empty($filename) || !is_file(FCPATH . $imagePath)) {
                $imagePath = $basePath . 'lcnl-placeholder.png';
            }
          ?>
          <img src="<?= base_url($imagePath) ?>" 
               class="card-img-top committee-photo" 
               alt="<?= esc($m['firstname'].' '.$m['surname']) ?>">

          <div class="card-body">
            <h5 class="card-title mb-1">
              <?= esc($m['firstname'].' '.$m['surname']) ?>
            </h5>
            <?php if (!empty($m['role'])): ?>
              <p class="text-muted mb-1"><?= esc($m['role']) ?></p>
            <?php endif; ?>
            <?php if (!empty($m['url'])): ?>
              <a href="<?= esc($m['url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">More</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

  </div>
</div>

<?= $this->endSection() ?>
