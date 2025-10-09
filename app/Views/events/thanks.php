<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-gold d-flex align-items-center justify-content-center">
  <div class="container text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-1"><i class="bi bi-check-circle-fill me-2"></i>Thank You!</h1>
    <p class="opacity-75 mb-0">Your registration has been received.</p>
  </div>
</section>

<div class="container py-5">
  <div class="row justify-content-center align-items-stretch g-4">
    
    <!-- Left: Event flier -->
    <div class="col-lg-5 d-flex">
      <div class="card shadow-sm border-0 flex-fill overflow-hidden">
        <img src="<?= base_url('assets/img/events/chopda_pujan.png') ?>" 
             alt="Chopda Pujan Event Flier" 
             class="img-fluid w-100 h-100 object-fit-cover rounded-start">
      </div>
    </div>

    <!-- Right: Thank-you message -->
    <div class="col-lg-7 d-flex">
      <div class="card shadow-sm border-0 flex-fill no-hover">
        <div class="card-body p-4">
          <p>Dear <?= esc(session()->getFlashdata('first_name') ?? 'Devotee') ?>,</p>

          <p>Thank you for registering for the <strong>LCNL Chopda Poojan</strong> on the 20th of October at DLC at 6 pm.</p>

          <p>Please note that your registration is not complete until payment of 
            <strong>£25</strong> has been made to the LCNL account using the reference 
            <strong>CP – FirstNameSurname</strong>.
          </p>

          <p>Once payment is made, your registration will be confirmed. We look forward to seeing you on the day.</p>

          <p>Please bring your book and swaroop of Lord Ganesh. All other samagri will be provided.</p>

          <p>For queries, please contact <strong>Madhuben</strong> on 07500 701 318 or 
            <strong>Vishal</strong> on 07732 010 955.
          </p>

          <p class="mb-0">Kind Regards,<br><strong>LCNL Committee</strong></p>
        </div>

        <div class="card-footer text-center bg-light">
          <a href="<?= base_url('/') ?>" class="btn btn-accent rounded-pill px-4">
            <i class="bi bi-house-door me-1"></i>Return Home
          </a>
        </div>
      </div>
    </div>

  </div>
</div>

<style>
  @media (min-width: 992px) {
    .object-fit-cover { object-fit: cover; height: 100%; }
  }
</style>

<?= $this->endSection() ?>
