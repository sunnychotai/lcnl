<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<!-- Hero Banner -->
<div class="hero hero-rangoli-pink d-flex align-items-center justify-content-center">
  <div class="overlay"></div>
  <div class="container position-relative text-center">
    <h1 class="text-white fw-bold">Frequently Asked Questions</h1>
    <h5>Sign up and become a member of the Lohana Community today</h5>
  </div>
</div>

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
