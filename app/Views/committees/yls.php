<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('partials/page_header', [
  'heading'    => 'Young Lohana Society',
  'subheading' => 'Growing up together, as part of LCNL',
  'tone'       => 'yls',
]) ?>

<div class="container py-4 py-md-5">
  <div class="cmt-page yls-page">

    <?= view('committees/_more', ['current' => 'yls']) ?>

    <!-- Intro -->
    <div class="yls-intro">
      <img src="<?= base_url('assets/img/committee/yls-logo-320.webp') ?>"
        alt="Young Lohana Society logo" class="yls-logo" width="160" height="160" decoding="async">
      <div>
        <h2 class="yls-heading mt-0">Who we are</h2>
        <p class="yls-lead mb-0">
          YLS is a youth organisation launched to capture and cater for the younger generation within the
          Lohana community, bringing young Lohanas in North London together.
        </p>
      </div>
    </div>

    <section class="yls-section">
      <h2 class="yls-heading">How we started</h2>
      <p>
        With no official youth community in North London, LCNL invited a group of youngsters, many of whom had never
        met each other, to come together to discuss the future of young Lohanas. Many came to learn about the Lohana
        community, others were keen on the free pizza, but it was a great opportunity to meet fellow like-minded
        Lohanas and to brainstorm ideas for serving the young Lohanas of North London. And so the Young Lohana
        Society was formed!
      </p>
      <p class="mb-0">
        Our committee is diverse, which lets us reach a wider audience. The youngest member is 18 and the eldest are in
        their 30s. Some only moved to London in the last few years; others have grown up here.
      </p>
    </section>

    <section class="yls-section">
      <h2 class="yls-heading">Our aim</h2>
      <p class="mb-0">
        To put it simply, our aim is to bring the Lohana youth together, and to preserve and strengthen our sense of
        belonging within the community by encouraging young Lohanas to take an active part in community events.
      </p>
    </section>

    <section class="yls-section">
      <h2 class="yls-heading">What we do</h2>
      <p>We hold a variety of religious, cultural and social events, including:</p>
      <ul class="yls-activities">
        <li>Sports days</li>
        <li>Summer BBQ &amp; fete</li>
        <li>Mehfil nights</li>
        <li>Quiz nights</li>
        <li>Parties</li>
        <li>…and many more</li>
      </ul>
    </section>

    <!-- Motto -->
    <figure class="yls-motto">
      <blockquote class="mb-0">
        <p>Growing up together</p>
      </blockquote>
      <figcaption>
        The LCNL motto is “we move forward together”. For YLS, it is simply a case of us all growing up together.
      </figcaption>
    </figure>

    <section class="yls-section yls-closing">
      <p>
        Whether you’re a young Lohana or you’ve been part of LCNL for years, we hope you’ll take an active part in our
        future: suggesting ideas, coming along to events, or even just coming up to say hello!
      </p>
      <p class="yls-signoff mb-0">We look forward to seeing you all soon!</p>
    </section>

  </div>
</div>

<?= $this->endSection() ?>
