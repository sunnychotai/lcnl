<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= $this->include('gallery/_styles') ?>

<div class="container py-4">

  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 fw-bold mb-0"><i class="bi bi-images me-2 text-brand"></i>Photo Gallery</h1>
    <div class="d-flex gap-2">
      <a href="<?= base_url('gallery') ?>" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">
        <i class="bi bi-box-arrow-up-right me-1"></i> View public gallery
      </a>
      <a href="<?= base_url('admin/gallery/create') ?>" class="btn btn-brand btn-sm">
        <i class="bi bi-plus-lg me-1"></i> New album
      </a>
    </div>
  </div>

  <?php if ($m = session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc($m) ?></div>
  <?php endif; ?>
  <?php if ($m = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($m) ?></div>
  <?php endif; ?>

  <?php if (! $storageReady): ?>
    <div class="alert alert-warning">
      <i class="bi bi-exclamation-triangle-fill me-1"></i>
      Photo storage is not set up yet, so uploads will not work. The <code>r2.*</code> settings in <code>.env</code> need filling in.
    </div>
  <?php endif; ?>

  <?php if (empty($albums)): ?>
    <div class="text-center text-muted py-5">
      <i class="bi bi-images display-4 d-block mb-3"></i>
      <p class="mb-3">No albums yet.</p>
      <a href="<?= base_url('admin/gallery/create') ?>" class="btn btn-brand">Create the first album</a>
    </div>
  <?php else: ?>
    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col" style="width: 96px;"><span class="visually-hidden">Cover</span></th>
              <th scope="col">Album</th>
              <th scope="col">Date</th>
              <th scope="col" class="text-end">Photos</th>
              <th scope="col">Status</th>
              <th scope="col"><span class="visually-hidden">Actions</span></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($albums as $album): ?>
              <tr>
                <td>
                  <div class="gallery-album-cover rounded" style="width: 80px;">
                    <?php if (isset($covers[(int) $album['id']])): ?>
                      <img src="<?= esc($covers[(int) $album['id']]) ?>" alt="" loading="lazy">
                    <?php endif; ?>
                  </div>
                </td>
                <td>
                  <a href="<?= base_url('admin/gallery/album/' . $album['id']) ?>" class="fw-semibold text-decoration-none"><?= esc($album['title']) ?></a>
                  <div class="small text-muted">/gallery/<?= esc($album['slug']) ?></div>
                </td>
                <td class="text-nowrap"><?= $album['album_date'] ? esc(date('j M Y', strtotime($album['album_date']))) : '<span class="text-muted">—</span>' ?></td>
                <td class="text-end"><?= number_format((int) $album['photo_count']) ?></td>
                <td>
                  <?php if (! $album['is_published']): ?>
                    <span class="badge text-bg-secondary">Draft</span>
                  <?php elseif ((int) $album['photo_count'] === 0): ?>
                    <span class="badge text-bg-warning" title="Published, but hidden until it has photos">Published · empty</span>
                  <?php else: ?>
                    <span class="badge text-bg-success">Published</span>
                  <?php endif; ?>
                </td>
                <td class="text-end text-nowrap">
                  <a href="<?= base_url('admin/gallery/album/' . $album['id']) ?>" class="btn btn-sm btn-outline-brand">
                    <i class="bi bi-images me-1"></i> Photos
                  </a>
                  <a href="<?= base_url('admin/gallery/edit/' . $album['id']) ?>" class="btn btn-sm btn-outline-secondary" aria-label="Edit details of <?= esc($album['title'], 'attr') ?>">
                    <i class="bi bi-pencil"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>

</div>

<?= $this->endSection() ?>
