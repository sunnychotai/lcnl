<?php
/**
 * Where the member is in: details → confirm email → Life Membership.
 *
 * @var int $step  1–3 is the current step; 4 means all done
 */
$labels = ['Your details', 'Confirm email', 'Life Membership'];
?>
<ol class="join-steps" aria-label="Joining LCNL">
  <?php foreach ($labels as $i => $label): ?>
    <?php
    $n = $i + 1;
    $state = $n < $step ? 'is-done' : ($n === $step ? 'is-current' : '');
    ?>
    <li class="<?= $state ?>"<?= $n === $step ? ' aria-current="step"' : '' ?>>
      <span class="join-step-marker" aria-hidden="true"></span>
      <span>
        <?= esc($label) ?>
        <?php if ($n < $step): ?><span class="visually-hidden">(done)</span><?php endif; ?>
      </span>
    </li>
  <?php endforeach; ?>
</ol>
