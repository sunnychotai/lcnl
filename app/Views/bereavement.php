<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<div class="hero hero-rangoli-grey d-flex align-items-center justify-content-center">
  <div class="overlay"></div>
  <div class="container position-relative text-center">
    <h1 class="text-white fw-bold">Bereavement Support Information</h1>
    <p class="text-white-50">Support & Assistance for the Lohana Community</p>
  </div>
</div>


<div class="container py-5">
  <!-- Image + Text side by side -->
  <div class="d-flex flex-wrap flex-row-reverse align-items-start mb-3">
    <!-- Image -->
    <div style="flex: 0 0 300px; max-width: 300px; margin-right: 20px; margin-bottom: 20px;">
      <img src="<?= base_url('assets/img/shiva.png') ?>" 
           alt="Shiva" 
           class="img-fluid rounded" 
           style="width: 100%; height: auto;">
    </div>

    <!-- Text -->
    <div style="flex: 1; min-width: 300px;">
      <p>
        The Bereavement Committee has always done their utmost to help and assist the bereaved families, 
        handling every matter sympathetically and sensitively to ensure that all the requirements of the 
        family are fulfilled as per their wishes. We are always available to provide the guidance and the 
        assistance during the time of mourning and assist in making the necessary arrangements starting 
        from the Prathna to the Funeral. We have a dedicated team to perform the Prathna according to 
        the wishes of the family in mourning. For the families that wish to have Bhajans, we also have 
        a team who are happy to provide this service for free. 
      </p>

      <p>
        We currently have over <strong>3,250 registered recipients</strong> for the bereavement emails and from 
        the 3-month period <strong>1st August 2022 to 31st October 2022</strong>, over <strong>539,000 emails</strong> 
        have been sent. This averages around <strong>11 emails per week</strong>. This is a totally free service LCNL provides 
        and is open to everyone including non-Lohanas.
      </p>

      <p>
        We still have a substantial number of recipients who have not re-registered under GDPR compliance. 
        To register to receive these emails (no limit on how many family members can register), 
        <a href="mailto:bereavement@lcnl.org">bereavement@lcnl.org</a>
      </p>

      <p>
        I would like this opportunity to thank the entire bereavement team for their hard work. 
        The feedback from the community is extremely positive as we wholeheartedly assist the family in mourning. 
        I also thank President and the EC committee members for their continued support.
      </p>
    </div>
  </div>

<!-- Contacts -->
<h5 class="mt-4 mb-3">Contacts</h5>
<div class="row g-4">
  <div class="col-md-6">
    <div class="card h-100 shadow-sm">
      <div class="card-body">
        <h6 class="card-title mb-2">Vinu Kotecha</h6>
        <p class="mb-1"><strong>Chairman – Bereavement Committee</strong></p>
        <p class="mb-1">
          Email: <a href="mailto:vinodk52@aol.com">vinodk52@aol.com</a><br>
          Tel: +44 7956 847764
        </p>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card h-100 shadow-sm">
      <div class="card-body">
        <h6 class="card-title mb-2">Rajubhai Sawjani</h6>
        <p class="mb-1"><strong>Bereavement Committee</strong></p>
        <p class="mb-1">
          Email: <a href="mailto:rtsawjani@btinternet.com">rtsawjani@btinternet.com</a><br>
          Tel: +44 7941 355358
        </p>
      </div>
    </div>
  </div>
</div>

  <!-- FAQ Section (dynamic from DB) -->
  <div class="faq mt-5">
    <h3 class="mb-4">Frequently Asked Questions</h3>
    <?= view('faqs/_accordion', ['faqs' => $faqs]) ?>
  </div>
</div>

</div>
</div>

<?= $this->endSection() ?>
