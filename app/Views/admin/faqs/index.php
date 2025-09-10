<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">
  <h2>Manage FAQs</h2>
  <a href="<?= base_url('admin/faqs/create') ?>" class="btn btn-success mb-3">+ Add FAQ</a>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>

  <?php if (!empty($groupedFaqs)): ?>
    <?php foreach ($groupedFaqs as $groupName => $faqs): ?>
      <h4 class="mt-4"><?= esc($groupName) ?></h4>
      <ul class="list-group sortable-group" data-group="<?= esc($groupName) ?>">
        <?php foreach ($faqs as $faq): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center"
              data-id="<?= $faq['id'] ?>">
            <span>
              <?= esc($faq['question']) ?>
            </span>
            <span>
              <a href="<?= base_url('admin/faqs/edit/'.$faq['id']) ?>" class="btn btn-sm btn-primary">Edit</a>
              <a href="<?= base_url('admin/faqs/delete/'.$faq['id']) ?>" class="btn btn-sm btn-danger"
                 onclick="return confirm('Delete this FAQ?')">Delete</a>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No FAQs found.</p>
  <?php endif; ?>
</div>

<!-- jQuery + jQuery UI -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
$(function() {
  $(".sortable-group").each(function() {
    var $list = $(this);
    $list.sortable({
      update: function(event, ui) {
        var group = $list.data("group");
        var order = $list.sortable("toArray", { attribute: "data-id" });
        console.log("Group:", group, "Order:", order);

        $.post("<?= base_url('admin/faqs/reorder') ?>", {
          group: group,
          order: order,
          <?= csrf_token() ?>: "<?= csrf_hash() ?>"
        }, function(response) {
          console.log("Reorder response:", response);
        });
      }
    });
  });
});
</script>

<?= $this->endSection() ?>