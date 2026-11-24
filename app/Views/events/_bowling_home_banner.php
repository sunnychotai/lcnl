<?php
/**
 * Home page feature panel for the Lohana Youth Club Christmas VIP Bowling,
 * full width under the hero, shown until the end of the event day.
 * Booking is on Eventbrite; the details link goes to the event page.
 */
$bowlingEnds = '2026-12-11 23:59:59';

if (time() > strtotime($bowlingEnds)) {
  return;
}

$bowlingUrl     = base_url('events/65');
$bowlingBookUrl = 'https://christmasvipbowling.eventbrite.co.uk/';
$bowlingFlier   = 'assets/img/youth-bowling/flier-2026';

// Countdown pill: whole calendar days to the event, in the site's timezone.
$bowlingDays = (int) (new DateTimeImmutable('today'))
  ->diff(new DateTimeImmutable('2026-12-11'))->format('%r%a');
$bowlingWhen = match (true) {
  $bowlingDays <= 0  => 'Tonight',
  $bowlingDays === 1 => 'Tomorrow',
  default            => 'In ' . $bowlingDays . ' days',
};
?>
<section class="container pt-4" aria-labelledby="youth-bowling">
  <div class="bowling-feature">
    <div class="row g-0">

      <div class="col-md-4 col-lg-3">
        <a href="<?= $bowlingUrl ?>" class="bowling-feature-flier" tabindex="-1" aria-hidden="true">
          <img src="<?= base_url($bowlingFlier . '-480.webp') ?>"
            srcset="<?= base_url($bowlingFlier . '-480.webp') ?> 480w, <?= base_url($bowlingFlier . '-854.webp') ?> 854w"
            sizes="(min-width: 992px) 25vw, (min-width: 768px) 33vw, 100vw"
            width="480" height="719" loading="lazy" decoding="async" alt="">
        </a>
      </div>

      <div class="col-md-8 col-lg-9">
        <div class="bowling-feature-body">
          <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <span class="legacy-eyebrow mb-0"><i class="bi bi-controller me-1" aria-hidden="true"></i>Lohana Youth Club</span>
            <span class="bowling-feature-when"><?= esc($bowlingWhen) ?></span>
          </div>

          <h2 id="youth-bowling" class="bowling-feature-title">Christmas VIP Bowling</h2>
          <p class="bowling-feature-tagline">
            An evening of bowling, laughter, food and Christmas cheer for 13&ndash;18 year olds.
            Catch up with friends and make new ones before the holidays!
          </p>

          <ul class="bowling-feature-facts">
            <li><i class="bi bi-calendar-event" aria-hidden="true"></i><span><strong>Friday 11 December</strong></span></li>
            <li><i class="bi bi-clock" aria-hidden="true"></i><span>6:45&ndash;8:45pm &middot; please arrive early</span></li>
            <li><i class="bi bi-geo-alt" aria-hidden="true"></i><span>Hollywood Bowl, Woodside Leisure Park, Watford WD25 7JZ</span></li>
            <li><i class="bi bi-ticket-perforated" aria-hidden="true"></i><span><strong>&pound;22</strong> per person &middot; 1 hour of bowling, burger &amp; chips, ice cream and soft drinks</span></li>
          </ul>

          <div class="d-flex flex-wrap align-items-center gap-3 mt-4">
            <a href="<?= $bowlingBookUrl ?>" class="btn legacy-btn btn-lg rounded-pill px-4"
              target="_blank" rel="noopener noreferrer">
              <i class="bi bi-ticket-perforated me-2" aria-hidden="true"></i>Book on Eventbrite<span class="visually-hidden"> (opens in a new tab)</span>
            </a>
            <a href="<?= $bowlingUrl ?>" class="bowling-feature-link">
              Event details <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
            <span class="bowling-feature-note"><i class="bi bi-stars me-1" aria-hidden="true"></i>Spaces are limited</span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
