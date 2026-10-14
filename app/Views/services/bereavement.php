<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * Bereavement Support.
 *
 * Ordered for someone who has just lost a family member: who to phone comes
 * first, then what happens next, then everything else. Keep it that way.
 */
$contacts = [
  [
    'name'  => 'Vinubhai Kotecha',
    'role'  => 'Chairman, Bereavement Committee',
    'phone' => '07956 847764',
    'email' => 'vinodk52@aol.com',
  ],
  [
    'name'  => 'Arvindbhai Saujani',
    'role'  => 'Bereavement Committee',
    'phone' => '07956 217782',
    'email' => null,
  ],
];

$teamEmail = 'bereavement@lcnl.org';
$tel       = static fn(string $uk): string => '+44' . substr(preg_replace('/\D/', '', $uk), 1);

$steps = [
  [
    'title' => 'In the first few hours',
    'body'  => 'If the death was expected and happened at home, call the family GP, or NHS 111 out of hours. If it was '
      . 'unexpected, call 999. In hospital or a care home, the staff will guide you. A doctor will confirm the death '
      . 'and arrange the medical certificate needed to register it.',
  ],
  [
    'title' => 'Call the Bereavement Committee',
    'body'  => 'Ring us as soon as you feel able. We will talk you through what happens next, and help with arrangements '
      . 'from the Prarthna to the funeral, according to your family’s wishes.',
  ],
  [
    'title' => 'Choose a funeral director',
    'body'  => 'The funeral director will bring your loved one into their care and help plan the funeral and cremation. '
      . 'We can share our experience of local funeral directors who serve the community.',
  ],
  [
    'title' => 'Register the death',
    'body'  => 'In England and Wales a death should be registered within five days, at the register office for the area '
      . 'where it happened. The registrar can also offer Tell Us Once, which informs most government departments for '
      . 'you. If the death has been referred to the coroner, registration and the funeral may take a little longer.',
  ],
  [
    'title' => 'The Prarthna',
    'body'  => 'Our dedicated team can perform the Prarthna, and volunteers can sing Bhajans if you wish — free of charge. '
      . 'Prarthnas can be held at the Dhamecha Lohana Centre in Harrow.',
  ],
  [
    'title' => 'The funeral, and letting people know',
    'body'  => 'Your funeral director will arrange the service with the crematorium. We can help you share the Prarthna '
      . 'and funeral details with the community through our bereavement emails.',
  ],
];
?>

<section class="hero-lcnl-watermark hero-overlay-midnight d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-4">
    <span class="legacy-eyebrow">Lohana Community North London</span>
    <h1 class="fw-bold display-6 mb-2">Bereavement Support</h1>
    <p class="lead fs-5 mb-0">Guiding families with care and dignity at the hardest of times</p>
  </div>
</section>

<div class="container py-4 py-lg-5">

  <!-- Need help now? -->
  <section class="bereavement-help mb-5" aria-labelledby="help-now">
    <div class="row g-4 align-items-center">
      <div class="col-lg-5">
        <h2 id="help-now" class="h3 fw-bold mb-2">Has someone in your family passed away?</h2>
        <p class="mb-0 text-secondary">
          We are so sorry for your loss. Please call us &mdash; we will guide you through what happens next,
          step by step.
        </p>
      </div>
      <div class="col-lg-7">
        <div class="row g-2">
          <?php foreach ($contacts as $c): ?>
            <div class="col-sm-6">
              <a href="tel:<?= esc($tel($c['phone']), 'attr') ?>" class="bereavement-call">
                <i class="bi bi-telephone-fill" aria-hidden="true"></i>
                <span>
                  <span class="d-block small">Call <?= esc($c['name']) ?></span>
                  <span class="d-block fw-bold fs-5"><?= esc($c['phone']) ?></span>
                </span>
              </a>
            </div>
          <?php endforeach; ?>
          <div class="col-12">
            <a href="mailto:<?= esc($teamEmail, 'attr') ?>" class="bereavement-call is-quiet">
              <i class="bi bi-envelope-fill" aria-hidden="true"></i>
              <span>
                <span class="d-block small">Or email the Bereavement Team</span>
                <span class="d-block fw-semibold"><?= esc($teamEmail) ?></span>
              </span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- About the committee -->
  <section class="row g-4 g-lg-5 align-items-center mb-5" aria-labelledby="about">
    <div class="col-md">
      <h2 id="about" class="h3 fw-bold text-brand mb-3">We are here for you</h2>
      <p class="fs-6">
        The <strong>Bereavement Committee</strong> supports families with compassion and sensitivity, according to
        each family’s wishes. We can guide you through arrangements from the <em>Prarthna</em> to the funeral.
        A dedicated team is available to perform the Prarthna and, where desired, volunteers can support with
        <em>Bhajans</em>.
      </p>

      <ul class="bereavement-facts list-unstyled mb-0">
        <li><span class="fact-value">1999</span><span class="fact-label">Supporting families since</span></li>
        <li><span class="fact-value">4,000+</span><span class="fact-label">Funerals guided</span></li>
        <li><span class="fact-value">Free</span><span class="fact-label">Prarthna &amp; Bhajans, no charge</span></li>
      </ul>
    </div>
    <div class="col-md-auto text-center">
      <img src="<?= base_url('assets/img/shiva.png') ?>" alt="Silhouette of Lord Shiva seated in meditation, with the Om symbol"
        class="img-fluid bereavement-image" width="508" height="556" loading="lazy" decoding="async">
    </div>
  </section>

  <!-- What to do -->
  <section class="lcnl-card border-0 shadow-sm mb-5" aria-labelledby="steps">
    <div class="card-body p-4 p-lg-5">
      <h2 id="steps" class="h3 fw-bold text-brand mb-1">What to do when someone passes away</h2>
      <p class="text-secondary mb-4">
        There is no need to remember all of this. Call us, and we will help you with each step.
      </p>

      <ol class="bereavement-steps list-unstyled mb-4">
        <?php foreach ($steps as $i => $step): ?>
          <li>
            <span class="step-number" aria-hidden="true"><?= $i + 1 ?></span>
            <div>
              <h3 class="h6 fw-bold mb-1"><?= esc($step['title']) ?></h3>
              <p class="mb-0 text-secondary"><?= esc($step['body']) ?></p>
            </div>
          </li>
        <?php endforeach; ?>
      </ol>

      <p class="small text-muted mb-0">
        <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>
        Official guidance:
        <a href="https://www.gov.uk/after-a-death" target="_blank" rel="noopener noreferrer">What to do after someone dies (GOV.UK)</a>
      </p>
    </div>
  </section>

  <!-- Centre + email list -->
  <div class="row g-4 mb-5">
    <section class="col-md-6" aria-labelledby="dlc">
      <div class="lcnl-card border-0 shadow-sm h-100">
        <div class="card-body p-4">
          <h2 id="dlc" class="h5 fw-bold text-brand mb-2">
            <i class="bi bi-building me-2" aria-hidden="true"></i>Dhamecha Lohana Centre
          </h2>
          <p>
            The Centre is available for <em>Prarthnas</em>, offering family and friends a dedicated space for
            comfort, reflection and togetherness. For guidance and arrangements, please contact
            <strong>Vinubhai</strong>, who will be happy to help.
          </p>
          <p class="mb-0">
            <i class="bi bi-geo-alt-fill text-brand me-1" aria-hidden="true"></i>
            Brember Road, South Harrow, HA2 8AX
            &middot;
            <a href="https://www.google.com/maps/search/?api=1&amp;query=Dhamecha+Lohana+Centre+HA2+8AX" target="_blank" rel="noopener noreferrer">Map</a>
          </p>
        </div>
      </div>
    </section>

    <section class="col-md-6" aria-labelledby="notices">
      <div class="lcnl-card border-0 shadow-sm h-100">
        <div class="card-body p-4">
          <h2 id="notices" class="h5 fw-bold text-brand mb-2">
            <i class="bi bi-envelope-heart me-2" aria-hidden="true"></i>Receive bereavement emails
          </h2>
          <p>
            We email the community with news of bereavements and the details of Prarthnas and funerals. Many people
            who used to receive these still need to <strong>re-register under GDPR</strong>, and any number of
            family members can join.
          </p>
          <p class="mb-0">
            To be added, email
            <a href="mailto:<?= esc($teamEmail, 'attr') ?>" class="fw-semibold"><?= esc($teamEmail) ?></a>.
            Not receiving them? See the questions below.
          </p>
        </div>
      </div>
    </section>
  </div>

  <!-- Contacts -->
  <section class="mb-5" aria-labelledby="contacts">
    <h2 id="contacts" class="h3 fw-bold text-brand mb-3">Contacts</h2>
    <div class="row g-3">
      <?php foreach ($contacts as $c): ?>
        <div class="col-md-6">
          <div class="lcnl-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
              <h3 class="h6 fw-bold mb-0"><?= esc($c['name']) ?></h3>
              <p class="small text-muted mb-2"><?= esc($c['role']) ?></p>
              <ul class="list-unstyled mb-0">
                <li class="mb-1">
                  <i class="bi bi-telephone-fill text-brand me-2" aria-hidden="true"></i>
                  <a href="tel:<?= esc($tel($c['phone']), 'attr') ?>" class="text-reset fw-semibold"><?= esc($c['phone']) ?></a>
                </li>
                <?php if ($c['email']): ?>
                  <li>
                    <i class="bi bi-envelope-fill text-brand me-2" aria-hidden="true"></i>
                    <a href="mailto:<?= esc($c['email'], 'attr') ?>"><?= esc($c['email']) ?></a>
                  </li>
                <?php endif; ?>
              </ul>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- FAQs -->
  <?php if (! empty($faqs)): ?>
    <section class="mb-5" aria-labelledby="faqs">
      <h2 id="faqs" class="h3 fw-bold text-brand mb-3">Frequently asked questions</h2>
      <?= view('faqs/_accordion', ['faqs' => $faqs]) ?>
    </section>
  <?php endif; ?>

  <!-- Wider support -->
  <section class="bereavement-support" aria-labelledby="support">
    <h2 id="support" class="h5 fw-bold mb-2">Support for you and your family</h2>
    <p class="text-secondary mb-3">Grief can be hard to carry. If you would like someone to talk to, these free services can help.</p>
    <div class="row g-3">
      <div class="col-md-6">
        <p class="mb-0">
          <strong>Cruse Bereavement Support</strong><br>
          Helpline <a href="tel:+448088081677" class="fw-semibold">0808 808 1677</a> &middot;
          <a href="https://www.cruse.org.uk" target="_blank" rel="noopener noreferrer">cruse.org.uk</a>
        </p>
      </div>
      <div class="col-md-6">
        <p class="mb-0">
          <strong>Samaritans</strong>, any time, day or night<br>
          Call <a href="tel:116123" class="fw-semibold">116 123</a> free &middot;
          <a href="https://www.samaritans.org" target="_blank" rel="noopener noreferrer">samaritans.org</a>
        </p>
      </div>
    </div>
  </section>

</div>

<?= $this->endSection() ?>
