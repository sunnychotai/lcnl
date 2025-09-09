<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">
  <h2><?= isset($event['id']) ? 'Edit Event' : 'Add Event' ?></h2>

  <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
      <ul>
        <?php foreach (session()->getFlashdata('errors') as $error): ?>
          <li><?= esc($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" action="<?= $action ?>">
    <?= csrf_field() ?>

    <div class="mb-3">
      <label for="title" class="form-label">Title</label>
      <input type="text" 
             id="title" 
             name="title" 
             class="form-control" 
             value="<?= old('title', $event['title'] ?? '') ?>" 
             required>
    </div>

    <div class="mb-3">
      <label for="event_date" class="form-label">Date</label>
      <input type="date" 
             id="event_date" 
             name="event_date" 
             class="form-control" 
             value="<?= old('event_date', $event['event_date'] ?? '') ?>" 
             required>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="time_from" class="form-label">From Time</label>
        <input type="time" 
               id="time_from" 
               name="time_from" 
               class="form-control" 
               value="<?= old('time_from', $event['time_from'] ?? '') ?>">
      </div>
      <div class="col-md-6 mb-3">
        <label for="time_to" class="form-label">To Time</label>
        <input type="time" 
               id="time_to" 
               name="time_to" 
               class="form-control" 
               value="<?= old('time_to', $event['time_to'] ?? '') ?>">
      </div>
    </div>

    <div class="mb-3">
      <label for="location" class="form-label">Location</label>
      <input type="text" 
             id="location" 
             name="location" 
             class="form-control" 
             value="<?= old('location', $event['location'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label for="committee" class="form-label">Committee</label>
      <input type="text" 
             id="committee" 
             name="committee" 
             class="form-control" 
             value="<?= old('committee', $event['committee'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea id="description" 
                name="description" 
                class="form-control" 
                rows="4"><?= old('description', $event['description'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label for="image" class="form-label">Event Image</label>
      <input type="file" 
             id="image" 
             name="image" 
             class="form-control">
      <?php if (!empty($event['image'])): ?>
        <div class="mt-2">
          <img src="<?= base_url($event['image']) ?>" alt="Current Image" style="max-width:150px; height:auto;">
        </div>
      <?php endif; ?>
    </div>

    <div class="mb-3">
      <label for="is_valid" class="form-label">Valid</label>
      <select id="is_valid" name="is_valid" class="form-select">
        <option value="1" <?= (old('is_valid', $event['is_valid'] ?? 1) == 1) ? 'selected' : '' ?>>Yes</option>
        <option value="0" <?= (old('is_valid', $event['is_valid'] ?? 1) == 0) ? 'selected' : '' ?>>No</option>
      </select>
    </div>

    <button type="submit" class="btn btn-brand">Save</button>
    <a href="<?= base_url('admin/events') ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?= $this->endSection() ?>
