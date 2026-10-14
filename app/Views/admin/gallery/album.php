<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= $this->include('gallery/_styles') ?>

<?php
$base      = 'admin/gallery/album/' . $album['id'];
$published = ! empty($album['is_published']);
$js        = 'assets/js/gallery-admin.js';
?>

<div class="container py-4">

  <a href="<?= base_url('admin/gallery') ?>" class="small text-decoration-none">
    <i class="bi bi-arrow-left"></i> All albums
  </a>

  <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mt-2 mb-3">
    <div>
      <h1 class="h3 fw-bold mb-1">
        <?= esc($album['title']) ?>
        <?php if ($published): ?>
          <span class="badge text-bg-success align-middle fs-6">Published</span>
        <?php else: ?>
          <span class="badge text-bg-secondary align-middle fs-6">Draft</span>
        <?php endif; ?>
      </h1>
      <div class="small text-muted">
        <?= $album['album_date'] ? esc(date('j F Y', strtotime($album['album_date']))) . ' &middot; ' : '' ?>
        <?= number_format(count($photos)) ?> photos &middot; /gallery/<?= esc($album['slug']) ?>
      </div>
    </div>

    <div class="d-flex flex-wrap gap-2">
      <a href="<?= base_url('gallery/' . $album['slug']) ?>" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">
        <i class="bi bi-eye me-1"></i> <?= $published && $photos ? 'View on site' : 'Preview' ?>
      </a>
      <a href="<?= base_url('admin/gallery/edit/' . $album['id']) ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-pencil me-1"></i> Edit details
      </a>
      <form method="post" action="<?= base_url('admin/gallery/publish/' . $album['id']) ?>" class="d-inline">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-sm <?= $published ? 'btn-outline-warning' : 'btn-success' ?>">
          <i class="bi <?= $published ? 'bi-eye-slash' : 'bi-globe' ?> me-1"></i> <?= $published ? 'Unpublish' : 'Publish' ?>
        </button>
      </form>
      <?= view('admin/_delete_button', [
        'action'  => base_url('admin/gallery/delete/' . $album['id']),
        'confirm' => 'Delete this album and all ' . count($photos) . ' of its photos? This cannot be undone.',
        'label'   => '<i class="bi bi-trash me-1"></i> Delete album',
        'class'   => 'btn btn-outline-danger btn-sm',
      ]) ?>
    </div>
  </div>

  <?php if ($m = session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc($m) ?></div>
  <?php endif; ?>
  <?php if ($m = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($m) ?></div>
  <?php endif; ?>

  <?php if ($unmarked > 0): ?>
    <div class="alert alert-info d-flex flex-wrap align-items-center justify-content-between gap-2" id="gallery-watermark">
      <div>
        <i class="bi bi-patch-check me-1" aria-hidden="true"></i>
        <span data-watermark-text><?= number_format($unmarked) ?> <?= $unmarked === 1 ? 'photo was' : 'photos were' ?> uploaded before the LCNL watermark was added.</span>
      </div>
      <button type="button" class="btn btn-sm btn-brand" data-watermark-start>
        <i class="bi bi-magic me-1"></i> Add the watermark to <?= $unmarked === 1 ? 'it' : 'them' ?>
      </button>
    </div>
  <?php endif; ?>

  <!-- Uploader -->
  <div class="card border-0 shadow-sm mb-4" id="gallery-uploader"
    data-check-url="<?= base_url($base . '/check') ?>"
    data-upload-url="<?= base_url($base . '/upload') ?>"
    data-reorder-url="<?= base_url($base . '/reorder') ?>"
    data-watermark-url="<?= base_url($base . '/watermark') ?>"
    data-token-url="<?= base_url('admin/gallery/token') ?>"
    data-csrf-name="<?= csrf_token() ?>"
    data-csrf-hash="<?= csrf_hash() ?>"
    data-next-sort="<?= (int) $nextSort ?>"
    data-web-size="<?= (int) $webSize ?>">
    <div class="card-body">
      <h2 class="h5 fw-semibold mb-3"><i class="bi bi-cloud-arrow-up me-2 text-brand"></i>Add photos</h2>

      <?php if (! $storageReady): ?>
        <div class="alert alert-warning mb-0">
          Photo storage is not set up yet, so photos cannot be uploaded. The <code>r2.*</code> settings in <code>.env</code> need filling in.
        </div>
      <?php else: ?>
        <div class="gallery-dropzone text-center p-4 p-lg-5" id="gallery-dropzone">
          <i class="bi bi-images display-5 text-brand d-block mb-2" aria-hidden="true"></i>
          <p class="fw-semibold mb-1">Drag photos or a whole folder here</p>
          <p class="small text-muted mb-3">
            Any number at once. Photos already in this album are skipped, so if an upload is interrupted, just drop the same folder again.
          </p>
          <div class="d-flex flex-wrap justify-content-center gap-2">
            <label class="btn btn-brand mb-0">
              <i class="bi bi-file-earmark-image me-1"></i> Choose photos
              <input type="file" id="gallery-files" accept="image/jpeg,image/png,image/webp,image/heic,.heic,.heif" multiple hidden>
            </label>
            <label class="btn btn-outline-brand mb-0">
              <i class="bi bi-folder2-open me-1"></i> Choose a folder
              <input type="file" id="gallery-folder" webkitdirectory multiple hidden>
            </label>
          </div>
        </div>

        <div id="gallery-progress" class="mt-3" hidden>
          <div class="progress" role="progressbar" aria-label="Upload progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="height: 1.25rem;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 0%"></div>
          </div>
          <p class="small mt-2 mb-2" id="gallery-summary" aria-live="polite"></p>
          <div id="gallery-done" class="alert alert-light border d-flex flex-wrap align-items-center justify-content-between gap-2 py-2" hidden>
            <span data-done-text></span>
            <a href="<?= current_url() ?>" class="btn btn-sm btn-brand">Refresh to manage the new photos</a>
          </div>
          <ul class="list-group gallery-upload-log" id="gallery-log"></ul>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Photos -->
  <div class="card border-0 shadow-sm" data-photos-card <?= $photos ? '' : 'hidden' ?>>
    <div class="card-body">
      <form method="post" action="<?= base_url($base . '/photos/delete') ?>" id="gallery-photos-form">
        <?= csrf_field() ?>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
          <div>
            <h2 class="h5 fw-semibold mb-0">Photos</h2>
            <div class="small text-muted">Drag to change the order. Tick photos to delete several at once.</div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="small text-muted" id="gallery-order-status" aria-live="polite"></span>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="gallery-select-none">Clear selection</button>
            <button type="submit" class="btn btn-sm btn-danger" id="gallery-delete-selected" disabled>
              <i class="bi bi-trash me-1"></i> Delete selected (<span id="gallery-selected-count">0</span>)
            </button>
          </div>
        </div>

        <div class="gallery-admin-grid" id="gallery-photos">
          <?php foreach ($photos as $photo): ?>
            <?php $isCover = (int) $album['cover_photo_id'] === (int) $photo['id']; ?>
            <div class="gallery-admin-photo" data-id="<?= (int) $photo['id'] ?>">
              <img src="<?= esc($photo['thumb']) ?>" alt="<?= esc($photo['original_name'] ?? '') ?>" loading="lazy" decoding="async">
              <input type="checkbox" class="form-check-input" name="ids[]" value="<?= (int) $photo['id'] ?>"
                aria-label="Select <?= esc($photo['original_name'] ?? 'photo', 'attr') ?>">
              <?php if ($isCover): ?>
                <span class="badge text-bg-warning gallery-cover-badge"><i class="bi bi-star-fill me-1"></i>Cover</span>
              <?php else: ?>
                <div class="gallery-photo-actions">
                  <button type="submit" class="btn btn-light btn-sm py-0 px-1 shadow-sm"
                    formaction="<?= base_url($base . '/cover/' . $photo['id']) ?>"
                    title="Use as album cover" aria-label="Use <?= esc($photo['original_name'] ?? 'this photo', 'attr') ?> as album cover">
                    <i class="bi bi-star"></i>
                  </button>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </form>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script src="<?= esc(base_url($js) . (is_file(FCPATH . $js) ? '?v=' . filemtime(FCPATH . $js) : '')) ?>"></script>

<?= $this->endSection() ?>
