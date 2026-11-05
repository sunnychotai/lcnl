<?php
/**
 * Home page "Watch live" panel for the Chopda Pujan YouTube stream.
 *
 * Shown whenever chopda.livestreamUrl in .env is a YouTube link; clear it to
 * take the panel down. Outside production it also shows without one, with a
 * placeholder player, so it can be previewed.
 */
$chopda  = config('ChopdaPujan');
$preview = ENVIRONMENT !== 'production';
$embed   = $chopda->livestreamEmbedUrl();
$watch   = $chopda->livestreamWatchUrl();

if (! $preview && $embed === null) {
  return;
}
?>
<section class="container pt-4" aria-labelledby="chopdaLiveTitle">
  <div class="chopda-live">
    <div class="row g-0">

      <div class="col-lg-8">
        <div class="chopda-live-player ratio ratio-16x9">
          <?php if ($embed !== null): ?>
            <iframe src="<?= esc($embed, 'attr') ?>" title="Chopda Pujan 2026 livestream"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
          <?php else: ?>
            <div class="chopda-live-placeholder">
              <i class="bi bi-youtube" aria-hidden="true"></i>
              <p class="mb-1 fw-semibold">The stream will play here</p>
              <p class="small mb-0">
                <?= $chopda->livestreamUrl === ''
                  ? 'Add <code>chopda.livestreamUrl</code> to .env'
                  : 'chopda.livestreamUrl isn&rsquo;t a YouTube link this page recognises' ?>
              </p>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="chopda-live-body">
          <span class="chopda-live-badge"><span class="chopda-live-dot" aria-hidden="true"></span>Watch live</span>
          <h2 id="chopdaLiveTitle" class="chopda-banner-title">Chopda Pujan 2026</h2>
          <p class="chopda-banner-tagline mb-3">Join the blessings for the new year from wherever you are</p>

          <?= $this->include('chopda/_livestream_status') ?>

          <ul class="chopda-banner-facts">
            <li><i class="bi bi-calendar-event" aria-hidden="true"></i><span><strong><?= esc($chopda->eventLabel()) ?></strong></span></li>
            <li><i class="bi bi-clock" aria-hidden="true"></i><span>Pooja <?= esc($chopda->poojaTimes) ?></span></li>
            <li><i class="bi bi-geo-alt" aria-hidden="true"></i><span><?= esc($chopda->venue['name']) ?></span></li>
            <li><i class="bi bi-person" aria-hidden="true"></i><span>Conducted by <?= esc($chopda->conductedBy) ?></span></li>
          </ul>

          <div class="d-flex flex-wrap gap-2 mt-4">
            <a href="<?= base_url($chopda->slug) ?>" class="btn legacy-btn rounded-pill px-4">
              <i class="bi bi-journal-text me-2" aria-hidden="true"></i>Chopda Pujan details
            </a>
            <?php if ($watch !== null): ?>
              <a href="<?= esc($watch, 'attr') ?>" class="btn btn-outline-light rounded-pill px-3" target="_blank" rel="noopener">
                <i class="bi bi-youtube me-1" aria-hidden="true"></i>Open in YouTube
              </a>
            <?php endif; ?>
          </div>

        </div>
      </div>

    </div>
  </div>
</section>
