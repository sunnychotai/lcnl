<?php
/**
 * Home page spotlight for Chopda Pujan, shown until the end of the event day.
 * After registration closes it still points people at the event page.
 *
 * Renders one column of the home spotlight row (see home.php).
 */
$chopda = config('ChopdaPujan');

if (! $chopda->isUpcoming()) {
  return;
}

$chopdaOpen = $chopda->isOpen();
$chopdaImg  = static fn(int $w): string => base_url($chopda->flier . '-' . $w . '.webp');
?>
<div class="col-lg">
  <div class="chopda-banner h-100">
    <div class="row g-0 h-100">

      <div class="col-sm-5 chopda-banner-flier">
        <a href="<?= base_url($chopda->slug) ?>" tabindex="-1" aria-hidden="true">
          <img src="<?= $chopdaImg(480) ?>" width="480" height="720" loading="lazy" decoding="async" alt="">
        </a>
      </div>

      <div class="col-sm-7">
        <div class="chopda-banner-body">
          <span class="legacy-eyebrow">LCNL cordially invites you</span>
          <h2 class="chopda-banner-title">Chopda Pujan 2026</h2>
          <p class="mb-3 chopda-banner-tagline">A time for new beginnings, blessings and prosperity</p>

          <ul class="chopda-banner-facts">
            <li><i class="bi bi-calendar-event"></i><span><strong><?= esc($chopda->eventLabel()) ?></strong></span></li>
            <li><i class="bi bi-clock"></i><span>Pooja <?= esc($chopda->poojaTimes) ?><br><small>Registration <?= esc($chopda->registrationTimes) ?></small></span></li>
            <li><i class="bi bi-geo-alt"></i><span><?= esc($chopda->venue['name']) ?></span></li>
            <li><i class="bi bi-currency-pound"></i><span><strong>&pound;<?= esc($chopda->fee) ?> per pooja</strong> &middot; <?= esc($chopda->yajmansPerPooja) ?> Yajmans per pooja</span></li>
          </ul>

          <a href="<?= base_url($chopda->slug) ?>" class="btn legacy-btn rounded-pill px-4 mt-3">
            <?php if ($chopdaOpen): ?>
              <i class="bi bi-journal-check me-2"></i>Register now
            <?php else: ?>
              <i class="bi bi-info-circle me-2"></i>Event details
            <?php endif; ?>
          </a>
          <p class="small chopda-banner-note mt-2 mb-0">
            <?php if ($chopdaOpen): ?>
              Registration closes <?= esc($chopda->closingLabel()) ?>
            <?php else: ?>
              Online registration has closed
            <?php endif; ?>
          </p>
        </div>
      </div>

    </div>
  </div>
</div>
