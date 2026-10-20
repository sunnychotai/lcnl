<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('partials/page_header', [
  'heading'    => 'Mahila Committee',
  'subheading' => 'Supporting and empowering the women of our community',
  'tone'       => 'ruby',
]) ?>

<div class="container py-4 py-md-5">
  <div class="cmt-page">

    <?= view('committees/_more', ['current' => 'mahila']) ?>

    <figure class="cmt-group-photo">
      <img src="<?= base_url('assets/img/committee/mahila-committee.jpg') ?>"
        alt="The LCNL Mahila Committee 2025–27"
        width="1920" height="699" decoding="async" fetchpriority="high">
      <figcaption>LCNL Mahila Committee 2025–27</figcaption>
    </figure>

    <section class="cmt-section cmt-prose" aria-labelledby="mahila-about">
      <h2 id="mahila-about" class="cmt-section-title">About us</h2>
      <p class="cmt-lead">
        The Mahila Committee has been at the heart of LCNL, bringing women together through cultural, social and
        charitable activities.
      </p>
      <p>
        We encourage everyone to participate and celebrate our traditions while supporting each other.
      </p>
    </section>

    <section class="cmt-section" aria-labelledby="mahila-events">
      <h2 id="mahila-events" class="cmt-section-title">Upcoming Mahila events</h2>
      <?= view('committees/_events', ['events' => $events, 'who' => 'Mahila']) ?>
    </section>
  </div>
</div>

<?= $this->endSection() ?>
