<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('partials/page_header', [
  'overlay'    => 'steel',
  'heading'    => 'Frequently Asked Questions',
  'subheading' => 'Answers to the questions our community asks most',
]) ?>

</div>
<div class="container">
  <?php if (! empty($groupedFaqs)): ?>
      <?php foreach ($groupedFaqs as $group => $faqs): ?>
        <h2 class="h4 mt-5 mb-3"><?= esc($group) ?></h2>
        <?= view('faqs/_accordion', ['faqs' => $faqs]) ?>
      <?php endforeach; ?>
  <?php else: ?>
      <p>No FAQs available at this time.</p>
  <?php endif; ?>
</div>

<?= $this->endSection() ?>
