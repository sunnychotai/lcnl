<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-5">
    <div class="card shadow-lg">
        <div class="card-header bg-gradient py-3">
            <h3 class="mb-0">
                <i class="bi bi-calendar-event me-2"></i>
                Select an Event to Register
            </h3>
        </div>

        <div class="card-body">

            <?php if (empty($events)): ?>
                <div class="alert alert-info">
                    There are currently no events open for registration.
                </div>
            <?php else: ?>

                <div class="list-group">
                    <?php foreach ($events as $event): ?>
                        <a href="<?= site_url('events/register/' . $event['slug']) ?>"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">

                            <div>
                                <strong><?= esc($event['title']) ?></strong><br>
                                <small><?= date('d F Y', strtotime($event['event_date'])) ?></small>
                            </div>

                            <span class="badge bg-primary rounded-pill">
                                Register
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>

<?= $this->endSection() ?>

