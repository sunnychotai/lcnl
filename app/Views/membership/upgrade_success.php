<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="container py-5">
    <div class="alert alert-success shadow-sm">
        <h4 class="mb-1">Thanks! Payment received.</h4>
        <p class="mb-0">
            Your Life Membership will be confirmed automatically (usually within seconds).
            If you don’t see it yet, refresh your dashboard in a moment.
        </p>
    </div>

    <a class="btn btn-primary" href="/account/dashboard">Back to dashboard</a>
</section>

<?= $this->endSection() ?>

