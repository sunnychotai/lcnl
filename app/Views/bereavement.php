<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-midnightindigo d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">Bereavement Support Information</h1>
    <p class="lead fs-5 mb-0">Support &amp; Assistance for the Lohana Community</p>
  </div>
</section>

<div class="container py-3">

  <!-- Image + Text side by side -->
  <div class="d-flex flex-wrap flex-row-reverse align-items-start mb-2">

    <!-- Shiva Image -->
    <div class="shiva-img-wrapper" 
         style="flex:0 0 300px; max-width:300px; margin-right:20px; margin-bottom:20px;">
      <img src="<?= base_url('assets/img/shiva.png') ?>" 
           alt="Shiva" 
           class="img-fluid rounded">
    </div>

    <!-- Text -->
    <div style="flex:1; min-width:300px;">
      <p>
        The Bereavement Committee is committed to supporting bereaved families with compassion and sensitivity, 
        ensuring that all requirements are met according to the family’s wishes. We are available to provide 
        guidance during the time of mourning and to help with arrangements, from the Prathna to the Funeral. 
        A dedicated team is available to perform the Prathna, and for families who wish to hold Bhajans, 
        we also have a volunteer team who provide this service free of charge.
      </p>

      <p>
        We currently have over <strong>3,250 registered recipients</strong> for bereavement emails. 
        Between <strong>1 August and 31 October 2022</strong>, more than <strong>539,000 emails</strong> were sent — 
        averaging around <strong>11 emails per week</strong>. This is a free LCNL service and is open to everyone, 
        including non-Lohanas.
      </p>

      <p>
        A large number of recipients have yet to re-register under GDPR rules. 
        To register (no limit on how many family members can do so), please email: 
        <a href="mailto:bereavement@lcnl.org">bereavement@lcnl.org</a>
      </p>

      <p>
        I would like to thank the entire Bereavement Team for their tireless efforts. 
        Feedback from the community has been overwhelmingly positive, and I also thank the President 
        and EC Committee members for their continued support.
      </p>
    </div>
  </div>

  <!-- Contacts -->
  <h5 class="mt-2 mb-3">Contacts</h5>
  <div class="row g-4">
    <div class="col-md-6">
      <div class="card-bereavement h-100">
        <h6 class="card-title mb-2">Vinu Kotecha</h6>
        <p class="mb-1"><strong>Chairman – Bereavement Committee</strong></p>
        <p class="mb-1">
          Email: <a href="mailto:vinodk52@aol.com">vinodk52@aol.com</a><br>
          Tel: +44 7956 847764
        </p>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card-bereavement h-100">
        <h6 class="card-title mb-2">Rajubhai Sawjani</h6>
        <p class="mb-1"><strong>Bereavement Committee</strong></p>
        <p class="mb-1">
          Email: <a href="mailto:rtsawjani@btinternet.com">rtsawjani@btinternet.com</a><br>
          Tel: +44 7941 355358
        </p>
      </div>
    </div>
  </div>

  <!-- FAQ Section (dynamic from DB) -->
  <div class="faq mt-3">
    <h3 class="mb-4">Frequently Asked Questions</h3>
    <?= view('faqs/_accordion', ['faqs' => $faqs]) ?>
  </div>
</div>

<?= $this->endSection() ?>
