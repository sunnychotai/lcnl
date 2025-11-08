<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
    <div class="container text-center text-white py-5">
        <h1 class="display-5 fw-bold mb-3"><i class="bi bi-shield-lock-fill me-2"></i>Access Denied</h1>
        <p class="lead mb-4"><?= esc($message) ?></p>
        <a href="<?= base_url('/') ?>" class="btn btn-light rounded-pill px-4"><i class="bi bi-house-door-fill me-1"></i>Return Home</a>
    </div>
</section>

<?= $this->endSection() ?>