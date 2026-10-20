<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('partials/page_header', [
  'overlay'    => 'steel',
  'heading'    => $groupName . ' FAQs',
  'subheading' => 'Answers to the questions our community asks most',
]) ?>

<div class="container py-4 py-md-5">
    <div class="faq">
        <?= view('faqs/_accordion', ['faqs' => $faqs, 'headingLevel' => 2]) ?>
    </div>
</div>

<?= $this->endSection() ?>
