<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-ocean d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">Event Administration</h1>
    <p class="lead fs-5 mb-0">LCNL Site Admin</p>
  </div>
</section>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-9">

      <div class="card shadow-sm border-0 no-hover">
        <div class="card-header bg-brand text-white d-flex justify-content-between align-items-center">
          <h4 class="mb-0">
            <i class="bi bi-calendar-event-fill me-2"></i>
            <?= isset($event['id']) ? 'Edit Event' : 'Add Event' ?>
          </h4>
          <a href="<?= base_url('admin/content/events') ?>" class="btn btn-outline-light btn-sm">
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
              <label class="form-label fw-semibold">Title</label>
              <input type="text" name="title" class="form-control" value="<?= old('title', $event['title'] ?? '') ?>"
                required>
            </div>

            <!-- Slug -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Slug (URL)</label>
              <input type="text" name="slug" class="form-control" value="<?= old('slug', $event['slug'] ?? '') ?>"
                placeholder="e.g. maha-shivratri-2026">
              <small class="text-muted">
                Used for registration URL: /events/register/your-slug
              </small>
            </div>

            <!-- Date + Time -->
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Date</label>
                <input type="date" name="event_date" class="form-control"
                  value="<?= old('event_date', $event['event_date'] ?? '') ?>" required>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label fw-semibold">From</label>
                <input type="time" name="time_from" class="form-control"
                  value="<?= old('time_from', $event['time_from'] ?? '') ?>">
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label fw-semibold">To</label>
                <input type="time" name="time_to" class="form-control"
                  value="<?= old('time_to', $event['time_to'] ?? '') ?>">
              </div>
            </div>

            <!-- Location -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Location</label>
              <input type="text" name="location" class="form-control"
                value="<?= old('location', $event['location'] ?? '') ?>">
            </div>

            <!-- Committee -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Committee</label>
              <select name="committee" class="form-select" required>
                <option value="">-- Select Committee --</option>
                <?php foreach ($committeeOptions as $value => $label): ?>
                  <option value="<?= esc($value) ?>" <?= old('committee', $event['committee'] ?? '') === $value ? 'selected' : '' ?>>
                    <?= esc($label) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Description -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Description</label>
              <textarea name="description" class="form-control"
                rows="4"><?= old('description', $event['description'] ?? '') ?></textarea>
            </div>

            <!-- ===================================== -->
            <!-- 🔥 REGISTRATION SETTINGS (NEW) -->
            <!-- ===================================== -->

            <hr class="my-4">

            <h5 class="fw-bold mb-3">
              <i class="bi bi-person-check-fill me-2"></i>
              Registration Settings
            </h5>

            <!-- Requires Registration -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Requires Registration?</label>
              <select name="requires_registration" id="requiresRegistration" class="form-select">
                <option value="0" <?= old('requires_registration', $event['requires_registration'] ?? 0) == 0 ? 'selected' : '' ?>>No</option>
                <option value="1" <?= old('requires_registration', $event['requires_registration'] ?? 0) == 1 ? 'selected' : '' ?>>Yes</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Maximum Registrations</label>
              <input type="number" name="max_registrations" class="form-control"
                value="<?= old('max_registrations', $event['max_registrations'] ?? 0) ?>">
              <small class="text-muted">Leave 0 for unlimited</small>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Maximum Total Headcount</label>
              <input type="number" name="max_headcount" class="form-control"
                value="<?= old('max_headcount', $event['max_headcount'] ?? 0) ?>">
              <small class="text-muted">Total people allowed including guests</small>
            </div>

            <hr class="my-4">

            <!-- Image -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Event Image</label>
              <input type="file" name="image" class="form-control">
              <?php if (!empty($event['image'])): ?>
                <div class="mt-2">
                  <img src="<?= base_url($event['image']) ?>" class="img-thumbnail shadow-sm" style="max-width:200px;">
                </div>
              <?php endif; ?>
            </div>

            <!-- Valid -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Valid</label>
              <select name="is_valid" class="form-select">
                <option value="1" <?= old('is_valid', $event['is_valid'] ?? 1) == 1 ? 'selected' : '' ?>>Yes</option>
                <option value="0" <?= old('is_valid', $event['is_valid'] ?? 1) == 0 ? 'selected' : '' ?>>No</option>
              </select>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-end gap-2 mt-3">
              <a href="<?= base_url('admin/content/events') ?>" class="btn btn-secondary">
                Cancel
              </a>
              <button type="submit" class="btn btn-success">
                <?= isset($event['id']) ? 'Update Event' : 'Save Event' ?>
              </button>
            </div>

          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- JS: Hide capacity if registration disabled -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('requiresRegistration');
    const capacityWrapper = document.getElementById('capacityWrapper');

    function toggleCapacity() {
      capacityWrapper.style.display = select.value == "1" ? "block" : "none";
    }

    toggleCapacity();
    select.addEventListener('change', toggleCapacity);
  });
</script>

<?= $this->endSection() ?>

