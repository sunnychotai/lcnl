<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('partials/page_header', [
  'heading'    => 'Lohana Youth Club',
  'subheading' => 'Supporting and empowering the youth of our community',
  'tone'       => 'lyc',
  'logo'       => 'assets/img/committee/lyc.jpg',
]) ?>

<?php
$whatsapp  = 'https://chat.whatsapp.com/FODohVokKuv1p2KgwrcSvt';
$instagram = 'https://www.instagram.com/lohanayouthclubnl/';

$committee = [
  [
    'name'  => 'Nina Valanju',
    'bio'   => 'Nina works as a Programme Director in Finance and is excited to help the youth of our community build strong, lasting connections.',
    'image' => 'nina-valanju.jpg',
  ],
  [
    'name'  => 'Vishali Sodha',
    'bio'   => 'Vishali is passionate about creating inspiring opportunities for young people. She is committed to strengthening community bonds and co-running initiatives for the LYC.',
    'image' => 'vishali-sodha.jpg',
  ],
  [
    'name'  => 'Neal Rajdev',
    'bio'   => 'Neal has served the Lohana community in South London and chaired his children\'s school PTA for five years, building a network of over 20 PTAs. He brings valuable experience in fostering collaboration and empowering youth.',
    'image' => 'neal-rajdev.jpg',
  ],
  [
    'name'  => 'Leena Tanna',
    'bio'   => 'Leena has been part of the LYC since its inception. She believes it offers young people a valuable chance to connect with their community and culture while forming lifelong friendships.',
    'image' => 'leena-tanna.jpg',
  ],
  [
    'name'  => 'Neil Morjaria',
    'bio'   => 'Neil is actively involved in initiatives such as the Lohana Table Tennis Club, the LCUK Sports Festival and the Raghuvanshi Charitable Trust. He is committed to strengthening family engagement and building connections among the youth.',
    'image' => 'neil-morjaria.jpg',
  ],
];
?>

<div class="container py-4 py-md-5">
  <div class="cmt-page">

    <?= view('committees/_more', ['current' => 'youth']) ?>

    <section class="cmt-section cmt-prose" aria-labelledby="lyc-about">
      <h2 id="lyc-about" class="cmt-section-title">About the LYC</h2>
      <p class="cmt-lead mb-0">
        The Lohana Youth Club (LYC) is dedicated to supporting, inspiring and empowering young people within our
        community. We aim to create opportunities for connection, personal growth and lasting friendships through
        cultural, social and sporting initiatives.
      </p>
    </section>

    <!-- Join -->
    <section class="cmt-join cmt-tone-lyc" aria-labelledby="lyc-join">
      <div class="cmt-join-body">
        <h2 id="lyc-join" class="cmt-section-title">Join the LYC</h2>
        <p>Families are welcome to join if:</p>
        <ul class="cmt-checklist">
          <li>you have at least one child aged <strong>13 to 18</strong></li>
          <li><strong>North London Mahajan</strong> is your closest Lohana community</li>
          <li>at least one parent is of <strong>Lohana origin</strong></li>
        </ul>
        <div class="cmt-join-actions">
          <a href="<?= $whatsapp ?>" target="_blank" rel="noopener" class="btn btn-success btn-lg rounded-pill px-4">
            <i class="bi bi-whatsapp me-2" aria-hidden="true"></i>Join our WhatsApp group
          </a>
          <a href="<?= $instagram ?>" target="_blank" rel="noopener" class="btn btn-outline-dark btn-lg rounded-pill px-4">
            <i class="bi bi-instagram me-2" aria-hidden="true"></i>@lohanayouthclubnl
          </a>
        </div>
      </div>

      <!-- A QR code only helps when the page is on a different screen from the phone -->
      <div class="cmt-join-qr d-none d-md-block">
        <img src="<?= base_url('assets/img/lyc_wa.png') ?>" alt="QR code to join the LYC WhatsApp group"
          width="180" height="180" loading="lazy" decoding="async">
        <p>Scan with your phone to join</p>
      </div>
    </section>

    <section class="cmt-section" aria-labelledby="lyc-events">
      <h2 id="lyc-events" class="cmt-section-title">Upcoming youth events</h2>
      <?= view('committees/_events', ['events' => $events, 'who' => 'youth']) ?>
    </section>

    <!-- Committee -->
    <section class="cmt-section" aria-labelledby="lyc-committee">
      <h2 id="lyc-committee" class="cmt-section-title">LYC Committee 2025–27</h2>
      <p class="cmt-section-intro">
        We are excited to introduce our new committee. Together, we are committed to creating meaningful events and
        opportunities for our youth.
      </p>

      <div class="cmt-photo-pair">
        <img src="<?= base_url('assets/img/youth-committee-25-27.jpg') ?>" alt="The LYC committee 2025–27"
          width="1600" height="1496" loading="lazy" decoding="async">
        <img src="<?= base_url('assets/img/youth-committee-25-27-2.jpg') ?>" alt="The LYC committee 2025–27, second photo"
          width="1600" height="1200" loading="lazy" decoding="async">
      </div>

      <ul class="cmt-bios">
        <?php foreach ($committee as $c): ?>
          <?php
          $photo = 'assets/img/committee/' . $c['image'];
          if (!is_file(FCPATH . $photo)) {
            $photo = 'assets/img/committee/lcnl-placeholder.png';
          }
          ?>
          <li class="cmt-bio">
            <img src="<?= base_url($photo) ?>" alt="" width="96" height="96" loading="lazy" decoding="async">
            <div>
              <h3 class="cmt-bio-name"><?= esc($c['name']) ?></h3>
              <p class="mb-0"><?= esc($c['bio']) ?></p>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
  </div>
</div>

<?= $this->endSection() ?>
