<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero -->
<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-1"><i class="bi bi-journal-check me-2"></i>Chopda Pujan Registration</h1>
    <p class="opacity-75 mb-0">Join us on 20 October 2025 @ 6PM at DLC</p>
  </div>
</section>

<!-- Ceremony Documents -->
<section class="py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="card shadow-sm border-0 no-hover">
          <div class="card-body p-4 p-md-5 text-center">
            <i class="bi bi-file-earmark-pdf text-brand fs-1 mb-3"></i>
            <h2 class="h3 fw-bold mb-3 text-brand">Ceremony Documents</h2>
            <p class="mb-4">Download the Chopda Pujan ceremony guide in your preferred language</p>
            
            <div class="row g-3 justify-content-center">
              <div class="col-md-5">
                <a href="<?= base_url('assets/documents/chopda-pujan/english.pdf') ?>" 
                   class="btn btn-outline-brand btn-lg w-100 d-flex align-items-center justify-content-center" 
                   download
                   target="_blank">
                  <i class="bi bi-download me-2"></i>
                  Download English Version
                </a>
              </div>
              <div class="col-md-5">
                <a href="<?= base_url('assets/documents/chopda-pujan/gujarati.pdf') ?>" 
                   class="btn btn-outline-brand btn-lg w-100 d-flex align-items-center justify-content-center" 
                   download
                   target="_blank">
                  <i class="bi bi-download me-2"></i>
                  Download Gujarati Version
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Video Section -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="card shadow-sm border-0 no-hover">
          <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
              <i class="bi bi-play-circle text-brand fs-1 mb-3"></i>
              <h2 class="h3 fw-bold mb-2 text-brand">LCNL Chopda Pujan 2025</h2>
            </div>
            
            <!-- YouTube Video Embed - Replace VIDEO_ID with actual YouTube video ID -->
            <div class="ratio ratio-16x9">
              <iframe 
                src="https://www.youtube.com/embed/VIDEO_ID" 
                title="Chopda Pujan Video" 
                allowfullscreen
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                loading="lazy">
              </iframe>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Event Context -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="card shadow-sm border-0 no-hover">
          <div class="card-body p-4 p-md-5">
            <h2 class="h3 fw-bold mb-4 text-brand">Chopda Pujan 2025</h2>
            <p class="lead mb-4">Join us for this sacred annual ceremony to mark the start of a prosperous New Year. We welcome all community members to this auspicious event.</p>
            
            <div class="row g-4">
              <div class="col-md-6">
                <div class="d-flex align-items-start">
                  <i class="bi bi-calendar-event text-brand fs-4 me-3"></i>
                  <div>
                    <h5 class="mb-1">Date</h5>
                    <p class="mb-0">Monday 20 October 2025</p>
                  </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="d-flex align-items-start">
                  <i class="bi bi-clock text-brand fs-4 me-3"></i>
                  <div>
                    <h5 class="mb-1">Timings</h5>
                    <p class="mb-0">Registration: 5:00 PM – 6:00 PM<br>Pooja: 6:00 PM – 7:30 PM</p>
                  </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="d-flex align-items-start">
                  <i class="bi bi-geo-alt text-brand fs-4 me-3"></i>
                  <div>
                    <h5 class="mb-1">Venue</h5>
                    <p class="mb-0">J.V. Gokal Hall<br>Dhamecha Lohana Centre, Harrow</p>
                  </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="d-flex align-items-start">
                  <i class="bi bi-currency-pound text-brand fs-4 me-3"></i>
                  <div>
                    <h5 class="mb-1">Donation</h5>
                    <p class="mb-0">£25 per pooja<br><small class="text-muted">(max 2 participants per Yajman)</small></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
  .auth-card { border-left: 6px solid var(--brand); border-radius: var(--radius); }
  .card-header.bg-accent1 { background-color: var(--accent1); }
  .card-header.bg-brand { background-color: var(--brand); }
  .text-brand { color: var(--brand); }
  .btn-outline-brand {
    color: var(--brand);
    border-color: var(--brand);
  }
  .btn-outline-brand:hover {
    background-color: var(--brand);
    border-color: var(--brand);
    color: white;
  }
</style>

<?= $this->endSection() ?>