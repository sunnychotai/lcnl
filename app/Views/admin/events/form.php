<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-9">

      <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
          <h4 class="mb-0">
            <i class="bi bi-calendar-event-fill me-2"></i>
            <?= isset($event['id']) ? 'Edit Event' : 'Add Event' ?>
          </h4>
          <a href="<?= base_url('admin/events') ?>" class="btn btn-outline-light btn-sm">
            <i class="bi bi-arrow-left"></i> Back
          </a>
        </div>

        <div class="card-body">

          <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                  <li><?= esc($error) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form method="post" enctype="multipart/form-data" action="<?= $action ?>">
            <?= csrf_field() ?>

            <!-- Title -->
            <div class="mb-3">
              <label for="title" class="form-label fw-semibold"><i class="bi bi-type me-1"></i> Title</label>
              <input type="text" id="title" name="title" class="form-control"
                     value="<?= old('title', $event['title'] ?? '') ?>" required>
            </div>

            <!-- Date + Time -->
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="event_date" class="form-label fw-semibold"><i class="bi bi-calendar-date me-1"></i> Date</label>
                <input type="date" id="event_date" name="event_date" class="form-control"
                       value="<?= old('event_date', $event['event_date'] ?? '') ?>" required>
              </div>
              <div class="col-md-3 mb-3">
                <label for="time_from" class="form-label fw-semibold"><i class="bi bi-clock me-1"></i> From</label>
                <input type="time" id="time_from" name="time_from" class="form-control"
                       value="<?= old('time_from', $event['time_from'] ?? '') ?>">
              </div>
              <div class="col-md-3 mb-3">
                <label for="time_to" class="form-label fw-semibold"><i class="bi bi-clock-history me-1"></i> To</label>
                <input type="time" id="time_to" name="time_to" class="form-control"
                       value="<?= old('time_to', $event['time_to'] ?? '') ?>">
              </div>
            </div>

            <!-- Location -->
            <div class="mb-3">
              <label for="location" class="form-label fw-semibold"><i class="bi bi-geo-alt me-1"></i> Location</label>
              <input type="text" id="location" name="location" class="form-control"
                     value="<?= old('location', $event['location'] ?? '') ?>">
            </div>

            <!-- Committee -->
            <div class="mb-3">
              <label for="committee" class="form-label fw-semibold"><i class="bi bi-diagram-3 me-1"></i> Committee</label>
              <input type="text" id="committee" name="committee" class="form-control"
                     value="<?= old('committee', $event['committee'] ?? '') ?>">
            </div>

            <!-- Description -->
            <div class="mb-3">
              <label for="description" class="form-label fw-semibold"><i class="bi bi-card-text me-1"></i> Description</label>
              <textarea id="description" name="description" class="form-control" rows="4"><?= old('description', $event['description'] ?? '') ?></textarea>
            </div>

            <!-- Image -->
            <div class="mb-3">
              <label for="image" class="form-label fw-semibold"><i class="bi bi-image me-1"></i> Event Image</label>
              <input type="file" id="image" name="image" class="form-control">
              <?php if (!empty($event['image'])): ?>
                <div class="mt-2">
                  <img src="<?= base_url($event['image']) ?>" alt="Current Image"
                       class="img-thumbnail rounded shadow-sm" style="max-width: 200px; height: auto;">
                </div>
              <?php endif; ?>
            </div>

            <!-- Valid -->
            <div class="mb-3">
              <label for="is_valid" class="form-label fw-semibold"><i class="bi bi-check-circle me-1"></i> Valid</label>
              <select id="is_valid" name="is_valid" class="form-select">
                <option value="1" <?= (old('is_valid', $event['is_valid'] ?? 1) == 1) ? 'selected' : '' ?>>Yes</option>
                <option value="0" <?= (old('is_valid', $event['is_valid'] ?? 1) == 0) ? 'selected' : '' ?>>No</option>
              </select>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-end gap-2 mt-3">
              <a href="<?= base_url('admin/events') ?>" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Cancel
              </a>
              <button type="submit" class="btn btn-success">
                <i class="bi bi-save"></i>
                <?= isset($event['id']) ? 'Update Event' : 'Save Event' ?>
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<?= $this->endSection() ?>
