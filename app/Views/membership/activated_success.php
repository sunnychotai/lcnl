<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
    <div class="container text-center text-white py-4">
        <h1 class="fw-bold display-6 mb-1"><i class="bi bi-check2-circle me-2"></i> Account Activated</h1>
        <p class="mb-0 opacity-75">Your password has been set and your account is now active.</p>
    </div>
</section>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-xl-6">
            <div class="card shadow-soft border-0 rounded-4">
                <div class="card-body p-4 p-md-5 text-center">
                    <p class="lead mb-4">Next step: please click <strong>“Dashboard”</strong> in the top-right to access
                        your member area and update your details.</p>
                    <a href="<?= base_url('account/dashboard') ?>" class="btn btn-brand rounded-pill px-4">
                        <i class="bi bi-speedometer2 me-2"></i> Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

