<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">Frequently Asked Questions</h1>
    <p class="lead fs-5 mb-0">Answers to the questions our community asks most</p>
  </div>
</section>

</div>
<div class="container">
  <?php if (! empty($groupedFaqs)): ?>
      <?php foreach ($groupedFaqs as $group => $faqs): ?>
        <h4 class="mt-5 mb-3"><?= esc($group) ?></h4>
        <?= view('faqs/_accordion', ['faqs' => $faqs]) ?>
      <?php endforeach; ?>
  <?php else: ?>
      <p>No FAQs available at this time.</p>
  <?php endif; ?>
</div>

<?= $this->endSection() ?>
