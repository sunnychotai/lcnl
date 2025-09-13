<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-ocean d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">FAQ Administration</h1>
    <p class="lead fs-5 mb-0">LCNL Site Admin</p>
  </div>
</section>
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      
      <div class="card shadow-sm border-0 no-hover">
        <div class="card-header bg-brand text-white d-flex justify-content-between align-items-center">
          <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Edit FAQ</h4>
          <a href="<?= base_url('admin/faqs') ?>" class="btn btn-outline-light btn-sm">
            <i class="bi bi-arrow-left"></i> Back
          </a>
        </div>
        
        <div class="card-body">
          <form method="post" action="<?= base_url('admin/faqs/update/'.$faq['id']) ?>">
            <?= csrf_field() ?>

            <!-- Group -->
            <div class="mb-3">
              <label class="form-label fw-semibold"><i class="bi bi-folder2-open me-1"></i> Group</label>
              <select name="faq_group" class="form-select" required>
                <option value="">-- Select Group --</option>
                <?php foreach ($groups as $g): ?>
                  <option value="<?= esc($g) ?>" <?= ($faq['faq_group'] == $g) ? 'selected' : '' ?>>
                    <?= esc($g) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Question -->
            <div class="mb-3">
              <label class="form-label fw-semibold"><i class="bi bi-chat-left-text me-1"></i> Question</label>
              <textarea name="question" class="form-control" rows="2" required><?= esc($faq['question']) ?></textarea>
            </div>

            <!-- Answer -->
            <div class="mb-3">
              <label class="form-label fw-semibold"><i class="bi bi-card-text me-1"></i> Answer</label>
              <textarea name="answer" class="form-control" rows="4" required><?= esc($faq['answer']) ?></textarea>
            </div>

            <!-- Order + Valid -->
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold"><i class="bi bi-list-ol me-1"></i> Display Order</label>
                <input type="number" name="faq_order" class="form-control" value="<?= esc($faq['faq_order']) ?>">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold"><i class="bi bi-check-circle me-1"></i> Valid</label>
                <select name="valid" class="form-select">
                  <option value="1" <?= $faq['valid'] ? 'selected' : '' ?>>Yes</option>
                  <option value="0" <?= !$faq['valid'] ? 'selected' : '' ?>>No</option>
                </select>
              </div>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-end gap-2 mt-3">
              <a href="<?= base_url('admin/faqs') ?>" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Cancel
              </a>
              <button type="submit" class="btn btn-success">
                <i class="bi bi-save"></i> Update FAQ
              </button>
            </div>
          </form>
        </div>
      </div>
      
    </div>
  </div>
</div>

<?= $this->endSection() ?>
