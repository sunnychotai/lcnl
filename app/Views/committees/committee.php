<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<!-- Hero Banner -->
<div class="position-relative w-100" style="height: 400px; overflow: hidden;">
    <img src="<?= base_url('assets/img/site/lcnl-ec-2025.jpg') ?>" 
         alt="LCNL Executive Committee 2025" 
         class="img-fluid w-100 h-100" 
         style="object-fit: cover; object-position: top;">

    <!-- Semi-transparent dark layer -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.4);"></div>

<!-- Overlay text -->
<div class="position-absolute top-50 start-0 translate-middle-y w-100 text-white">
    <div class="container text-center">
        <h1 class="fw-bold display-4">Executive Committee</h1>
        <p class="lead">Meet the LCNL 2025-2027 Executive Committee</p>
    </div>
</div>
</div>

<div class="container py-5">
  
  <div class="row g-4">
    <h2>Lohana Community of North London Committee 2025-2027</h2>
    <?php foreach ($members as $m): ?>
      <div class="col-md-3 col-sm-6">
        <div class="card h-100 text-center">
          <?php 
    $imagePath = $m['image'];
    if (empty($imagePath) || !is_file(FCPATH . ltrim($imagePath, '/'))) {
        $imagePath = '/uploads/committee/lcnl-placeholder.png';
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