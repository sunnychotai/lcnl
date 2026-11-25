<?php
/**
 * Home page feature panel for Connexions – Singles Mix & Mingle, full width
 * at the top of the event panels. Shown until the end of the event day;
 * after booking closes it still points at the booking page for the details.
 */
$cnx = config('Connexions');

if (! $cnx->isUpcoming()) {
  return;
}

$cnxFull = (new \App\Services\ConnexionsBookingService())->isFull();
$cnxOpen = $cnx->isOpen() && $cnx->acceptsCard() && ! $cnxFull;
$cnxUrl  = base_url($cnx->slug);
$cnxDays = $cnx->daysToGo();
$cnxWhen = match (true) {
  $cnxDays <= 0  => 'Tonight',
  $cnxDays === 1 => 'Tomorrow',
  default        => 'In ' . $cnxDays . ' days',
};
?>
<section class="container pt-4" aria-labelledby="connexions-feature">
  <div class="cnx-feature">
    <div class="row g-0">

      <div class="col-md-4 col-lg-3">
        <a href="<?= $cnxUrl ?>" class="cnx-feature-flier" tabindex="-1" aria-hidden="true">
          <img src="<?= base_url($cnx->flier . '-480.webp') ?>"
            srcset="<?= base_url($cnx->flier . '-480.webp') ?> 480w, <?= base_url($cnx->flier . '-854.webp') ?> 854w"
            sizes="(min-width: 992px) 25vw, (min-width: 768px) 33vw, 100vw"
            width="480" height="720" loading="lazy" decoding="async" alt="">
        </a>
      </div>

      <div class="col-md-8 col-lg-9">
        <div class="cnx-feature-body">
          <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <span class="cnx-feature-eyebrow"><i class="bi bi-heart-fill me-1" aria-hidden="true"></i>LCNL Connexions &amp; YLS</span>
            <span class="cnx-feature-when"><?= esc($cnxWhen) ?></span>
          </div>

          <h2 id="connexions-feature" class="cnx-feature-title">Singles Mix &amp; Mingle</h2>
          <p class="cnx-feature-tagline">
            It&rsquo;s that unexpected meeting that changes your life&hellip; An evening of new connections and
            friendly conversation, exclusively for <strong><?= esc($cnx->eligibility) ?></strong>.
          </p>

          <ul class="cnx-feature-facts">
            <li><i class="bi bi-calendar-event" aria-hidden="true"></i><span><strong><?= esc((new DateTimeImmutable($cnx->eventDate))->format('l j F')) ?></strong></span></li>
            <li><i class="bi bi-clock" aria-hidden="true"></i><span><?= esc($cnx->times) ?></span></li>
            <li><i class="bi bi-geo-alt" aria-hidden="true"></i><span><?= esc($cnx->venue['name']) ?>, <?= esc($cnx->venue['postcode']) ?></span></li>
            <li><i class="bi bi-ticket-perforated" aria-hidden="true"></i><span><strong>&pound;<?= esc($cnx->ticketPrice) ?></strong> per person &middot; welcome drink and nibbles</span></li>
          </ul>

          <div class="d-flex flex-wrap align-items-center gap-3 mt-4">
            <a href="<?= $cnxUrl ?>" class="btn cnx-feature-btn btn-lg rounded-pill px-4">
              <?php if ($cnxOpen): ?>
                <i class="bi bi-ticket-perforated me-2" aria-hidden="true"></i>Book your place
              <?php else: ?>
                <i class="bi bi-info-circle me-2" aria-hidden="true"></i>Event details
              <?php endif; ?>
            </a>
            <span class="cnx-feature-note">
              <?php if ($cnxFull): ?>
                Fully booked
              <?php elseif (! $cnx->isOpen()): ?>
                Online booking has closed
              <?php else: ?>
                <i class="bi bi-stars me-1" aria-hidden="true"></i>Places are limited &middot; booking closes <?= esc($cnx->closingLabel()) ?>
              <?php endif; ?>
            </span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
