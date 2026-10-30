<?php
/**
 * Home page strip for the Diwali Fireworks Night, shown until the end of the
 * event day. Kept to one slim row so it sits above the Legacy 50 banner
 * without crowding the page.
 */
$diwaliEnds = '2026-11-22 23:59:59';

if (time() > strtotime($diwaliEnds)) {
  return;
}

$diwaliUrl   = base_url('events/63');
$diwaliFlier = 'https://lcnl.org/uploads/events/1793387665_fddaf7fa366627df2ff7.jpeg';
?>
<section class="container pt-4 pb-2" aria-labelledby="diwali-fireworks">
  <div class="diwali-strip">
    <a href="<?= $diwaliUrl ?>" class="diwali-strip-flier" tabindex="-1" aria-hidden="true">
      <img src="<?= $diwaliFlier ?>" width="1024" height="1536" loading="lazy" decoding="async" alt="">
    </a>

    <div class="diwali-strip-body">
      <span class="legacy-eyebrow"><i class="bi bi-stars me-1" aria-hidden="true"></i>Happy Diwali from LCNL</span>
      <h2 id="diwali-fireworks" class="diwali-strip-title">Diwali Fireworks Night</h2>
      <ul class="diwali-strip-facts">
        <li><i class="bi bi-calendar-event" aria-hidden="true"></i>22 November &middot; 5&ndash;7pm</li>
        <li><i class="bi bi-geo-alt" aria-hidden="true"></i>RCT Sports Centre, North Harrow</li>
        <li><i class="bi bi-cash-coin" aria-hidden="true"></i>Free on foot &middot; &pound;5 per car &middot; cash only</li>
      </ul>
    </div>

    <div class="diwali-strip-cta">
      <a href="<?= $diwaliUrl ?>" class="btn legacy-btn rounded-pill px-4">
        <i class="bi bi-check2-circle me-2" aria-hidden="true"></i>Register to attend
      </a>
      <span class="small diwali-strip-note"><i class="bi bi-gift-fill me-1" aria-hidden="true"></i>Win a Diwali hamper</span>
    </div>
  </div>
</section>
