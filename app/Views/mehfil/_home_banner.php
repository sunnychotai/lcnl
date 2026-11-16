<?php
/**
 * Home page feature panel for the Christmas Mehfil, full width under the hero
 * (below the Diwali panel until that event has passed). Shown until the end
 * of the event day; after booking closes it still points at the event page.
 */
$mehfil = config('ChristmasMehfil');

if (! $mehfil->isUpcoming()) {
  return;
}

$mehfilFull = $mehfil->capacity > 0
  && (new \App\Models\MehfilBookingModel())->seatsTaken($mehfil->tableSize) >= $mehfil->capacity;
$mehfilOpen   = $mehfil->isOpen() && ! $mehfilFull;
$mehfilUrl    = base_url($mehfil->slug);
$mehfilPrices = $mehfil->currentPrices();
$mehfilEarly  = $mehfil->isEarlyBird();
$mehfilDays   = $mehfil->daysToGo();
$mehfilWhen   = match (true) {
  $mehfilDays <= 0  => 'Tonight',
  $mehfilDays === 1 => 'Tomorrow',
  default           => 'In ' . $mehfilDays . ' days',
};
?>
<section class="container pt-4" aria-labelledby="christmas-mehfil">
  <div class="xmas-feature">
    <div class="row g-0">

      <div class="col-md-4 col-lg-3">
        <a href="<?= $mehfilUrl ?>" class="xmas-feature-flier" tabindex="-1" aria-hidden="true">
          <img src="<?= base_url($mehfil->flier . '-480.webp') ?>" width="480" height="640" loading="lazy" decoding="async" alt="">
        </a>
      </div>

      <div class="col-md-8 col-lg-9">
        <div class="xmas-feature-body">
          <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <span class="legacy-eyebrow mb-0"><i class="bi bi-snow me-1" aria-hidden="true"></i>50th Golden Jubilee</span>
            <span class="xmas-feature-when"><?= esc($mehfilWhen) ?></span>
          </div>

          <h2 id="christmas-mehfil" class="xmas-feature-title">Christmas Mehfil</h2>
          <p class="xmas-feature-tagline">
            Live band, vegetarian dinner and drinks, Christmas bingo and a charity raffle. Come in your best Christmas jumper!
          </p>

          <ul class="xmas-feature-facts">
            <li><i class="bi bi-calendar-event" aria-hidden="true"></i><span><strong><?= esc((new DateTimeImmutable($mehfil->eventDate))->format('l j F')) ?></strong></span></li>
            <li><i class="bi bi-clock" aria-hidden="true"></i><span><?= esc($mehfil->times) ?></span></li>
            <li><i class="bi bi-geo-alt" aria-hidden="true"></i><span><?= esc($mehfil->venue['name']) ?>, <?= esc($mehfil->venue['address2']) ?></span></li>
            <li><i class="bi bi-ticket-perforated" aria-hidden="true"></i><span>
              <strong>&pound;<?= esc($mehfilPrices['individual']) ?></strong> per person &middot;
              <strong>&pound;<?= esc($mehfilPrices['table']) ?></strong> table of <?= esc($mehfil->tableSize) ?></span></li>
          </ul>

          <div class="d-flex flex-wrap align-items-center gap-3 mt-4">
            <a href="<?= $mehfilUrl ?>" class="btn legacy-btn btn-lg rounded-pill px-4">
              <?php if ($mehfilOpen): ?>
                <i class="bi bi-ticket-perforated me-2" aria-hidden="true"></i>Book tickets
              <?php else: ?>
                <i class="bi bi-info-circle me-2" aria-hidden="true"></i>Event details
              <?php endif; ?>
            </a>
            <span class="xmas-feature-note">
              <?php if ($mehfilFull): ?>
                Sold out
              <?php elseif (! $mehfilOpen): ?>
                Online booking has closed
              <?php elseif ($mehfilEarly): ?>
                <i class="bi bi-stars me-1" aria-hidden="true"></i>Early bird prices until <?= esc($mehfil->earlyBirdLabel()) ?>
              <?php else: ?>
                Booking closes <?= esc($mehfil->closingLabel()) ?>
              <?php endif; ?>
            </span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
