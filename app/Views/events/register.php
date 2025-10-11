<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero -->
<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-1"><i class="bi bi-journal-check me-2"></i>Chopda Pujan Registration</h1>
    <p class="opacity-75 mb-0">Join us on 20 October 2025 @ 6PM at DLC</p>
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

<!-- Registration Form -->
<section class="py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <h3 class="text-center mb-4">Complete Your Registration</h3>
        <iframe src="https://www.cognitoforms.com/f/K8NtvjObHUG_pd--VmcMAg/296" allow="payment" style="border:0;width:100%;" height="1174"></iframe>
        <script src="https://www.cognitoforms.com/f/iframe.js"></script>
      </div>
    </div>
  </div>
</section>

<style>
  .auth-card { border-left: 6px solid var(--brand); border-radius: var(--radius); }
  .card-header.bg-accent1 { background-color: var(--accent1); }
  .card-header.bg-brand { background-color: var(--brand); }
  .text-brand { color: var(--brand); }
</style>

<?= $this->endSection() ?>
