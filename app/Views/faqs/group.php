<div class="faq mt-5">
    <h3 class="mb-4"><?= esc($groupName) ?> FAQs</h3>
    <?= view('faqs/_accordion', ['faqs' => $faqs]) ?>
</div>
