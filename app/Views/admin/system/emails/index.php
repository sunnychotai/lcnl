<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-envelope-fill me-2"></i>Email Queue</h2>

    <?php if ($message = session()->getFlashdata('message')): ?>
        <div class="alert alert-success"><?= esc($message) ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table id="emailTable" class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>To</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Attempts</th>
                    <th>Scheduled</th>
                    <th>Sent</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($emails as $row): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td>
                        <?= esc($row['to_name'] ?: '') ?>
                        <br><small><?= esc($row['to_email']) ?></small>
                    </td>
                    <td><?= esc($row['subject']) ?></td>
                    <td>
                        <span class="badge 
                            <?php if ($row['status']=='pending') echo 'bg-warning';
                                  elseif ($row['status']=='sent') echo 'bg-success';
                                  elseif ($row['status']=='failed') echo 'bg-danger';
                                  else echo 'bg-secondary'; ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </td>
                    <td><?= $row['priority'] ?></td>
                    <td><?= $row['attempts'] ?? 0 ?></td>
                    <td><?= $row['scheduled_at'] ?></td>
                    <td><?= $row['sent_at'] ?></td>
                    <td>
                        <a href="<?= base_url('admin/system/emails/view/'.$row['id']) ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="<?= base_url('admin/system/emails/retry/'.$row['id']) ?>" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-arrow-repeat"></i>
                        </a>
                        <a href="<?= base_url('admin/system/emails/delete/'.$row['id']) ?>" class="btn btn-sm btn-outline-danger" 
                           onclick="return confirm('Delete this email?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function(){
    $('#emailTable').DataTable();
});
</script>

<?= $this->endSection() ?>
