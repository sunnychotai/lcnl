<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('partials/page_header', [
  'overlay'    => 'steel',
  'heading'    => 'All FAQs',
  'subheading' => 'Every question our community asks, in one place',
]) ?>

<div class="container py-4 py-md-5">
    <div class="faq">
        <?= view('faqs/_accordion', ['faqs' => $faqs, 'headingLevel' => 2]) ?>
    </div>
</div>

<?= $this->endSection() ?>
