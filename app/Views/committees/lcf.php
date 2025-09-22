<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-cobalt d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">Lohana Charitable Foundation</h1>
    <p class="lead fs-5 mb-0"></p>
  </div>
</section>

<div class="container py-5">

<p>
  The Lohana Charitable Foundation...
</p>

      <hr class="my-5">
    
        <div class="row g-4">
    <h2>LCF Trustees 2025-2027</h2>
    <?php foreach ($members as $m): ?>
      <div class="col-md-3 col-sm-6">
<div class="card h-100 text-center">            <?php 
            // Base path for committee images
            $basePath = 'assets/img/committee/';
            $imagePath = $basePath . ($m['image'] ?? '');

            // Check if file exists, otherwise use placeholder
            if (empty($m['image']) || !is_file(FCPATH . $imagePath)) {
                $imagePath = $basePath . 'lcnl-placeholder.png';
            }
          ?>
          <img src="<?= base_url($imagePath) ?>" 
               class="card-img-top committee-photo" 
               alt="<?= esc($m['firstname'].' '.$m['surname']) ?>">

          <div class="card-body">
            <h5 class="card-title mb-1">
              <?= esc($m['firstname'].' '.$m['surname']) ?>
            </h5>
            <?php if (!empty($m['role'])): ?>
              <p class="text-muted mb-1"><?= esc($m['role']) ?></p>
            <?php endif; ?>
            <?php if (!empty($m['url'])): ?>
              <a href="<?= esc($m['url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">More</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>



<?= $this->endSection() ?>
