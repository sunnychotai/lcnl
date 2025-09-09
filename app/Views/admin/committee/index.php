<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">
  <h2 class="mb-4">Committee Management</h2>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <!-- Tabs -->
  <ul class="nav nav-tabs" id="committeeTabs" role="tablist">
    <?php foreach ($committeeTypes as $i => $type): ?>
      <li class="nav-item" role="presentation">
        <button class="nav-link <?= $i === 0 ? 'active' : '' ?>"
                id="tab-<?= strtolower($type) ?>"
                data-bs-toggle="tab"
                data-bs-target="#<?= strtolower($type) ?>-tab"
                type="button"
                role="tab">
          <?= $type ?>
        </button>
      </li>
    <?php endforeach; ?>
  </ul>

  <div class="tab-content mt-3">
    <?php foreach ($committeeTypes as $i => $type): ?>
      <div class="tab-pane fade <?= $i === 0 ? 'show active' : '' ?>"
           id="<?= strtolower($type) ?>-tab"
           role="tabpanel">

        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4><?= $type ?> Committee</h4>
          <a href="<?= base_url('admin/committee/create?committee=' . urlencode($type)) ?>" class="btn btn-brand">
            <i class="bi bi-plus-circle"></i> Add <?= $type ?> Member
          </a>
        </div>

        <table class="table table-striped table-bordered committee-table" id="table-<?= strtolower($type) ?>">
  <thead>
    <tr>
      <th>Order</th>
      <th>Photo</th> <!-- ✅ Thumbnail column -->
      <th>Name</th>
      <th>Email</th>
      <th>Role</th>
      <th>Committee</th>
      
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if (! empty($members[$type])): ?>
      <?php foreach ($members[$type] as $member): ?>
        <tr>
          <td><?= esc($member['display_order']) ?></td>
          
          <!-- ✅ Thumbnail with file existence check -->
<td>
  <?php 
    $imgPath = FCPATH . ltrim($member['image'], '/'); // absolute file path
    $imgUrl  = base_url($member['image']);

    if (!empty($member['image']) && file_exists($imgPath)) {
        $finalImg = $imgUrl;
    } else {
        $finalImg = base_url('/uploads/committee/lcnl-placeholder.png');
    }
  ?>
  <img src="<?= $finalImg ?>" 
       alt="Photo" 
       style="width:40px;height:40px;object-fit:cover;border-radius:50%;">
</td>



          <td><?= esc($member['firstname']) ?> <?= esc($member['surname']) ?></td>
          <td><?= esc($member['email']) ?></td>
          <td><?= esc($member['role']) ?></td>
          <td><?= esc($member['committee']) ?></td>
          
          <td>
            <a href="<?= base_url('admin/committee/edit/'.$member['id']) ?>" class="btn btn-sm btn-warning">
              <i class="bi bi-pencil-square"></i>
            </a>
            <a href="<?= base_url('admin/committee/clone/'.$member['id']) ?>" class="btn btn-sm btn-info">
              <i class="bi bi-files"></i> <!-- ✅ Clone icon -->
            </a>
            <a href="<?= base_url('admin/committee/delete/'.$member['id']) ?>" 
               class="btn btn-sm btn-danger"
               onclick="return confirm('Delete this member?');">
              <i class="bi bi-trash"></i>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>

      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- DataTables JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
  $(document).ready(function() {
    $('.committee-table').DataTable({
      paging: false,    // no pagination, all rows visible
      searching: true,  // enable search
      ordering: true,   // enable sort
      info: false
    });
  });
</script>

<?= $this->endSection() ?>
