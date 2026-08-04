<?php
/**
 * @var array       $faqs
 * @var int|null    $headingLevel  Level for each question. The grouped listing
 *                                 nests questions under a group heading (3);
 *                                 the flat listing sits directly under the h1 (2).
 */
$hl = (int) ($headingLevel ?? 3);
$hl = $hl >= 2 && $hl <= 6 ? $hl : 3;
?>
<?php if (! empty($faqs)): ?>
<div class="accordion" id="faqAccordion">
    <?php foreach ($faqs as $index => $faq): ?>
        <div class="accordion-item">
            <h<?= $hl ?> class="accordion-header" id="heading<?= $faq['id'] ?>">
                <button class="accordion-button collapsed" type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapse<?= $faq['id'] ?>"
                        aria-expanded="false"
                        aria-controls="collapse<?= $faq['id'] ?>">
                    <?= esc($faq['question']) ?>
                </button>
            </h<?= $hl ?>>
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
