<?php
/**
 * One page of album photos. Rendered into the album page and, for "Load
 * more", returned on its own and appended to the grid.
 *
 * @var array    $album
 * @var array    $photos   rows with ['urls' => ['web' => ..., 'thumb' => ...]]
 * @var int      $offset   photos on earlier pages, for numbering the alt text
 * @var int|null $nextPage
 */
foreach ($photos as $i => $photo):
  $ratio = number_format($photo['width'] / max(1, $photo['height']), 4, '.', '');
?>
<a class="gallery-item" href="<?= esc($photo['urls']['web']) ?>"
  data-pswp-width="<?= (int) $photo['width'] ?>" data-pswp-height="<?= (int) $photo['height'] ?>"
  data-download="<?= esc(base_url('gallery/photo/' . $photo['id'] . '/download')) ?>"
  style="--r: <?= $ratio ?>">
  <img src="<?= esc($photo['urls']['thumb']) ?>" alt="Photo <?= $offset + $i + 1 ?> from <?= esc($album['title']) ?>"
    loading="lazy" decoding="async">
</a>
<?php endforeach; ?>
<span class="gallery-next" data-next-page="<?= $nextPage ?? '' ?>" hidden></span>
