<?php
/**
 * Photo grid of people.
 *
 * @var array<int, array{name: string, role?: string|null, photo: string}> $people
 *        photo is a path under public/; a missing file falls back to the placeholder
 */
$placeholder = 'assets/img/committee/lcnl-placeholder.png';
?>
<ul class="cmt-people">
  <?php foreach ($people as $p): ?>
    <?php $photo = (!empty($p['photo']) && is_file(FCPATH . $p['photo'])) ? $p['photo'] : $placeholder; ?>
    <li class="cmt-person">
      <img src="<?= base_url($photo) ?>" alt="" class="cmt-person-photo<?= $photo === $placeholder ? ' is-placeholder' : '' ?>"
        width="240" height="300" loading="lazy" decoding="async">
      <span class="cmt-person-name"><?= esc($p['name']) ?></span>
      <?php if (!empty($p['role'])): ?>
        <span class="cmt-person-role"><?= esc($p['role']) ?></span>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
</ul>
