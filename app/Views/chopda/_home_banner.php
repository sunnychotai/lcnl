<?php
/**
 * Home page spotlight for Chopda Pujan, shown until the end of the event day.
 * After registration closes it still points people at the event page.
 */
$chopda = config('ChopdaPujan');

if (! $chopda->isUpcoming()) {
  return;
}

$chopdaOpen = $chopda->isOpen();
$chopdaImg  = static fn(int $w): string => base_url($chopda->flier . '-' . $w . '.webp');
?>
<section class="container pt-4 pb-2">
  <div class="chopda-banner">
    <div class="row g-0 align-items-stretch">

      <div class="col-md-4 col-lg-3 chopda-banner-flier">
        <a href="<?= base_url($chopda->slug) ?>" tabindex="-1" aria-hidden="true">
          <img src="<?= $chopdaImg(480) ?>" width="480" height="720" loading="lazy" decoding="async" alt="">
        </a>
      </div>

      <div class="col-md-8 col-lg-9">
        <div class="chopda-banner-body">
          <span class="legacy-eyebrow">LCNL cordially invites you</span>
          <h2 class="chopda-banner-title">Chopda Pujan 2026</h2>
          <p class="mb-3 chopda-banner-tagline">A time for new beginnings, blessings and prosperity</p>

          <ul class="chopda-banner-facts">
            <li><i class="bi bi-calendar-event"></i><span><strong><?= esc($chopda->eventLabel()) ?></strong></span></li>
            <li><i class="bi bi-clock"></i><span>Registration <?= esc($chopda->registrationTimes) ?> &middot; Pooja <?= esc($chopda->poojaTimes) ?></span></li>
            <li><i class="bi bi-geo-alt"></i><span><?= esc($chopda->venueLine()) ?></span></li>
            <li><i class="bi bi-currency-pound"></i><span>Nyochavar <strong>&pound;<?= esc($chopda->fee) ?> per pooja</strong> &middot; <?= esc($chopda->yajmansPerPooja) ?> Yajmans can sit for one pooja</span></li>
          </ul>

          <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
            <a href="<?= base_url($chopda->slug) ?>" class="btn legacy-btn rounded-pill px-4">
              <?php if ($chopdaOpen): ?>
                <i class="bi bi-journal-check me-2"></i>Register now
              <?php else: ?>
                <i class="bi bi-info-circle me-2"></i>Event details
              <?php endif; ?>
            </a>
            <span class="small chopda-banner-note">
              <?php if ($chopdaOpen): ?>
                <i class="bi bi-hourglass-split me-1"></i>Registration closes <?= esc($chopda->closingLabel()) ?>
              <?php else: ?>
                <i class="bi bi-telephone me-1"></i>Online registration has closed
              <?php endif; ?>
            </span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
