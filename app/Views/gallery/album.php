<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= $this->include('gallery/_styles') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/photoswipe@5.4.4/dist/photoswipe.css">

<section class="hero-lcnl-watermark hero-overlay-ruby d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2"><?= esc($album['title']) ?></h1>
    <p class="lead fs-5 mb-0">
      <?php if ($album['album_date']): ?>
        <?= esc(date('j F Y', strtotime($album['album_date']))) ?> &middot;
      <?php endif; ?>
      <?= number_format($total) ?> <?= $total === 1 ? 'photo' : 'photos' ?>
    </p>
  </div>
</section>

<div class="container py-4">

  <?php if ($isPreview): ?>
    <div class="alert alert-warning d-flex align-items-center gap-2">
      <i class="bi bi-eye-slash-fill" aria-hidden="true"></i>
      <div>
        <strong>Preview.</strong> Only admins can see this album until it is published and has photos.
        <a href="<?= base_url('admin/gallery/album/' . $album['id']) ?>" class="alert-link">Manage album</a>
      </div>
    </div>
  <?php endif; ?>

  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <a href="<?= base_url('gallery') ?>" class="small text-decoration-none">
      <i class="bi bi-arrow-left" aria-hidden="true"></i> All albums
    </a>
    <div class="d-flex flex-wrap gap-2">
      <?php if ($event): ?>
        <a href="<?= base_url('events/' . $event['id']) ?>" class="btn btn-outline-brand btn-sm">
          <i class="bi bi-calendar-event me-1" aria-hidden="true"></i> Event details
        </a>
      <?php endif; ?>
    </div>
  </div>

  <?php if (! empty($album['credit']) || ! empty($album['description'])): ?>
    <div class="mb-4">
      <?php if (! empty($album['credit'])): ?>
        <p class="text-muted mb-1"><i class="bi bi-camera me-1" aria-hidden="true"></i><?= esc($album['credit']) ?></p>
      <?php endif; ?>
      <?php if (! empty($album['description'])): ?>
        <div class="text-secondary"><?= nl2br(esc($album['description'])) ?></div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php if ($photos === []): ?>
    <p class="text-muted py-5 text-center">No photos here yet.</p>
  <?php else: ?>
    <div class="gallery-grid" id="gallery" aria-label="Photos — select one to view it full screen">
      <?= view('gallery/_photos', ['album' => $album, 'photos' => $photos, 'offset' => $offset, 'nextPage' => $nextPage]) ?>
    </div>

    <div class="d-flex justify-content-center gap-2 mt-4">
      <?php if ($page > 1): ?>
        <a href="<?= base_url('gallery/' . $album['slug']) ?>" class="btn btn-outline-secondary px-4">
          Back to the start
        </a>
      <?php endif; ?>
      <?php if ($nextPage): ?>
        <a href="<?= base_url('gallery/' . $album['slug'] . '?page=' . $nextPage) ?>" class="btn btn-brand px-4" id="gallery-more">
          Load more photos
        </a>
      <?php endif; ?>
    </div>
    <p class="text-center small text-muted mt-2 mb-0" id="gallery-status" aria-live="polite"></p>
  <?php endif; ?>

  <p class="small text-muted text-center mt-5 mb-0">
    Would you like a photo removed? <a href="<?= base_url('contact') ?>">Contact us</a> and we&rsquo;ll take it down.
  </p>

</div>

<?php if ($photos !== []): ?>
<script type="module">
  import PhotoSwipeLightbox from 'https://cdn.jsdelivr.net/npm/photoswipe@5.4.4/dist/photoswipe-lightbox.esm.min.js';

  const grid = document.getElementById('gallery');
  const more = document.getElementById('gallery-more');
  const status = document.getElementById('gallery-status');

  // Full-screen viewer. Items are read from the grid on each open, so photos
  // added by "Load more" are included automatically.
  const lightbox = new PhotoSwipeLightbox({
    gallery: '#gallery',
    children: 'a.gallery-item',
    pswpModule: () => import('https://cdn.jsdelivr.net/npm/photoswipe@5.4.4/dist/photoswipe.esm.min.js'),
    bgOpacity: 0.95,
    showHideAnimationType: 'zoom',
  });

  lightbox.on('uiRegister', () => {
    lightbox.pswp.ui.registerElement({
      name: 'download-button',
      title: 'Download photo',
      ariaLabel: 'Download photo',
      order: 8,
      isButton: true,
      tagName: 'a',
      html: {
        isCustomSVG: true,
        inner: '<path d="M20.5 14.3 17.1 18V10h-2.2v7.9l-3.4-3.6L10 16l6 6.1 6-6.1ZM23 23H9v2h14Z" id="pswp__icn-download"/>',
        outlineID: 'pswp__icn-download',
      },
      onInit: (el, pswp) => {
        el.setAttribute('download', '');
        el.setAttribute('rel', 'noopener');
        pswp.on('change', () => {
          el.href = pswp.currSlide.data.element.dataset.download;
        });
      },
    });
  });

  lightbox.init();

  // "Load more": fetch the next page's photos and append them, in place of
  // following the link to a separate page.
  const takeNextPage = (container) => {
    const marker = container.querySelector('.gallery-next');
    const next = marker ? marker.dataset.nextPage : '';
    marker?.remove();
    return next;
  };

  let nextPage = takeNextPage(grid);

  more?.addEventListener('click', async (event) => {
    event.preventDefault();
    if (!nextPage || more.classList.contains('disabled')) return;

    more.classList.add('disabled');
    more.textContent = 'Loading…';

    try {
      const url = new URL(more.href);
      url.searchParams.set('page', nextPage);
      url.searchParams.set('partial', '1');

      const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (!response.ok) throw new Error(response.statusText);

      const holder = document.createElement('div');
      holder.innerHTML = await response.text();
      nextPage = takeNextPage(holder);

      const added = holder.querySelectorAll('a.gallery-item').length;
      grid.append(...holder.children);
      status.textContent = `${added} more photos loaded.`;

      if (nextPage) {
        more.href = more.href.replace(/page=\d+/, 'page=' + nextPage);
        more.textContent = 'Load more photos';
        more.classList.remove('disabled');
      } else {
        more.remove();
        status.textContent += ' That’s all of them.';
      }
    } catch (e) {
      more.textContent = 'Load more photos';
      more.classList.remove('disabled');
      status.textContent = 'Couldn’t load more photos. Please try again.';
    }
  });
</script>
<?php endif; ?>

<?= $this->endSection() ?>
