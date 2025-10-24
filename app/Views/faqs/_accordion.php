<?php if (! empty($faqs)): ?>
<div class="accordion" id="faqAccordion">
    <?php foreach ($faqs as $index => $faq): ?>
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading<?= $faq['id'] ?>">
                <button class="accordion-button collapsed" type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapse<?= $faq['id'] ?>"
                        aria-expanded="false"
                        aria-controls="collapse<?= $faq['id'] ?>">
                    <?= esc($faq['question']) ?>
                </button>
            </h2>
            <div id="collapse<?= $faq['id'] ?>" class="accordion-collapse collapse"
                 aria-labelledby="heading<?= $faq['id'] ?>"
                 data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    <?= esc($faq['answer']) ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
    <p>No FAQs available.</p>
<?php endif; ?>
