<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$errors = session('errors') ?? [];
$isEdit = ! empty($album['id']);
$action = $isEdit ? base_url('admin/gallery/update/' . $album['id']) : base_url('admin/gallery/store');
?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      <a href="<?= $isEdit ? base_url('admin/gallery/album/' . $album['id']) : base_url('admin/gallery') ?>" class="small text-decoration-none">
        <i class="bi bi-arrow-left"></i> <?= $isEdit ? 'Back to photos' : 'All albums' ?>
      </a>
      <h1 class="h3 fw-bold mt-2 mb-3"><?= $isEdit ? 'Edit album details' : 'New album' ?></h1>

      <?php if (! empty($errors)): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="<?= $action ?>" class="card border-0 shadow-sm">
        <?= csrf_field() ?>
        <div class="card-body">

          <div class="mb-3">
            <label for="event_id" class="form-label fw-semibold">Event <span class="text-muted fw-normal">(optional)</span></label>
            <select name="event_id" id="event_id" class="form-select">
              <option value="">Not linked to an event</option>
              <?php $selected = (string) old('event_id', $album['event_id'] ?? ''); ?>
              <?php foreach ($events as $event): ?>
                <option value="<?= (int) $event['id'] ?>"
                  data-title="<?= esc($event['title'], 'attr') ?>" data-date="<?= esc($event['event_date'], 'attr') ?>"
                  <?= $selected === (string) $event['id'] ? 'selected' : '' ?>>
                  <?= esc(($event['event_date'] ? date('j M Y', strtotime($event['event_date'])) . ' — ' : '') . $event['title']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div class="form-text">Linked albums get a &ldquo;View photos&rdquo; button on the event&rsquo;s page.</div>
          </div>

          <div class="mb-3">
            <label for="title" class="form-label fw-semibold">Album title</label>
            <input type="text" name="title" id="title" class="form-control" maxlength="150" required
              value="<?= esc(old('title', $album['title'] ?? ''), 'attr') ?>" placeholder="e.g. Navratri 2026 – Night 3">
          </div>

          <div class="row g-3 mb-3">
            <div class="col-sm-5">
              <label for="album_date" class="form-label fw-semibold">Date</label>
              <input type="date" name="album_date" id="album_date" class="form-control"
                value="<?= esc(old('album_date', $album['album_date'] ?? ''), 'attr') ?>">
            </div>
            <div class="col-sm-7">
              <label for="slug" class="form-label fw-semibold">Web address <span class="text-muted fw-normal">(optional)</span></label>
              <div class="input-group">
                <span class="input-group-text small">/gallery/</span>
                <input type="text" name="slug" id="slug" class="form-control" maxlength="150"
                  value="<?= esc(old('slug', $album['slug'] ?? ''), 'attr') ?>" placeholder="made from the title">
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label for="credit" class="form-label fw-semibold">Photo credit</label>
            <input type="text" name="credit" id="credit" class="form-control" maxlength="150"
              value="<?= esc(old('credit', $album['credit'] ?? ''), 'attr') ?>">
          </div>

          <div class="mb-0">
            <label for="description" class="form-label fw-semibold">Description <span class="text-muted fw-normal">(optional)</span></label>
            <textarea name="description" id="description" class="form-control" rows="3" maxlength="5000"><?= esc(old('description', $album['description'] ?? '')) ?></textarea>
          </div>

        </div>
        <div class="card-footer bg-white d-flex justify-content-end gap-2">
          <button type="submit" class="btn btn-brand px-4"><?= $isEdit ? 'Save details' : 'Create album' ?></button>
        </div>
      </form>

    </div>
  </div>
</div>

<script>
  // Choosing an event fills in the title and date if they are still blank.
  document.getElementById('event_id').addEventListener('change', (e) => {
    const option = e.target.selectedOptions[0];
    const title = document.getElementById('title');
    const date = document.getElementById('album_date');
    if (!option.value) return;
    if (!title.value.trim()) title.value = option.dataset.title;
    if (!date.value) date.value = option.dataset.date;
  });
</script>

<?= $this->endSection() ?>
