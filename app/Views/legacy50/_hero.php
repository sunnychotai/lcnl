<?php /** @var \Config\Legacy50 $config */ ?>
<section class="hero-lcnl-watermark legacy-hero d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-4">
    <span class="legacy-eyebrow">50 Years of LCNL &middot; 1976&ndash;2026</span>
    <h1 class="fw-bold display-6 mb-2"><?= esc($heading ?? '100 Donors, One Lasting Legacy') ?></h1>
    <p class="lead fs-5 mb-0"><?= esc($subheading ?? 'Help us make the Dhamecha Lohana Centre debt-free') ?></p>

    <div class="mela-hero-pills">
      <span class="pill is-accent"><i class="bi bi-heart-fill"></i> &pound;<?= esc($config->donation) ?> pledge</span>
      <span class="pill"><i class="bi bi-plus-circle"></i> Matched &pound;<?= esc($config->match) ?> by LCNL</span>
      <span class="pill"><i class="bi bi-flag"></i> Target <?= esc((new DateTimeImmutable($config->targetDate))->format('j M Y')) ?></span>
    </div>
  </div>
</section>
