<div class="faq mt-5">
    <h1 class="h3 mb-4"><?= esc($groupName) ?> FAQs</h1>
    <?= view('faqs/_accordion', ['faqs' => $faqs, 'headingLevel' => 2]) ?>
</div>
