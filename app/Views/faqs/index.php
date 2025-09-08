<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<!-- Hero Banner -->
<div class="position-relative w-100" style="height: 300px; overflow: hidden;">
    <img src="<?= base_url('assets/img/hero/faq.png') ?>" 
         alt="Bereavement Support Information" 
         class="img-fluid w-100 h-100" 
         style="object-fit: cover; object-position: center;">

    <!-- Semi-transparent dark layer -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.4);"></div>

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
