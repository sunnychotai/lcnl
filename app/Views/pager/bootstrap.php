<?php
/**
 * Bootstrap 5 pagination for CodeIgniter's pager.
 *
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>
<ul class="pagination mb-0">
  <?php if ($pager->hasPrevious()): ?>
    <li class="page-item"><a class="page-link" href="<?= $pager->getPrevious() ?>" aria-label="Previous page">&laquo;</a></li>
  <?php endif; ?>

  <?php foreach ($pager->links() as $link): ?>
    <li class="page-item<?= $link['active'] ? ' active' : '' ?>">
      <a class="page-link" href="<?= $link['uri'] ?>"<?= $link['active'] ? ' aria-current="page"' : '' ?>><?= $link['title'] ?></a>
    </li>
  <?php endforeach; ?>

  <?php if ($pager->hasNext()): ?>
    <li class="page-item"><a class="page-link" href="<?= $pager->getNext() ?>" aria-label="Next page">&raquo;</a></li>
  <?php endif; ?>
</ul>
