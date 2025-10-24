<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-cobalt d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">Lohana Charitable Foundation</h1>
  </div>
</section>

<div class="container py-5">

  <!-- Logo in main body -->
  <div class="text-center mb-4">
    <img src="<?= base_url('assets/img/logos/lcf.jpg') ?>" 
         alt="Lohana Charitable Foundation Logo" 
         class="img-fluid shadow-sm rounded" 
         style="max-height:150px;">
  </div>

  <div class="mb-4">
    <p class="lead">
      The Trust Board of <strong>Lohana Charitable Foundations Ltd (LCF)</strong> is honoured to oversee 
      the governance of our charity and safeguard community assets for future generations.
    </p>
    <p>
      Through our subsidiary, <strong>Dhamecha Lohana Centre Ltd (DLC)</strong>, we manage essential operations 
      including functions, bookings, maintenance, improvements, security, and finance — ensuring the centre 
      runs smoothly, efficiently, and safely.
    </p>
    <p>
      We are thankful to the members and wider community for their generous donations and support since 
      the acquisition of the property on <strong>2nd March 2011</strong> and its inauguration on 
      <strong>29th June 2014</strong>.
    </p>
    <p>
      It is encouraging to see record attendance at LCNL community events and prayer meetings, reflecting 
      the strong spirit and unity of our members. Families are also invited to consider booking DLC for 
      personal celebrations. This not only provides an opportunity to enjoy the centre, but also supports 
      essential income. Alongside steady external bookings, we continue to explore ways to make full use 
      of this valuable facility for the community.
    </p>
    <p>
      With the dedication of the <strong>LCF Trustees</strong>, <strong>DLC Directors</strong>, 
      <strong>LCNL President</strong>, <strong>LCNL Secretary</strong>, 
      <strong>LCNL Executive Committee</strong>, and our many committed volunteers, 
      we remain focused on maintaining the highest standards and enhancing the centre 
      for the benefit of both our community and external users.
    </p>
  </div>

  <div class="bg-light p-3 rounded shadow-sm mb-4">
    <p class="mb-1"><strong>Vinod Thakrar</strong></p>
    <p class="mb-1">Chairperson (2024–2027)</p>
    <p class="mb-1">Lohana Charitable Foundation Ltd</p>
    <p class="mb-1">Charity Registered Number: <strong>1161788</strong></p>
    <p class="mb-0">Registered address: Brember Road, South Harrow, Middlesex, HA2 8AX</p>
  </div>

<hr class="my-5">

<h2 class="fw-bold text-center mb-4">Trustees</h2>

<div class="row g-4 justify-content-center">
  <?php
    // Define display order (edit this array as needed)
    $trustees = [
      'pradip-dhamecha.jpg'    => 'Pradip Dhamecha',
      'vinod-thakrar.jpg'      => 'Vinod Thakrar',
      'bhavesh-radia.jpg'      => 'Bhavesh Radia',
      'janu-kotecha.jpg'       => 'Janu Kotecha',
      'vimal-pau.jpg'          => 'Vimal Pau',
      'yatinbhai-dawada.jpg'   => 'Yatinbhai Dawada',
      'jagdish-nagrecha.jpg'   => 'Jagdish Nagrecha',
      'sudhir-karia.jpg'       => 'Sudhir Karia',
      'rashik-kantaria.jpg'    => 'Rashik Kantaria',
      'reena-popat.jpg'        => 'Reena Popat',
      'ramesh-kantaria.jpg'    => 'Ramesh Kantaria',
      'yagnish-chotai.jpg'     => 'Yagnish Chotai',
      'narendra-thakrar.jpg'   => 'Narendra Thakrar',
      'ronak-paw.jpg'   => 'Ronak Paw',
      'jeet-rughani.jpg'   => 'Jeet Rughani',
      'ajay-gokani.jpg'        => 'Ajay Gokani',      
    ];
  ?>

<div class="row g-4 justify-content-center">
  <?php foreach ($trustees as $file => $name): ?>
    <div class="col-6 col-sm-4 col-md-3 col-lg-2 text-center">
      <div class="trustee-card shadow-sm h-100 p-2">
        <img src="<?= base_url('assets/img/committee/lcf/' . $file) ?>" 
             alt="<?= esc($name) ?>" 
             class="trustee-photo">
        <p class="trustee-name"><?= esc($name) ?></p>
      </div>
    </div>
  <?php endforeach; ?>
</div>

  
</div>

</div>

<?= $this->endSection() ?>
