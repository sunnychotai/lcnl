<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container">
    <div class="faq mt-5">
        <h3 class="mb-4">All FAQs</h3>
        <?= view('faqs/_accordion', ['faqs' => $faqs]) ?>
    </div>
</div>

<?= $this->endSection() ?>
