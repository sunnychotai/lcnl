<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('partials/page_header', [
  'overlay'    => 'ruby',
  'heading'    => 'About LCNL',
  'subheading' => 'Lohana Community North London, since 1976',
  'tone'       => 'brand',
]) ?>

<div class="container py-4 py-md-5">
  <div class="cmt-page">

    <?= view('committees/_more', ['current' => 'aboutus']) ?>

    <!-- Who we are -->
    <div class="row g-4 g-lg-5 align-items-start mb-2">
      <section class="col-lg-8 cmt-prose" aria-labelledby="about-who">
        <h2 id="about-who" class="cmt-section-title">Who we are</h2>
        <p class="cmt-lead">
          The Lohana Community North London (LCNL) was founded in 1976 as an offshoot of the Lohana Union. Over the
          years, it has grown into a prominent voluntary organisation serving thousands of families across North
          London and Middlesex.
        </p>
        <p>
          LCNL connects with over 2,300 families through regular News &amp; Events, the Raghuvanshi Diwali Magazine,
          and annual festivals. We come together for Navratri, Diwali, Janmashtami, Hanuman Jayanti, and many more
          religious and cultural celebrations.
        </p>
        <p class="mb-0">
          We are proud of our contributions to local and international charities, the establishment of the RCT Sports
          Centre in Harrow, and most recently, the acquisition of a new community centre to serve future generations.
        </p>
      </section>

      <aside class="col-lg-4" aria-labelledby="about-facts">
        <div class="cmt-facts cmt-tone-brand">
          <h2 id="about-facts" class="visually-hidden">LCNL at a glance</h2>
          <dl>
            <dt>Founded</dt>
            <dd>1976</dd>
            <dt>Families</dt>
            <dd>Over 2,300</dd>
            <dt>Serving</dt>
            <dd>North London and Middlesex</dd>
            <dt>Our motto</dt>
            <dd>“We move forward together”</dd>
          </dl>
        </div>
      </aside>
    </div>

    <!-- What we do -->
    <section class="cmt-section" aria-labelledby="about-aims">
      <h2 id="about-aims" class="cmt-section-title">What we do</h2>
      <p class="cmt-section-intro">
        Our aim is to promote charitable causes, advance Hindu religion and culture, support education, and provide
        relief to those in need.
      </p>
      <ul class="about-aims">
        <li><i class="bi bi-heart" aria-hidden="true"></i><span>Charitable causes</span></li>
        <li><i class="bi bi-brightness-high" aria-hidden="true"></i><span>Hindu religion and culture</span></li>
        <li><i class="bi bi-book" aria-hidden="true"></i><span>Education</span></li>
        <li><i class="bi bi-hand-thumbs-up" aria-hidden="true"></i><span>Relief for those in need</span></li>
      </ul>
    </section>

    <!-- Committees -->
    <section class="cmt-section" aria-labelledby="about-committees">
      <h2 id="about-committees" class="cmt-section-title">Our committees</h2>
      <p class="cmt-section-intro">
        LCNL’s work is carried out by its Executive Committee and a family of committees, each with its own focus.
      </p>
      <?= view('committees/_cards') ?>
      <p class="about-also">
        Also part of LCNL: the Senior Men’s and Senior Ladies’ groups, the Raghuvanshi Charitable Trust, our Sports
        Club, and a wide range of subcommittees.
      </p>
    </section>

    <!-- Motto -->
    <figure class="about-motto">
      <blockquote class="mb-0">
        <p>We move forward together</p>
      </blockquote>
      <figcaption>
        Rooted in a long tradition of unity and service, LCNL continues to move forward together, honouring our
        history while building for the future.
      </figcaption>
    </figure>

    <!-- Constitution + get involved -->
    <div class="row g-4">
      <section class="col-lg-5" aria-labelledby="about-constitution">
        <div class="about-doc">
          <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
          <div>
            <h2 id="about-constitution" class="h5 fw-bold mb-1">LCNL Constitution</h2>
            <p class="mb-2">Our guiding principles and values, as set out in the constitution of February 2023.</p>
            <a href="<?= base_url('assets/documents/LCNL Constitution February 2023.pdf') ?>" target="_blank" rel="noopener">
              Download the constitution (PDF, 212 KB)
            </a>
          </div>
        </div>
      </section>

      <section class="col-lg-7" aria-labelledby="about-involved">
        <h2 id="about-involved" class="h5 fw-bold mb-3">Get involved</h2>
        <ul class="about-links">
          <li>
            <a href="<?= base_url('membership') ?>">
              <strong>Become a member</strong>
              <span>Join LCNL and have your say.</span>
            </a>
          </li>
          <li>
            <a href="<?= base_url('events') ?>">
              <strong>Events</strong>
              <span>Navratri, Diwali and year-round programmes.</span>
            </a>
          </li>
          <li>
            <a href="<?= base_url('bereavement') ?>">
              <strong>Bereavement support</strong>
              <span>Support, notices and community prayers.</span>
            </a>
          </li>
        </ul>
      </section>
    </div>

  </div>
</div>

<?= $this->endSection() ?>
