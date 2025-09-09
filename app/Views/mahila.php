<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="hero hero-rangoli-purple d-flex align-items-center justify-content-center">
  <div class="overlay"></div>
  <div class="container position-relative">
    <h1 class="text-white fw-bold">Mahila Mandal</h1>
    <p class="text-white-75">Supporting and empowering the women of our community.</p>
  </div>
</div>

<div class="container py-5">
   <div class="row g-4">
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
