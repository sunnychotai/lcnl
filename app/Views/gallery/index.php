<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= $this->include('gallery/_styles') ?>

<section class="hero-lcnl-watermark hero-overlay-ruby d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">Photo Gallery</h1>
    <p class="lead fs-5 mb-0">Moments from our community</p>
  </div>
</section>

<div class="container py-4 py-lg-5">

  <?php if (empty($albums)): ?>
    <div class="text-center text-muted py-5">
      <i class="bi bi-images display-4 d-block mb-3 text-brand"></i>
      <p class="lead mb-1">Photos from our events will appear here soon.</p>
      <p class="mb-0">In the meantime, see what&rsquo;s coming up on our <a href="<?= base_url('events') ?>">events page</a>.</p>
    </div>
  <?php else: ?>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
      <?php foreach ($albums as $album): ?>
        <?php $cover = $album['cover']; ?>
        <div class="col">
          <article class="card gallery-album-card border-0 shadow-sm h-100">
            <div class="gallery-album-cover">
              <?php if ($cover): ?>
                <img src="<?= esc($cover['urls']['thumb']) ?>" alt="" loading="lazy" decoding="async"
                  width="<?= (int) $cover['width'] ?>" height="<?= (int) $cover['height'] ?>">
              <?php endif; ?>
            </div>
            <div class="card-body">
              <h2 class="h5 fw-semibold mb-1">
                <a href="<?= base_url('gallery/' . $album['slug']) ?>" class="stretched-link text-decoration-none text-dark">
                  <?= esc($album['title']) ?>
                </a>
              </h2>
              <p class="small text-muted mb-0">
                <?php if ($album['album_date']): ?>
                  <i class="bi bi-calendar3 me-1" aria-hidden="true"></i><?= esc(date('j F Y', strtotime($album['album_date']))) ?>
                  <span class="mx-1" aria-hidden="true">&middot;</span>
                <?php endif; ?>
                <i class="bi bi-images me-1" aria-hidden="true"></i><?= number_format((int) $album['photo_count']) ?> <?= (int) $album['photo_count'] === 1 ? 'photo' : 'photos' ?>
              </p>
            </div>
          </article>
        </div>
      <?php endforeach; ?>
    </div>

    <nav class="mt-4 d-flex justify-content-center" aria-label="Album pages">
      <?= $pager->links('default', 'bootstrap') ?>
    </nav>
  <?php endif; ?>

</div>

<?= $this->endSection() ?>
