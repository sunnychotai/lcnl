<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('partials/page_header', [
  'heading'    => 'Lohana Charitable Foundation',
  'subheading' => 'Looking after the Dhamecha Lohana Centre for future generations',
  'tone'       => 'lcf',
  'logo'       => 'assets/img/logos/lcf.jpg',
]) ?>

<div class="container py-4 py-md-5">
  <div class="cmt-page">

    <?= view('committees/_more', ['current' => 'lcf']) ?>

    <div class="row g-4 g-lg-5 mb-2">
      <section class="col-lg-8 cmt-prose" aria-labelledby="lcf-about">
        <h2 id="lcf-about" class="cmt-section-title">About the LCF</h2>
        <p class="cmt-lead">
          The Trust Board of <strong>Lohana Charitable Foundations Ltd (LCF)</strong> is honoured to oversee the
          governance of our charity and safeguard community assets for future generations.
        </p>
        <p>
          Through our subsidiary, <strong>Dhamecha Lohana Centre Ltd (DLC)</strong>, we manage essential operations
          including functions, bookings, maintenance, improvements, security and finance, ensuring the centre runs
          smoothly, efficiently and safely.
        </p>
        <p>
          We are thankful to the members and wider community for their generous donations and support since the
          acquisition of the property on 2nd March 2011 and its inauguration on 29th June 2014.
        </p>
        <p>
          It is encouraging to see record attendance at LCNL community events and prayer meetings, reflecting the
          strong spirit and unity of our members. Families are also invited to consider booking DLC for personal
          celebrations. This not only provides an opportunity to enjoy the centre, but also supports essential income.
          Alongside steady external bookings, we continue to explore ways to make full use of this valuable facility
          for the community.
        </p>
        <p>
          With the dedication of the LCF Trustees, DLC Directors, LCNL President, LCNL Secretary, LCNL Executive
          Committee, and our many committed volunteers, we remain focused on maintaining the highest standards and
          enhancing the centre for the benefit of both our community and external users.
        </p>
        <p class="cmt-signature">
          <strong>Vinod Thakrar</strong><br>
          Chairperson (2024–2027), Lohana Charitable Foundation Ltd
        </p>
      </section>

      <aside class="col-lg-4" aria-labelledby="lcf-facts">
        <div class="cmt-facts cmt-tone-lcf">
          <h2 id="lcf-facts" class="visually-hidden">Key facts</h2>
          <dl>
            <dt>Property acquired</dt>
            <dd>2 March 2011</dd>
            <dt>Centre inaugurated</dt>
            <dd>29 June 2014</dd>
            <dt>Registered charity</dt>
            <dd>No. 1161788</dd>
            <dt>Registered address</dt>
            <dd>Brember Road, South Harrow, Middlesex, HA2 8AX</dd>
          </dl>
          <a href="<?= base_url('dlc-hire') ?>" class="btn btn-outline-dark rounded-pill w-100">Hire the centre</a>
        </div>
      </aside>
    </div>

    <section class="cmt-section" aria-labelledby="lcf-trustees">
      <h2 id="lcf-trustees" class="cmt-section-title">Trustees</h2>
      <?= view('committees/_people', ['people' => $trustees]) ?>
    </section>
  </div>
</div>

<?= $this->endSection() ?>
