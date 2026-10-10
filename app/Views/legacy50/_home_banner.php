<?php
/**
 * Home page spotlight for the Legacy 50 campaign. Shown while pledges are open.
 * Deliberately shows no progress figures.
 */
$legacy = config('Legacy50');

if (! $legacy->isOpen()) {
  return;
}
?>
<section class="container pt-4 pb-2">
  <?php
  // The artwork has its text baked in, which is unreadable below ~992px, so
  // small screens get the HTML banner below instead. loading="lazy" stops the
  // hidden image being downloaded on phones at all.
  $legacyImg = static fn(int $w, string $ext = 'webp'): string => base_url("assets/img/legacy50/home-banner-{$w}.{$ext}");
  ?>
  <a href="<?= base_url($legacy->slug) ?>" class="legacy-banner-image d-none d-lg-block">
    <picture>
      <source type="image/webp"
        srcset="<?= $legacyImg(1024) ?> 1024w, <?= $legacyImg(1600) ?> 1600w, <?= $legacyImg(2560) ?> 2560w"
        sizes="(min-width: 1400px) 1296px, 100vw">
      <img src="<?= $legacyImg(1600, 'jpg') ?>" width="1600" height="480" loading="lazy" decoding="async"
        alt="50th anniversary appeal: 100 Donors, One Lasting Legacy. Help us make the Dhamecha Lohana Centre debt-free. Just £<?= esc(number_format($legacy->targetAmount), 'attr') ?> remains on the loan. Pledge £<?= esc($legacy->donation, 'attr') ?> and LCNL will match it, and your name will be on a commemorative plaque at the Centre. Target: <?= esc($legacy->targetLabel(), 'attr') ?>. Make your pledge.">
    </picture>
  </a>

  <div class="legacy-banner d-lg-none">
    <div class="row g-0 align-items-stretch">

      <div class="col-md-4 legacy-banner-emblem">
        <div class="legacy-emblem" aria-hidden="true">
          <span class="legacy-emblem-num">50</span>
          <span class="legacy-emblem-sub">Years of LCNL</span>
          <i class="bi bi-building-fill legacy-emblem-icon"></i>
          <span class="legacy-emblem-place">Dhamecha Lohana Centre</span>
        </div>
      </div>

      <div class="col-md-8">
        <div class="legacy-banner-body">
          <span class="legacy-eyebrow">50th anniversary appeal</span>
          <h2 class="legacy-banner-title">100 Donors, One Lasting Legacy</h2>
          <p class="mb-3">
            Help us make the <strong>Dhamecha Lohana Centre debt-free</strong>. Just
            &pound;<?= esc(number_format($legacy->targetAmount)) ?> remains on the loan &mdash; pledge
            &pound;<?= esc($legacy->donation) ?> and LCNL will match it, and your name will be
            on a commemorative plaque at the Centre.
          </p>

          <div class="legacy-banner-sum" aria-label="Your £<?= esc($legacy->donation, 'attr') ?> plus LCNL's £<?= esc($legacy->match, 'attr') ?> makes £<?= esc(number_format($legacy->donation + $legacy->match), 'attr') ?>">
            <span>You &pound;<?= esc($legacy->donation) ?></span>
            <span class="op" aria-hidden="true">+</span>
            <span>LCNL &pound;<?= esc($legacy->match) ?></span>
            <span class="op" aria-hidden="true">=</span>
            <span class="total">&pound;<?= esc(number_format($legacy->donation + $legacy->match)) ?></span>
          </div>

          <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
            <a href="<?= base_url($legacy->slug) ?>" class="btn legacy-btn rounded-pill px-4">
              <i class="bi bi-heart-fill me-2"></i>Make your pledge
            </a>
            <span class="small legacy-banner-note">
              <i class="bi bi-flag-fill me-1"></i>Target: <?= esc($legacy->targetLabel()) ?>
            </span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
