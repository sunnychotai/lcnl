<?php
/**
 * Home page spotlight for Navratri 2026. Shown until Sharad Poonam; on a
 * festival night it leads with tonight's colour and the live stream.
 */
$navratri = config('Navratri');

if (! $navratri->isPromoted()) {
  return;
}

$tonight  = $navratri->tonight();
$streamId = $tonight ? $navratri->streamFor($tonight['date']) : null;
$isLight  = $tonight && $tonight['ink'] !== '#fff';
?>
<section class="container pt-4 pb-2">
  <?php if ($tonight): ?>
  <div class="nvr-banner is-themed<?= $isLight ? ' is-light' : '' ?>"
    style="--nvr-day: <?= esc($tonight['swatch'], 'attr') ?>; --nvr-ink: <?= esc($tonight['ink'], 'attr') ?>">
  <?php else: ?>
  <div class="nvr-banner">
  <?php endif; ?>
    <div class="nvr-banner-stripe" aria-hidden="true">
      <?php foreach ($navratri->nights as $night): ?>
        <span style="background: <?= esc($night['swatch'], 'attr') ?>"></span>
      <?php endforeach; ?>
    </div>

    <div class="nvr-banner-body">
      <div class="row g-4 align-items-center">
        <div class="<?= $streamId ? 'col-lg-6' : 'col-lg-8' ?>">
          <span class="nvr-eyebrow">Dhamecha Lohana Centre &middot; 11&ndash;19 October &amp; 24 October</span>
          <h2 class="nvr-banner-title">LCNL Navratri Festival 2026</h2>

          <?php if ($tonight): ?>
            <p class="mb-3">
              <span class="badge bg-danger me-1">Tonight</span>
              <strong><?= esc($tonight['label']) ?></strong> &mdash; dress in
              <span class="nvr-chip" style="background: <?= esc($tonight['swatch'], 'attr') ?>; color: <?= esc($tonight['ink'], 'attr') ?>"><?= esc($tonight['colour']) ?></span>
            </p>
          <?php endif; ?>

          <p class="mb-3">
            Ten evenings of garba, dandia and live music with Strings, plus Disco Dandia for Sharad Poonam.
            Tickets from &pound;5 &mdash; season pass &pound;60.
          </p>

          <div class="d-flex flex-wrap gap-2">
            <a href="<?= base_url($navratri->slug) ?>" class="btn legacy-btn rounded-pill px-4">
              <i class="bi bi-stars me-2"></i>Tickets, colours &amp; info
            </a>
            <?php if ($tonight && ! $streamId): ?>
              <a href="<?= esc($navratri->youtubeChannel, 'attr') ?>" class="btn <?= $isLight ? 'btn-outline-dark' : 'btn-outline-light' ?> rounded-pill px-4" target="_blank" rel="noopener">
                <i class="bi bi-youtube me-2"></i>Watch on YouTube
              </a>
            <?php endif; ?>
          </div>
        </div>

        <?php if ($streamId): ?>
          <div class="col-lg-6">
            <div class="ratio ratio-16x9 rounded overflow-hidden shadow">
              <iframe src="https://www.youtube-nocookie.com/embed/<?= esc($streamId, 'url') ?>"
                title="Navratri <?= esc($tonight['label'], 'attr') ?> live stream" loading="lazy"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
