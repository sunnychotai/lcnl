<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-cobalt d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">Lohana Youth Committee</h1>
    <p class="lead fs-5 mb-0">Supporting and empowering the youth of our community.</p>
  </div>
</section>

<div class="container py-5">

  <!-- Tabs Navigation -->
  <ul class="nav nav-tabs justify-content-center" id="youthTabs" role="tablist">
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
  <div class="tab-content py-4" id="youthTabsContent">

    <!-- About Tab -->
<div class="tab-pane fade show active" id="about" role="tabpanel">
  <div class="">
    <h2>About Us</h2>
    
   
    <p>
      Placeholder
    </p>

    
  </div>
</div>


<!-- Events Tab -->
<div class="tab-pane fade" id="events" role="tabpanel">
  <h3 class="mb-4">Upcoming Youth Events</h3>
  
  <div class="container">
    <?php if (!empty($groupedEvents)): ?>
      <?php foreach ($groupedEvents as $month => $events): ?>
        <h2 class="mb-4 mt-5"><?= esc($month) ?></h2>
        <div class="d-flex overflow-auto gap-3 pb-2">
          <?php foreach ($events as $event): ?>
            <?php if (in_array($event['committee'], ['Youth'])): ?>
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
