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

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Manage Events</h2>
    <a href="<?= base_url('admin/content/events/create') ?>"
      class="btn btn-brand d-inline-flex align-items-center shadow-sm">
      <i class="bi bi-calendar-plus me-2"></i> Add Event
    </a>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success shadow-sm">
      <?= session()->getFlashdata('success') ?>
    </div>
  <?php endif; ?>

  <div class="table-responsive">
    <table class="table table-hover align-middle shadow-sm">
      <thead class="table-light">
        <tr>
          <th>Title</th>
          <th>Date</th>
          <th>Time</th>
          <th>Location</th>
          <th>Committee</th>
          <th>Registration</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>

        <?php if (empty($events)): ?>
          <tr>
            <td colspan="8" class="text-center text-muted py-4">
              No events found.
            </td>
          </tr>
        <?php endif; ?>

        <?php foreach ($events as $event):


          $requiresRegistration = $event['requires_registration'] ?? 0;

          $maxRegistrations = $event['max_registrations'] ?? null;
          $maxHeadcount = $event['max_headcount'] ?? null;

          $currentRegistrations = $event['current_registrations'] ?? 0;
          $currentHeadcount = $event['current_headcount'] ?? 0;

          $isFull = $event['is_full'] ?? false;


          ?>

          <tr>
            <td>
              <strong><?= esc($event['title']) ?></strong><br>
              <small class="text-muted">Slug: <?= esc($event['slug'] ?? '-') ?></small>
            </td>

            <td><?= date('d M Y', strtotime($event['event_date'])) ?></td>

            <td>
              <?= esc($event['time_from']) ?> - <?= esc($event['time_to']) ?>
            </td>

            <td><?= esc($event['location']) ?></td>

            <td><?= esc($event['committee']) ?></td>

            <!-- REGISTRATION COLUMN -->
            <td>
              <?php if ($requiresRegistration): ?>

                <span class="badge bg-primary mb-2">Enabled</span><br>

                <!-- Registration Count -->
                <?php if ($maxRegistrations): ?>
                  <small>
                    Registrations:
                    <?= $currentRegistrations ?> / <?= $maxRegistrations ?>
                  </small><br>
                <?php else: ?>
                  <small class="text-muted">
                    Registrations: Unlimited
                  </small><br>
                <?php endif; ?>

                <!-- Headcount -->
                <?php if ($maxHeadcount): ?>
                  <small>
                    Headcount:
                    <?= $currentHeadcount ?> / <?= $maxHeadcount ?>
                  </small>
                <?php else: ?>
                  <small class="text-muted">
                    Headcount: Unlimited
                  </small>
                <?php endif; ?>

                <?php if ($isFull): ?>
                  <div class="mt-1">
                    <span class="badge bg-danger">Full</span>
                  </div>
                <?php endif; ?>

              <?php else: ?>
                <span class="badge bg-secondary">Not Required</span>
              <?php endif; ?>
            </td>


            <!-- VALID COLUMN -->
            <td>
              <?php if ($event['is_valid']): ?>
                <span class="badge bg-success">Live</span>
              <?php else: ?>
                <span class="badge bg-danger">Hidden</span>
              <?php endif; ?>
            </td>

            <!-- ACTIONS -->
            <td class="text-end">
              <div class="btn-group btn-group-sm">

                <!-- View Event -->
                <a href="<?= base_url('events/' . $event['id']) ?>" class="btn btn-outline-secondary" target="_blank"
                  data-bs-toggle="tooltip" title="View Event">
                  <i class="bi bi-eye"></i>
                </a>

                <!-- Register Page -->
                <?php if ($requiresRegistration && !empty($event['slug'])): ?>
                  <a href="<?= site_url('events/register/' . $event['slug']) ?>" class="btn btn-outline-info"
                    target="_blank" data-bs-toggle="tooltip" title="Registration Page">
                    <i class="bi bi-person-plus"></i>
                  </a>
                <?php endif; ?>

                <!-- Edit -->
                <a href="<?= base_url('admin/content/events/edit/' . $event['id']) ?>" class="btn btn-outline-primary"
                  data-bs-toggle="tooltip" title="Edit Event">
                  <i class="bi bi-pencil"></i>
                </a>

                <!-- Clone -->
                <a href="<?= base_url('admin/content/events/clone/' . $event['id']) ?>" class="btn btn-outline-warning"
                  data-bs-toggle="tooltip" title="Clone Event">
                  <i class="bi bi-files"></i>
                </a>

                <!-- Delete -->
                <a href="<?= base_url('admin/content/events/delete/' . $event['id']) ?>" class="btn btn-outline-danger"
                  onclick="return confirm('Delete this event?')" data-bs-toggle="tooltip" title="Delete Event">
                  <i class="bi bi-trash"></i>
                </a>

              </div>
            </td>

          </tr>

        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->endSection() ?>

