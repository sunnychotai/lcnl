<?php
/**
 * Organiser contacts, shown on the page, the closed state and the thank-you page.
 *
 * @var \Config\ChopdaPujan $config
 */
?>
<div class="p-3 rounded border bg-light">
  <h2 class="h6 fw-bold text-brand mb-2">Questions?</h2>
  <p class="mb-0 small">
    Please call
    <?php foreach ($config->contacts as $i => $contact): ?>
      <?= $i > 0 ? ' or ' : '' ?>
      <?= esc($contact['name']) ?>
      on <a href="tel:<?= esc(preg_replace('/\s+/', '', $contact['phone']), 'attr') ?>"><?= esc($contact['phone']) ?></a><?php endforeach; ?>,
    or email <a href="mailto:info@lcnl.org">info@lcnl.org</a>.
  </p>
</div>
