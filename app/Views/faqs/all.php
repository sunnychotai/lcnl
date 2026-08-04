<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container">
    <div class="faq mt-5">
        <h1 class="h3 mb-4">All FAQs</h1>
        <?= view('faqs/_accordion', ['faqs' => $faqs, 'headingLevel' => 2]) ?>
    </div>
</div>

<?= $this->endSection() ?>
