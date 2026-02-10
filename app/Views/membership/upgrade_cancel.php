<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="container py-5">
    <div class="alert alert-warning shadow-sm">
        <h4 class="mb-1">Payment cancelled</h4>
        <p class="mb-0">No worries — you can upgrade any time from your dashboard.</p>
    </div>

    <a class="btn btn-outline-secondary" href="/account/dashboard">Back to dashboard</a>
</section>

<?= $this->endSection() ?>

