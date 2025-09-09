<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="mb-0">Manage Events</h2>
  <a href="<?= base_url('admin/events/create') ?>" class="btn btn-brand d-inline-flex align-items-center shadow-sm">
    <i class="bi bi-calendar-plus me-2"></i> Add Event
  </a>
</div>

  <?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Title</th>
        <th>Date</th>
        <th>Time</th>
        <th>Location</th>
        <th>Committee</th>
        <th>Image</th>
        <th>Valid</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($events as $event): ?>
      <tr>
        <td><?= esc($event['title']) ?></td>
        <td><?= esc($event['event_date']) ?></td>
        <td><?= esc($event['time_from']) ?> - <?= esc($event['time_to']) ?></td>
        <td><?= esc($event['location']) ?></td>
        <td><?= esc($event['committee']) ?></td>
        <td>
          <?php if (!empty($event['image'])): ?>
            <img src="<?= base_url($event['image']) ?>" alt="Event Image" style="width:60px; height:auto;">
          <?php endif; ?>
        </td>
        <td><?= $event['is_valid'] ? 'Yes' : 'No' ?></td>
        <td>
          <a href="<?= base_url('admin/events/edit/'.$event['id']) ?>" class="btn btn-sm btn-primary">Edit</a>
          <a href="<?= base_url('admin/events/clone/'.$event['id']) ?>" class="btn btn-sm btn-warning">Clone</a>
          <a href="<?= base_url('admin/events/delete/'.$event['id']) ?>" class="btn btn-sm btn-danger" 
             onclick="return confirm('Delete this event?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?= $this->endSection() ?>
