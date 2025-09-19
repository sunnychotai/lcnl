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
  </ul>

  <!-- Tabs Content -->
  <div class="tab-content py-4" id="youthTabsContent">

    <!-- About Tab (now includes committee content) -->
    <div class="tab-pane fade show active" id="about" role="tabpanel">
      <div class="text-center mb-4">
  <img src="<?= base_url('assets/img/committee/lyc.jpg') ?>" 
       alt="Lohana Youth Committee Logo" 
       class="img-fluid rounded shadow-sm mb-3" 
       style="max-height:150px; object-fit:contain;">
</div>

<p>
  The Lohana Youth Committee (LYC) is dedicated to supporting, inspiring and empowering
  young people within our community. We aim to create opportunities for connection,
  personal growth and lasting friendships through cultural, social and sporting initiatives.
</p>


      <hr class="my-5">

      <h3 class="text-center mb-4">LYC Committee 2025–2027</h3>

      <!-- Committee Image -->
<div class="text-center mb-4">
  <div class="row justify-content-center g-3">
    <div class="col-md-6">
      <img src="<?= base_url('assets/img/youth-committee-25-27.jpg') ?>" 
           alt="Lohana Youth Committee 2025–2027 (Photo 1)" 
           class="img-fluid rounded shadow-sm w-100" 
           style="max-height:400px; object-fit:contain">
    </div>
    <div class="col-md-6">
      <img src="<?= base_url('assets/img/youth-committee-25-27-2.jpg') ?>" 
           alt="Lohana Youth Committee 2025–2027 (Photo 2)" 
           class="img-fluid rounded shadow-sm w-100" 
           style="max-height:400px; object-fit:cover;">
    </div>
  </div>
</div>


      <p class="text-center mb-5">
        We are excited to introduce our new committee. Together, we are committed to creating meaningful
        events and opportunities for our youth. Here are our bios:
      </p>

      <div class="row g-4">

  <?php 
    $committee = [
      [
        'name' => 'Nina Valanju',
        'bio'  => 'Nina works as a Programme Director in Finance and is excited to help the youth of our community build strong, lasting connections.',
        'image'=> 'nina-valanju.jpg',
      ],
      [
        'name' => 'Vishali Sodha',
        'bio'  => 'Vishali is passionate about creating inspiring opportunities for young people. She is committed to strengthening community bonds and co-running initiatives for the LYC.',
        'image'=> 'vishali-sodha.jpg',
      ],
      [
        'name' => 'Neal Rajdev',
        'bio'  => 'Neal has served the Lohana community in South London and chaired his children’s school PTA for five years, building a network of over 20 PTAs. He brings valuable experience in fostering collaboration and empowering youth.',
        'image'=> 'neal-rajdev.jpg',
      ],
      [
        'name' => 'Leena Tanna',
        'bio'  => 'Leena has been part of the LYC since its inception. She believes it offers young people a valuable chance to connect with their community and culture while forming lifelong friendships.',
        'image'=> 'leena-tanna.jpg',
      ],
      [
        'name' => 'Neil Morjaria',
        'bio'  => 'Neil is actively involved in initiatives such as the Lohana Table Tennis Club, the LCUK Sports Festival and the Raghuvanshi Charitable Trust. He is committed to strengthening family engagement and building connections among the youth.',
        'image'=> 'neil-morjaria.jpg',
      ],
    ];

    $basePath = 'assets/img/committee/';
    $placeholder = $basePath . 'lcnl-placeholder.png';
  ?>

  <?php foreach ($committee as $c): ?>
    <?php 
      $filePath = $basePath . $c['image'];
      $photo = is_file(FCPATH . $filePath) ? $filePath : $placeholder;
    ?>
    <div class="col-md-6">
      <div class="card h-100 border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-body text-center p-4">
          <div class="mb-3">
            <img src="<?= base_url($photo) ?>" 
                 alt="<?= esc($c['name']) ?>" 
                 class="rounded-circle shadow-sm" 
                 style="width:120px; height:120px; object-fit:cover;">
          </div>
          <h5 class="fw-bold mb-2"><?= esc($c['name']) ?></h5>
          <p class="text-muted"><?= esc($c['bio']) ?></p>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

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

  </div>
</div>

<?= $this->endSection() ?>
