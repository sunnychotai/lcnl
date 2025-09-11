<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<!-- Hero Banner -->
<div class="position-relative w-100" style="height: 400px; overflow: hidden;">
    <img src="<?= base_url('assets/img/committee/lcnl-ec-large.jpg') ?>" 
         alt="LCNL Executive Committee 2025" 
         class="img-fluid w-100 h-100" 
         style="object-fit: cover; object-position: top;">

    <!-- Semi-transparent dark layer -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.4);"></div>

<!-- Overlay text -->
<div class="position-absolute top-50 start-0 translate-middle-y w-100 text-white">
    <div class="container text-center">
        <h1 class="fw-bold display-4">Executive Committee</h1>
        <h5>Meet the LCNL 2025-2027 Executive Committee</h5>
    </div>
</div>
</div>

<div class="container py-4">
  <div class="row g-4">
    <h2>2025-2027 Committee</h2>
    <?php foreach ($members as $m): ?>
      <div class="col-md-3 col-sm-6">
        <div class="card h-100 text-center">
          <?php 
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