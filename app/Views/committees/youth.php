<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-cobalt d-flex align-items-center justify-content-center">
  <div class="container position-relative py-4">
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-3 gap-md-4 text-white">
      <!-- Logo -->
      <div class="flex-shrink-0">
        <img src="<?= base_url('assets/img/committee/lyc.jpg') ?>" alt="Lohana Youth Committee Logo"
          class="img-fluid rounded-circle shadow-lg"
          style="width: 100px; height: 100px; object-fit: cover; border: 4px solid rgba(255,255,255,0.3);">
      </div>

      <!-- Text Content -->
      <div class="text-center text-md-start">
        <h1 class="fw-bold display-6 mb-2">Lohana Youth Committee</h1>
        <p class="lead fs-5 mb-0">Supporting and empowering the youth of our community.</p>
      </div>
    </div>
  </div>
</section>

<div class="container py-5">

  <!-- About Section (logo removed from here) -->
  <p class="text-center mb-4">
    The Lohana Youth Committee (LYC) is dedicated to supporting, inspiring and empowering
    young people within our community. We aim to create opportunities for connection,
    personal growth and lasting friendships through cultural, social and sporting initiatives.
  </p>

  <!-- Join Us Section -->
  <div class="my-5">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="card-body p-0">
        <div class="row g-0">
          <!-- Left Column - Info -->
          <div class="col-lg-7 p-4 p-md-5">
            <h3 class="mb-4">
              <i class="bi bi-people-fill text-primary me-2"></i>
              Join Our Community
            </h3>
            <p class="lead mb-4">
              Connect with the Lohana Youth Committee and be part of our vibrant community!
              Stay updated on all our events, activities and make lasting friendships.
            </p>

            <h5 class="mt-4 mb-3 fw-bold">
              <i class="bi bi-check-circle text-success me-2"></i>
              Who Can Join?
            </h5>
            <ul class="list-unstyled mb-4">
              <li class="mb-3 d-flex align-items-start">
                <i class="bi bi-arrow-right-circle-fill text-primary me-3 mt-1"></i>
                <span>Families with at least one child between <strong>13-18 years old</strong></span>
              </li>
              <li class="mb-3 d-flex align-items-start">
                <i class="bi bi-arrow-right-circle-fill text-primary me-3 mt-1"></i>
                <span><strong>North London Mahajan</strong> is your closest Lohana community</span>
              </li>
              <li class="mb-3 d-flex align-items-start">
                <i class="bi bi-arrow-right-circle-fill text-primary me-3 mt-1"></i>
                <span>At least one parent is of <strong>Lohana origin</strong></span>
              </li>
            </ul>

            <!-- WhatsApp Button - Desktop/Mobile -->
            <div class="d-grid gap-3 d-md-flex">
              <a href="https://chat.whatsapp.com/FODohVokKuv1p2KgwrcSvt" target="_blank"
                class="btn btn-success btn-lg rounded-pill px-4 py-3 shadow-sm">
                <i class="bi bi-whatsapp me-2 fs-5"></i>
                Join WhatsApp Group
              </a>
              <a href="https://www.instagram.com/lohanayouthclubnl/" target="_blank"
                class="btn btn-outline-primary btn-lg rounded-pill px-4 py-3">
                <i class="bi bi-instagram me-2 fs-5"></i>
                Follow Us
              </a>
            </div>

            <p class="text-muted small mt-4 mb-0">
              <i class="bi bi-info-circle me-1"></i>
              We welcome all eligible families to join us in creating memorable experiences for our youth.
            </p>
          </div>

          <!-- Right Column - QR Code -->
          <div class="col-lg-5 bg-light d-flex align-items-center justify-content-center p-4 p-md-5">
            <div class="text-center">
              <h5 class="mb-3 fw-bold">Scan to Join</h5>
              <div class="bg-white p-3 rounded-3 shadow-sm d-inline-block">
                <img src="<?= base_url('assets/img/lyc_wa.png') ?>" alt="Join LYC WhatsApp Group QR Code"
                  class="img-fluid rounded" style="max-width: 220px; width: 100%;">
              </div>
              <p class="text-muted small mt-3 mb-0">
                <i class="bi bi-qr-code-scan me-1"></i>
                Point your camera at the QR code
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Instagram Feed Section -->
  <div class="my-5">
    <div class="text-center mb-4">
      <h3 class="mb-3">
        <i class="bi bi-instagram text-danger me-2"></i>
        Follow Us on Instagram
      </h3>
      <p class="text-muted">
        Stay updated with our latest events, photos and youth activities at
        <a href="https://www.instagram.com/lohanayouthclubnl/" target="_blank" class="text-decoration-none fw-bold">
          @lohanayouthclubnl
        </a>
      </p>
    </div>

    <div class="row justify-content-center">
      <div class="col-12 col-lg-10 col-xl-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
          <div class="card-body p-3 p-md-4">
            <!-- Instagram Embed -->
            <div class="instagram-embed-container">
              <blockquote class="instagram-media" data-instgrm-permalink="https://www.instagram.com/lohanayouthclubnl/"
                data-instgrm-version="14"
                style="background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px auto; max-width:540px; min-width:280px; padding:0; width:calc(100% - 2px);">
                <div style="padding:16px;">
                  <a href="https://www.instagram.com/lohanayouthclubnl/"
                    style="background:#FFFFFF; line-height:0; padding:0 0; text-align:center; text-decoration:none; width:100%;"
                    target="_blank">
                    <div style="display: flex; flex-direction: row; align-items: center;">
                      <div
                        style="background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 40px; margin-right: 14px; width: 40px;">
                      </div>
                      <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center;">
                        <div
                          style="background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 100px;">
                        </div>
                        <div
                          style="background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 60px;">
                        </div>
                      </div>
                    </div>
                    <div style="padding: 19% 0;"></div>
                    <div style="display:block; height:50px; margin:0 auto 12px; width:50px;">
                      <svg width="50px" height="50px" viewBox="0 0 60 60" version="1.1"
                        xmlns="http://www.w3.org/2000/svg">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                          <g transform="translate(-511.000000, -20.000000)" fill="#000000">
                            <g>
                              <path
                                d="M556.869,30.41 C554.814,30.41 553.148,32.076 553.148,34.131 C553.148,36.186 554.814,37.852 556.869,37.852 C558.924,37.852 560.59,36.186 560.59,34.131 C560.59,32.076 558.924,30.41 556.869,30.41 M541,60.657 C535.114,60.657 530.342,55.887 530.342,50 C530.342,44.114 535.114,39.342 541,39.342 C546.887,39.342 551.658,44.114 551.658,50 C551.658,55.887 546.887,60.657 541,60.657 M541,33.886 C532.1,33.886 524.886,41.1 524.886,50 C524.886,58.899 532.1,66.113 541,66.113 C549.9,66.113 557.115,58.899 557.115,50 C557.115,41.1 549.9,33.886 541,33.886 M565.378,62.101 C565.244,65.022 564.756,66.606 564.346,67.663 C563.803,69.06 563.154,70.057 562.106,71.106 C561.058,72.155 560.06,72.803 558.662,73.347 C557.607,73.757 556.021,74.244 553.102,74.378 C549.944,74.521 548.997,74.552 541,74.552 C533.003,74.552 532.056,74.521 528.898,74.378 C525.979,74.244 524.393,73.757 523.338,73.347 C521.94,72.803 520.942,72.155 519.894,71.106 C518.846,70.057 518.197,69.06 517.654,67.663 C517.244,66.606 516.755,65.022 516.623,62.101 C516.479,58.943 516.448,57.996 516.448,50 C516.448,42.003 516.479,41.056 516.623,37.899 C516.755,34.978 517.244,33.391 517.654,32.338 C518.197,30.938 518.846,29.942 519.894,28.894 C520.942,27.846 521.94,27.196 523.338,26.654 C524.393,26.244 525.979,25.756 528.898,25.623 C532.057,25.479 533.004,25.448 541,25.448 C548.997,25.448 549.943,25.479 553.102,25.623 C556.021,25.756 557.607,26.244 558.662,26.654 C560.06,27.196 561.058,27.846 562.106,28.894 C563.154,29.942 563.803,30.938 564.346,32.338 C564.756,33.391 565.244,34.978 565.378,37.899 C565.522,41.056 565.552,42.003 565.552,50 C565.552,57.996 565.522,58.943 565.378,62.101 M570.82,37.631 C570.674,34.438 570.167,32.258 569.425,30.349 C568.659,28.377 567.633,26.702 565.965,25.035 C564.297,23.368 562.623,22.342 560.652,21.575 C558.743,20.834 556.562,20.326 553.369,20.18 C550.169,20.033 549.148,20 541,20 C532.853,20 531.831,20.033 528.631,20.18 C525.438,20.326 523.257,20.834 521.349,21.575 C519.376,22.342 517.703,23.368 516.035,25.035 C514.368,26.702 513.342,28.377 512.574,30.349 C511.834,32.258 511.326,34.438 511.181,37.631 C511.035,40.831 511,41.851 511,50 C511,58.147 511.035,59.17 511.181,62.369 C511.326,65.562 511.834,67.743 512.574,69.651 C513.342,71.625 514.368,73.296 516.035,74.965 C517.703,76.634 519.376,77.658 521.349,78.425 C523.257,79.167 525.438,79.673 528.631,79.82 C531.831,79.965 532.853,80.001 541,80.001 C549.148,80.001 550.169,79.965 553.369,79.82 C556.562,79.673 558.743,79.167 560.652,78.425 C562.623,77.658 564.297,76.634 565.965,74.965 C567.633,73.296 568.659,71.625 569.425,69.651 C570.167,67.743 570.674,65.562 570.82,62.369 C570.966,59.17 571,58.147 571,50 C571,41.851 570.966,40.831 570.82,37.631">
                              </path>
                            </g>
                          </g>
                        </g>
                      </svg>
                    </div>
                    <div style="padding-top: 8px;">
                      <div
                        style="color:#3897f0; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:550; line-height:18px;">
                        View this profile on Instagram
                      </div>
                    </div>
                  </a>
                </div>
              </blockquote>
            </div>

            <!-- Fallback link if embed doesn't load -->
            <noscript>
              <div class="text-center p-4">
                <a href="https://www.instagram.com/lohanayouthclubnl/" target="_blank"
                  class="btn btn-lg btn-outline-primary rounded-pill">
                  <i class="bi bi-instagram me-2"></i>
                  Visit @lohanayouthclubnl on Instagram
                </a>
              </div>
            </noscript>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Events Section -->
  <div class="my-5">
    <h3 class="mb-4">Upcoming Youth Events</h3>
    <?php if (!empty($events)): ?>
      <div class="d-flex overflow-auto gap-3 pb-2">
        <?php foreach ($events as $event): ?>
          <a href="<?= base_url('events/' . $event['id']) ?>" class="text-decoration-none flex-shrink-0"
            style="width: 280px;">
            <div class="card shadow-sm border-0 h-100 event-card">
              <?php if (!empty($event['image'])): ?>
                <div class="event-img-wrapper">
                  <img src="<?= base_url($event['image']) ?>" class="card-img-top" alt="<?= esc($event['title']) ?>">
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
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-muted">No upcoming events at this time.</p>
    <?php endif; ?>
  </div>

  <hr class="my-5">

  <!-- Committee Section -->
  <h3 class="text-center mb-4">LYC Committee 2025–2027</h3>

  <!-- Committee Image -->
  <div class="text-center mb-4">
    <div class="row justify-content-center g-3">
      <div class="col-md-6">
        <img src="<?= base_url('assets/img/youth-committee-25-27.jpg') ?>"
          alt="Lohana Youth Committee 2025–2027 (Photo 1)" class="img-fluid rounded shadow-sm w-100"
          style="max-height:400px; object-fit:contain">
      </div>
      <div class="col-md-6">
        <img src="<?= base_url('assets/img/youth-committee-25-27-2.jpg') ?>"
          alt="Lohana Youth Committee 2025–2027 (Photo 2)" class="img-fluid rounded shadow-sm w-100"
          style="max-height:400px; object-fit:cover;">
      </div>
    </div>
  </div>

  <p class="text-center mb-5">
    We are excited to introduce our new committee. Together, we are committed to creating meaningful
    events and opportunities for our youth. Here are our bios:
  </p>

  <!-- Committee Members -->
  <div class="row g-4">
    <?php
    $committee = [
      [
        'name' => 'Nina Valanju',
        'bio' => 'Nina works as a Programme Director in Finance and is excited to help the youth of our community build strong, lasting connections.',
        'image' => 'nina-valanju.jpg',
      ],
      [
        'name' => 'Vishali Sodha',
        'bio' => 'Vishali is passionate about creating inspiring opportunities for young people. She is committed to strengthening community bonds and co-running initiatives for the LYC.',
        'image' => 'vishali-sodha.jpg',
      ],
      [
        'name' => 'Neal Rajdev',
        'bio' => 'Neal has served the Lohana community in South London and chaired his children\'s school PTA for five years, building a network of over 20 PTAs. He brings valuable experience in fostering collaboration and empowering youth.',
        'image' => 'neal-rajdev.jpg',
      ],
      [
        'name' => 'Leena Tanna',
        'bio' => 'Leena has been part of the LYC since its inception. She believes it offers young people a valuable chance to connect with their community and culture while forming lifelong friendships.',
        'image' => 'leena-tanna.jpg',
      ],
      [
        'name' => 'Neil Morjaria',
        'bio' => 'Neil is actively involved in initiatives such as the Lohana Table Tennis Club, the LCUK Sports Festival and the Raghuvanshi Charitable Trust. He is committed to strengthening family engagement and building connections among the youth.',
        'image' => 'neil-morjaria.jpg',
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
              <img src="<?= base_url($photo) ?>" alt="<?= esc($c['name']) ?>" class="rounded-circle shadow-sm"
                style="width:120px; height:120px; object-fit:cover;">
            </div>
            <h5 class="fw-bold mb-2"><?= esc($c['name']) ?></h5>
            <p class="text-muted"><?= esc($c['bio']) ?></p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<!-- Instagram Embed Script -->
<script async src="//www.instagram.com/embed.js"></script>

<?= $this->endSection() ?>

