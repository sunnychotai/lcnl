<?php
/**
 * Strip under the header of About Us and each committee page.
 *
 * @var string $current  route path of this page ('aboutus' or a Config\Committees key)
 */
?>
<nav class="cmt-more" aria-label="More from LCNL">
  <span class="cmt-more-label" aria-hidden="true">More from LCNL</span>
  <ul>
    <li><a href="<?= base_url('aboutus') ?>"<?= $current === 'aboutus' ? ' aria-current="page"' : '' ?>>About LCNL</a></li>
    <?php foreach (config('Committees')->pages as $path => $c): ?>
      <li>
        <a href="<?= base_url($path) ?>"<?= $path === $current ? ' aria-current="page"' : '' ?>><?= esc($c['name']) ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>
