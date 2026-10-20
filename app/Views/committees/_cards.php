<?php
/**
 * One card per committee page, from Config\Committees.
 *
 * @var string|null $exclude  route path of the page we're on
 */
$exclude ??= null;
?>
<ul class="cmt-cards">
  <?php foreach (config('Committees')->pages as $path => $c): ?>
    <?php if ($path === $exclude) continue; ?>
    <li>
      <a href="<?= base_url($path) ?>" class="cmt-card cmt-tone-<?= esc($c['tone'], 'attr') ?>">
        <span class="cmt-card-media is-<?= esc($c['fit'], 'attr') ?>">
          <img src="<?= base_url($c['image']) ?>" alt="" loading="lazy" decoding="async">
        </span>
        <span class="cmt-card-body">
          <span class="cmt-card-title"><?= esc($c['name']) ?></span>
          <span class="cmt-card-summary"><?= esc($c['summary']) ?></span>
          <span class="cmt-card-more" aria-hidden="true">Visit page &rarr;</span>
        </span>
      </a>
    </li>
  <?php endforeach; ?>
</ul>
