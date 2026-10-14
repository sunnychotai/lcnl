<?php
/**
 * Home page: the newest public photo albums. Hidden until there is one.
 *
 * @var list<array> $latestAlbums from GalleryService::latestAlbums()
 */
if (empty($latestAlbums)) {
  return;
}
?>
<?= $this->include('gallery/_styles') ?>

<section class="container py-3" aria-labelledby="latest-photos">
  <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-2 mb-3">
    <h2 id="latest-photos" class="mb-0">
      <a href="<?= base_url('gallery') ?>" class="text-decoration-none text-dark">Latest Photos</a>
    </h2>
    <a href="<?= base_url('gallery') ?>" class="fw-semibold text-decoration-none">
      View all albums <i class="bi bi-arrow-right" aria-hidden="true"></i>
    </a>
  </div>

  <div class="row g-3">
    <?php foreach ($latestAlbums as $album): ?>
      <?php $count = (int) $album['photo_count']; ?>
      <div class="col-6 col-lg-3">
        <a href="<?= base_url('gallery/' . $album['slug']) ?>" class="gallery-home-card">
          <?php if ($album['cover']): ?>
            <img src="<?= esc($album['cover']['urls']['thumb']) ?>" alt="" loading="lazy" decoding="async"
              width="<?= (int) $album['cover']['width'] ?>" height="<?= (int) $album['cover']['height'] ?>">
          <?php endif; ?>
          <span class="gallery-home-count">
            <i class="bi bi-images" aria-hidden="true"></i> <?= number_format($count) ?><span class="visually-hidden"> <?= $count === 1 ? 'photo' : 'photos' ?></span>
          </span>
          <span class="gallery-home-caption">
            <span class="gallery-home-title"><?= esc($album['title']) ?></span>
            <?php if ($album['album_date']): ?>
              <span class="gallery-home-date"><?= esc(date('j M Y', strtotime($album['album_date']))) ?></span>
            <?php endif; ?>
          </span>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</section>
