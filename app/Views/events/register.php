<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero -->
<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-1"><i class="bi bi-journal-check me-2"></i>Chopda Pujan Registration</h1>
    <p class="opacity-75 mb-0">Join us on 20 October 2025 @ 6PM at DLC</p>
  </div>
</section>
<section>
<iframe src="https://www.cognitoforms.com/f/K8NtvjObHUG_pd--VmcMAg/296" allow="payment" style="border:0;width:100%;" height="1174"></iframe>
<script src="https://www.cognitoforms.com/f/iframe.js"></script>

</section>

<style>
  .auth-card { border-left: 6px solid var(--brand); border-radius: var(--radius); }
  .card-header.bg-accent1 { background-color: var(--accent1); }
  .card-header.bg-brand { background-color: var(--brand); }
</style>

<?= $this->endSection() ?>
