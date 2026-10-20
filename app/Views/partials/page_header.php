<?php
/**
 * The page header used across the site. Only the home page and the campaign
 * pages (Navratri, Legacy 50) have their own.
 *
 * @var string      $heading
 * @var string|null $subheading
 * @var string|null $eyebrow     small line above the heading
 * @var string|null $logo        emblem beside the heading (path under public/)
 * @var string|null $extra       trusted HTML under the subheading: badges, a button
 * @var string|null $overlay     the page's own colour: a .hero-overlay-* name (steel, ruby, midnight…)
 * @var string|null $tone        committee pages instead: ruby|yls|lyc|lcf (default brand)
 */
$tone = $tone ?? 'brand';
?>
<section class="hero-lcnl-watermark page-header cmt-tone-<?= esc($tone, 'attr') ?><?= !empty($overlay) ? ' hero-overlay-' . esc($overlay, 'attr') : '' ?>">
  <div class="container position-relative py-4">
    <div class="page-header-inner">
      <?php if (!empty($logo)): ?>
        <img src="<?= base_url($logo) ?>" alt="" class="page-header-logo" width="88" height="88" decoding="async">
      <?php endif; ?>
      <div>
        <?php if (!empty($eyebrow)): ?>
          <p class="page-header-eyebrow"><?= esc($eyebrow) ?></p>
        <?php endif; ?>
        <h1><?= esc($heading) ?></h1>
        <?php if (!empty($subheading)): ?>
          <p class="page-header-sub"><?= esc($subheading) ?></p>
        <?php endif; ?>
        <?php if (!empty($extra)): ?>
          <div class="page-header-extra"><?= $extra ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
