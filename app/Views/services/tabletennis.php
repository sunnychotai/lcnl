<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-cobalt d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-4 py-md-5">
    <h1 class="fw-bold display-5 mb-3">🏓 Table Tennis</h1>
    <p class="lead fs-5 mb-0">Community • Coaching • Competition</p>
  </div>
</section>

<div class="container py-5">

  <!-- Wide Image -->
  <div class="mb-5 text-center">
    <img src="/assets/img/services/tabletennis.png" alt="Table Tennis Club"
      class="img-fluid rounded-4 shadow-lg border border-light">
  </div>

  <!-- Training Times Highlight -->
  <div class="row justify-content-center mb-2">
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
  <div class="row justify-content-center mb-2">
    <div class="col-lg-10">
      <div class="lcnl-card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-4 p-md-5">

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
              The Club currently has <strong>5 teams</strong> in the Wembley & Harrow Leagues (Divisions 2, 4, 5, 6 and
              7)
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
              <a href="https://www.instagram.com/lcnl.tabletennis" target="_blank" class="fw-bold text-decoration-none">
                Instagram page @lcnl.tabletennis
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

  <!-- Membership Section -->
  <div class="row justify-content-center mb-5">
    <div class="col-lg-10">
      <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-body p-0">
          <div class="bg-primary text-white text-center py-4">
            <h2 class="fw-bold mb-2">
              <i class="bi bi-clipboard-check-fill me-2"></i>
              Become a Member
            </h2>
            <p class="mb-0 fs-5">Join the LCNL Table Tennis Club today!</p>
          </div>

          <div class="p-4 p-md-5">
            <p class="lead text-center mb-4">
              Ready to join our thriving table tennis community? Choose your preferred way to apply:
            </p>

            <div class="row g-4">
              <!-- Online Form -->
              <div class="col-md-6">
                <div class="card h-100 border-primary shadow-sm hover-lift">
                  <div class="card-body text-center p-4">
                    <div class="mb-3">
                      <i class="bi bi-laptop text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Online Form</h4>
                    <p class="text-muted mb-4">
                      Fill out the membership form online - quick and easy!
                    </p>
                    <a href="https://forms.office.com/e/wYZczqhxtg" target="_blank"
                      class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm">
                      <i class="bi bi-box-arrow-up-right me-2"></i>
                      Complete Online Form
                    </a>
                  </div>
                </div>
              </div>

              <!-- PDF Download -->
              <div class="col-md-6">
                <div class="card h-100 border-danger shadow-sm hover-lift">
                  <div class="card-body text-center p-4">
                    <div class="mb-3">
                      <i class="bi bi-file-pdf text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">PDF Form</h4>
                    <p class="text-muted mb-4">
                      Download, print and submit the membership form
                    </p>
                    <a href="<?= base_url('assets/documents/lcnl_tabletennis_membership_jan25.pdf') ?>" target="_blank"
                      class="btn btn-danger btn-lg rounded-pill px-4 shadow-sm">
                      <i class="bi bi-download me-2"></i>
                      Download PDF Form
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <div class="alert alert-info border-0 shadow-sm mt-4" role="alert">
              <i class="bi bi-info-circle-fill me-2"></i>
              <strong>Need help?</strong> Contact us at
              <a href="mailto:tabletennis@lcnl.org" class="alert-link fw-bold">tabletennis@lcnl.org</a>
              if you have any questions about membership.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Instagram Feed Section -->
  <div class="mb-5">
    <div class="text-center mb-4">
      <h3 class="fw-bold mb-3">
        <i class="bi bi-instagram text-danger me-2"></i>
        Follow Us on Instagram
      </h3>
      <p class="text-muted">
        Stay updated with match results, training sessions and club news at
        <a href="https://www.instagram.com/lcnl.tabletennis" target="_blank" class="text-decoration-none fw-bold">
          @lcnl.tabletennis
        </a>
      </p>
    </div>

    <div class="row justify-content-center">
      <div class="col-12 col-lg-10 col-xl-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
          <div class="card-body p-3 p-md-4">
            <!-- Instagram Embed -->
            <div class="instagram-embed-container">
              <blockquote class="instagram-media" data-instgrm-permalink="https://www.instagram.com/lcnl.tabletennis/"
                data-instgrm-version="14"
                style="background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px auto; max-width:540px; min-width:280px; padding:0; width:calc(100% - 2px);">
                <div style="padding:16px;">
                  <a href="https://www.instagram.com/lcnl.tabletennis/"
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

            <!-- Fallback link -->
            <noscript>
              <div class="text-center p-4">
                <a href="https://www.instagram.com/lcnl.tabletennis/" target="_blank"
                  class="btn btn-lg btn-outline-primary rounded-pill">
                  <i class="bi bi-instagram me-2"></i>
                  Visit @lcnl.tabletennis on Instagram
                </a>
              </div>
            </noscript>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- CTA -->
  <div class="text-center mt-5">
    <a href="mailto:tabletennis@lcnl.org" class="btn btn-lg btn-danger rounded-pill px-5 shadow-sm me-3 mb-3">
      <i class="bi bi-envelope-fill me-2"></i> Contact Us
    </a>
    <a href="https://forms.office.com/e/wYZczqhxtg" target="_blank"
      class="btn btn-lg btn-primary rounded-pill px-5 shadow-sm mb-3">
      <i class="bi bi-person-plus-fill me-2"></i> Join the Club
    </a>
  </div>

</div>

<!-- Instagram Embed Script -->
<script async src="//www.instagram.com/embed.js"></script>

<!-- Hover Effect CSS -->
<style>
  .hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
  }
</style>

<?= $this->endSection() ?>

