<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">

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
              <textarea id="field-description" name="description"
                class="form-control"><?= old('description', $event['description'] ?? '') ?></textarea>
            </div>

            <!-- ===================================== -->
            <!-- REGISTRATION SETTINGS -->
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

            <div id="capacityWrapper">

              <!-- Registration Open -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Registration Open?</label>
                <select name="registration_open" class="form-select">
                  <option value="1" <?= old('registration_open', $event['registration_open'] ?? 1) == 1 ? 'selected' : '' ?>>Yes — accepting registrations</option>
                  <option value="0" <?= old('registration_open', $event['registration_open'] ?? 1) == 0 ? 'selected' : '' ?>>No — closed / coming soon</option>
                </select>
                <small class="text-muted">Controls whether the Register Now button appears on the event page.</small>
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
            </div>

            <!-- ===================================== -->
            <!-- EVENT CONTENT DETAILS -->
            <!-- ===================================== -->

            <hr class="my-4">

            <h5 class="fw-bold mb-3">
              <i class="bi bi-file-earmark-text-fill me-2"></i>
              Event Content Details
            </h5>

            <!-- Ticket Information -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Ticket Information</label>
              <textarea id="field-ticketinfo" name="ticketinfo"
                class="form-control"><?= old('ticketinfo', $event['ticketinfo'] ?? '') ?></textarea>
              <small class="text-muted">Pricing, what's included, booking instructions etc.</small>
            </div>

            <!-- Purchase Tickets / Registration URL -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Ticket / Registration URL</label>
              <input type="url" name="purchase_ticket_url" class="form-control"
                value="<?= old('purchase_ticket_url', $event['purchase_ticket_url'] ?? '') ?>"
                placeholder="https://example.com/tickets">
              <small class="text-muted">
                Optional. If populated, a button will appear on the event page linking to this URL.
              </small>
            </div>

            <!-- Button Label -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Button Label</label>
              <select name="ticket_url_label" class="form-select">
                <option value="purchase" <?= old('ticket_url_label', $event['ticket_url_label'] ?? 'purchase') === 'purchase' ? 'selected' : '' ?>>Purchase Tickets</option>
                <option value="register" <?= old('ticket_url_label', $event['ticket_url_label'] ?? 'purchase') === 'register' ? 'selected' : '' ?>>Register</option>
              </select>
              <small class="text-muted">Controls the label shown on the button linked to the URL above.</small>
            </div>

            <!-- Event Terms -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Event Terms</label>
              <textarea id="field-eventterms" name="eventterms"
                class="form-control"><?= old('eventterms', $event['eventterms'] ?? '') ?></textarea>
              <small class="text-muted">Cancellation policy, refund rules, conditions etc.</small>
            </div>

            <!-- Contact Information -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Contact Information</label>
              <textarea id="field-contactinfo" name="contactinfo"
                class="form-control"><?= old('contactinfo', $event['contactinfo'] ?? '') ?></textarea>
              <small class="text-muted">Who to contact regarding this event.</small>
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

            <!-- Sold Out -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Sold Out?</label>
              <select name="is_sold_out" class="form-select">
                <option value="0" <?= old('is_sold_out', $event['is_sold_out'] ?? 0) == 0 ? 'selected' : '' ?>>No</option>
                <option value="1" <?= old('is_sold_out', $event['is_sold_out'] ?? 0) == 1 ? 'selected' : '' ?>>Yes — show SOLD OUT banner</option>
              </select>
              <small class="text-muted">Displays a prominent SOLD OUT overlay on all event images and cards.</small>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-end gap-2 mt-3">
              <a href="<?= base_url('admin/content/events') ?>" class="btn btn-secondary">Cancel</a>
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

<script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
<script>
  (function () {
    const toolbar = [
      { name: 'heading-2', action: EasyMDE.toggleHeading2, className: 'fa fa-header', title: 'Section Heading (##)' },
      'bold',
      '|',
      'unordered-list',
      'ordered-list',
      '|',
      'preview',
      'guide',
    ];

    const opts = { spellChecker: false, status: false, toolbar, minHeight: '120px' };

    ['field-description', 'field-ticketinfo', 'field-eventterms', 'field-contactinfo'].forEach(function (id) {
      const el = document.getElementById(id);
      if (el) new EasyMDE(Object.assign({}, opts, { element: el }));
    });

    // Hide capacity wrapper when registration not required
    const regSelect = document.getElementById('requiresRegistration');
    const capacityWrapper = document.getElementById('capacityWrapper');
    function toggleCapacity() {
      capacityWrapper.style.display = regSelect.value == '1' ? 'block' : 'none';
    }
    toggleCapacity();
    regSelect.addEventListener('change', toggleCapacity);
  })();
</script>

<?= $this->endSection() ?>
