<?php
/**
 * Organiser contacts, shown on the form, the closed state and the confirmation.
 *
 * @var \Config\MelaStalls $config
 */
$config = $config ?? config('MelaStalls');
?>
<div class="p-3 rounded border bg-light">
  <h2 class="h6 fw-bold text-brand mb-2">Questions?</h2>
  <p class="mb-0 small">
    For more information you can call
    <?php foreach ($config->contacts as $i => $contact): ?>
      <?= $i > 0 ? ' or ' : '' ?>
      <?= esc($contact['name']) ?>
      on <a href="tel:<?= esc(preg_replace('/\s+/', '', $contact['phone']), 'attr') ?>"><?= esc($contact['phone']) ?></a><?php endforeach; ?>.
  </p>
</div>
