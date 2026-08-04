<?php
/**
 * POST-only delete control.
 *
 * Destructive actions must never sit behind a GET link: a link prefetcher, a
 * crawler or an accidental history revisit carrying an admin session would fire
 * them with no user intent involved.
 *
 * @var string $action  URL to post to
 * @var string $confirm Confirmation prompt shown before submitting
 * @var string $label   Button contents (icon markup and/or text)
 * @var string $class   Button classes
 * @var string $wrap    Classes for the wrapping form. Use 'd-contents' inside a
 *                      .btn-group so the button stays a direct visual child and
 *                      keeps the group's border radii.
 */
$wrap = $wrap ?? 'd-inline';
?>
<form action="<?= esc($action) ?>" method="post" class="<?= esc($wrap) ?>"
      onsubmit="return confirm('<?= esc($confirm, 'js') ?>');">
    <?= csrf_field() ?>
    <button type="submit" class="<?= esc($class) ?>"><?= $label ?></button>
</form>
