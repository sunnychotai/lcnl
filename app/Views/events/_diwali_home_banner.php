<?php
/**
 * Home page feature panel for the Diwali Fireworks Night, shown straight
 * under the hero until the end of the event day.
 */
$diwaliEnds = '2026-11-22 23:59:59';

if (time() > strtotime($diwaliEnds)) {
  return;
}

$diwaliUrl   = base_url('events/63');
$diwaliFlier = 'https://lcnl.org/uploads/events/1793387665_fddaf7fa366627df2ff7.jpeg';

// Countdown pill: whole calendar days to the event, in the site's timezone.
$diwaliDays = (int) (new DateTimeImmutable('today'))
  ->diff(new DateTimeImmutable('2026-11-22'))->format('%r%a');
$diwaliWhen = match (true) {
  $diwaliDays <= 0 => 'Tonight',
  $diwaliDays === 1 => 'Tomorrow',
  default => 'In ' . $diwaliDays . ' days',
};
?>
<section class="container pt-4" aria-labelledby="diwali-fireworks">
  <div class="diwali-feature">
    <div class="row g-0">

      <div class="col-md-4 col-lg-3">
        <a href="<?= $diwaliUrl ?>" class="diwali-feature-flier" tabindex="-1" aria-hidden="true">
          <img src="<?= $diwaliFlier ?>" width="1024" height="1536" decoding="async" alt="">
        </a>
      </div>

      <div class="col-md-8 col-lg-9">
        <div class="diwali-feature-body">
          <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <span class="legacy-eyebrow mb-0"><i class="bi bi-stars me-1" aria-hidden="true"></i>Happy Diwali from LCNL</span>
            <span class="diwali-feature-when"><?= esc($diwaliWhen) ?></span>
          </div>

          <h2 id="diwali-fireworks" class="diwali-feature-title">Diwali Fireworks Night</h2>
          <p class="diwali-feature-tagline">
            Bring the whole family and light up the sky with the community.
          </p>

          <ul class="diwali-feature-facts">
            <li><i class="bi bi-calendar-event" aria-hidden="true"></i><span><strong>Sunday 22 November</strong></span></li>
            <li><i class="bi bi-clock" aria-hidden="true"></i><span>5&ndash;7pm</span></li>
            <li><i class="bi bi-geo-alt" aria-hidden="true"></i><span>RCT Sports Centre, North Harrow</span></li>
            <li><i class="bi bi-cash-coin" aria-hidden="true"></i><span>Free on foot &middot; &pound;5 per car &middot; cash only</span></li>
          </ul>

          <div class="d-flex flex-wrap align-items-center gap-3 mt-4">
            <a href="<?= $diwaliUrl ?>" class="btn legacy-btn btn-lg rounded-pill px-4">
              <i class="bi bi-check2-circle me-2" aria-hidden="true"></i>Register to attend
            </a>
            <span class="diwali-feature-note"><i class="bi bi-gift-fill me-1" aria-hidden="true"></i>Win a Diwali hamper</span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
