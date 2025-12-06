<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-4">

  <!-- Header with gradient banner -->
  <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, var(--brand) 0%, #982d52 100%);">
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="text-white">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="rounded-circle bg-white bg-opacity-25 p-3">
              <i class="bi bi-person-circle fs-1"></i>
            </div>
            <div>
              <h1 class="h3 fw-bold mb-1"><?= esc($m['first_name'] . ' ' . $m['last_name']) ?></h1>
              <div class="d-flex align-items-center gap-3">
                <span class="badge-lcnl-id bg-white text-brand">LCNL<?= (int)$m['id'] ?></span>
                <span class="badge bg-<?= $m['status'] === 'active' ? 'success' : ($m['status'] === 'pending' ? 'warning text-dark' : 'secondary') ?> px-3 py-2">
                  <?= ucfirst($m['status']) ?>
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex gap-2">
          <a href="<?= base_url('admin/membership/' . $m['id'] . '/edit') ?>" class="btn btn-light btn-pill px-4">
            <i class="bi bi-pencil me-2"></i>Edit
          </a>
          <a href="<?= base_url('admin/membership') ?>" class="btn btn-outline-light btn-pill px-4">
            <i class="bi bi-arrow-left me-2"></i>Back
          </a>
        </div>
      </div>
    </div>
  </div>

  <?php if ($msg = session()->getFlashdata('message')): ?>
    <div class="alert alert-success shadow-sm border-0 rounded-4 mb-4">
      <i class="bi bi-check-circle-fill me-2"></i><?= esc($msg) ?>
    </div>
  <?php endif; ?>
  <?php if ($err = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger shadow-sm border-0 rounded-4 mb-4">
      <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc($err) ?>
    </div>
  <?php endif; ?>

  <div class="row g-4">

    <!-- Main Content -->
    <div class="col-lg-8">

      <!-- Contact Information -->
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
          <h5 class="fw-bold text-brand mb-4">
            <i class="bi bi-person-lines-fill me-2"></i>Contact Information
          </h5>

          <div class="row g-4">
            <div class="col-md-6">
              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-envelope-fill"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Email Address</div>
                  <div class="info-value"><?= esc($m['email']) ?></div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-telephone-fill"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Mobile Number</div>
                  <div class="info-value"><?= esc($m['mobile'] ?? 'Not provided') ?></div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-cake2-fill"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Date of Birth</div>
                  <div class="info-value">
                    <?= $m['date_of_birth'] ? date('d M Y', strtotime($m['date_of_birth'])) : 'Not provided' ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-gender-ambiguous"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Gender</div>
                  <div class="info-value">
                    <?= esc(ucwords(str_replace('_', ' ', $m['gender'] ?? 'Not specified'))) ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Address Information -->
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
          <h5 class="fw-bold text-brand mb-4">
            <i class="bi bi-geo-alt-fill me-2"></i>Address
          </h5>

          <?php if ($m['address1'] || $m['city'] || $m['postcode']): ?>
            <div class="address-display p-4 bg-light rounded-3">
              <div class="d-flex align-items-start gap-3">
                <i class="bi bi-house-fill text-brand fs-4"></i>
                <div>
                  <?php if ($m['address1']): ?>
                    <div><?= esc($m['address1']) ?></div>
                  <?php endif; ?>
                  <?php if ($m['address2']): ?>
                    <div><?= esc($m['address2']) ?></div>
                  <?php endif; ?>
                  <?php if ($m['city'] || $m['postcode']): ?>
                    <div class="mt-1">
                      <?= esc($m['city'] ?? '') ?>
                      <?= $m['city'] && $m['postcode'] ? ', ' : '' ?>
                      <?= esc($m['postcode'] ?? '') ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php else: ?>
            <div class="text-center text-muted py-4">
              <i class="bi bi-house-slash fs-1 mb-2 d-block"></i>
              No address information provided
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Family Members -->
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-brand mb-0">
              <i class="bi bi-people-fill me-2"></i>Family Members
            </h5>
            <?php if (!empty($family)): ?>
              <span class="badge bg-brand rounded-pill px-3 py-2">
                <?= count($family) ?> member<?= count($family) !== 1 ? 's' : '' ?>
              </span>
            <?php endif; ?>
          </div>

          <?php if (!empty($family)): ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="border-0 fw-semibold">Name</th>
                    <th class="border-0 fw-semibold">Relation</th>
                    <th class="border-0 fw-semibold">Age</th>
                    <th class="border-0 fw-semibold">Email</th>
                    <th class="border-0 fw-semibold">Gender</th>
                    <th class="border-0 fw-semibold">Notes</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($family as $f):
                    $age = $f['year_of_birth'] ? date('Y') - (int)$f['year_of_birth'] : null;
                  ?>
                    <tr>
                      <td class="fw-semibold"><?= esc($f['name']) ?></td>
                      <td>
                        <span class="relation-badge relation-<?= esc($f['relation']) ?>">
                          <?= esc($f['relation']) ?>
                        </span>
                      </td>
                      <td class="text-muted">
                        <?= $age ? $age . ' yrs' : '-' ?>
                        <?php if ($f['year_of_birth']): ?>
                          <span class="small">(<?= $f['year_of_birth'] ?>)</span>
                        <?php endif; ?>
                      </td>
                      <td class="small"><?= esc($f['email'] ?? '-') ?></td>
                      <td class="small text-muted"><?= esc($f['gender'] ?? '-') ?></td>
                      <td class="small text-muted"><?= esc($f['notes'] ?? '-') ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="text-center text-muted py-5">
              <i class="bi bi-people fs-1 mb-3 d-block"></i>
              <h6 class="fw-semibold">No Family Members</h6>
              <p class="small mb-0">This member hasn't added any family members yet.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">

      <!-- Activity Timeline -->
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
          <h6 class="fw-bold text-brand mb-4">
            <i class="bi bi-clock-history me-2"></i>Activity Timeline
          </h6>

          <div class="timeline">
            <?php if ($m['last_login']): ?>
              <div class="timeline-item">
                <div class="timeline-icon bg-success">
                  <i class="bi bi-box-arrow-in-right"></i>
                </div>
                <div class="timeline-content">
                  <div class="timeline-label">Last Login</div>
                  <div class="timeline-value"><?= date('d M Y, g:i A', strtotime($m['last_login'])) ?></div>
                </div>
              </div>
            <?php endif; ?>

            <?php if ($m['verified_at']): ?>
              <div class="timeline-item">
                <div class="timeline-icon bg-info">
                  <i class="bi bi-patch-check-fill"></i>
                </div>
                <div class="timeline-content">
                  <div class="timeline-label">Email Verified</div>
                  <div class="timeline-value"><?= date('d M Y, g:i A', strtotime($m['verified_at'])) ?></div>
                </div>
              </div>
            <?php endif; ?>

            <?php if ($m['consent_at']): ?>
              <div class="timeline-item">
                <div class="timeline-icon bg-warning">
                  <i class="bi bi-shield-check"></i>
                </div>
                <div class="timeline-content">
                  <div class="timeline-label">Consent Given</div>
                  <div class="timeline-value"><?= date('d M Y, g:i A', strtotime($m['consent_at'])) ?></div>
                </div>
              </div>
            <?php endif; ?>

            <div class="timeline-item">
              <div class="timeline-icon bg-primary">
                <i class="bi bi-person-plus-fill"></i>
              </div>
              <div class="timeline-content">
                <div class="timeline-label">Member Since</div>
                <div class="timeline-value"><?= date('d M Y', strtotime($m['created_at'])) ?></div>
              </div>
            </div>

            <?php if ($m['updated_at'] !== $m['created_at']): ?>
              <div class="timeline-item">
                <div class="timeline-icon bg-secondary">
                  <i class="bi bi-pencil-square"></i>
                </div>
                <div class="timeline-content">
                  <div class="timeline-label">Last Updated</div>
                  <div class="timeline-value"><?= date('d M Y, g:i A', strtotime($m['updated_at'])) ?></div>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
          <h6 class="fw-bold text-brand mb-4">
            <i class="bi bi-lightning-charge-fill me-2"></i>Quick Actions
          </h6>

          <div class="d-grid gap-3">
            <?php if (($m['status'] ?? '') === 'active'): ?>
              <form method="post" action="<?= base_url('admin/membership/' . $m['id'] . '/disable') ?>" onsubmit="return confirm('Are you sure you want to disable this member?');">
                <?= csrf_field() ?>
                <button class="btn btn-outline-danger w-100 btn-pill">
                  <i class="bi bi-slash-circle me-2"></i>Disable Member
                </button>
              </form>
            <?php else: ?>
              <form method="post" action="<?= base_url('admin/membership/' . $m['id'] . '/activate') ?>">
                <?= csrf_field() ?>
                <button class="btn btn-success w-100 btn-pill">
                  <i class="bi bi-check2-circle me-2"></i>Activate Member
                </button>
              </form>
            <?php endif; ?>

            <form method="post" action="<?= base_url('admin/membership/' . $m['id'] . '/resend') ?>">
              <?= csrf_field() ?>
              <button class="btn btn-outline-brand w-100 btn-pill">
                <i class="bi bi-envelope me-2"></i>Resend Verification
              </button>
            </form>

            <a href="<?= base_url('admin/membership/' . $m['id'] . '/edit') ?>" class="btn btn-brand w-100 btn-pill">
              <i class="bi bi-pencil-square me-2"></i>Edit Profile
            </a>
          </div>
        </div>
      </div>

    </div>

  </div>
</div>

<style>
  /* Info Item Styles */
  .info-item {
    display: flex;
    align-items: start;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 0.75rem;
    transition: all 0.2s ease;
  }

  .info-item:hover {
    background: #e9ecef;
    transform: translateY(-2px);
  }

  .info-icon {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, var(--brand) 0%, #982d52 100%);
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    flex-shrink: 0;
  }

  .info-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    margin-bottom: 0.25rem;
  }

  .info-value {
    font-size: 1rem;
    color: #212529;
    font-weight: 500;
  }

  /* Timeline Styles */
  .timeline {
    position: relative;
  }

  .timeline::before {
    content: '';
    position: absolute;
    left: 17px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
  }

  .timeline-item {
    display: flex;
    align-items: start;
    gap: 1rem;
    margin-bottom: 1.5rem;
    position: relative;
  }

  .timeline-item:last-child {
    margin-bottom: 0;
  }

  .timeline-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
  }

  .timeline-label {
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: 600;
    margin-bottom: 0.25rem;
  }

  .timeline-value {
    font-size: 0.875rem;
    color: #212529;
    font-weight: 500;
  }

  /* Relation Badge Styles */
  .relation-badge {
    font-size: 0.75rem;
    border-radius: 999px;
    padding: 0.25rem 0.75rem;
    font-weight: 600;
    display: inline-block;
  }

  .relation-Spouse {
    background: #fff1f3;
    color: #ba1b42;
  }

  .relation-Child {
    background: #eef8ff;
    color: #0a66c2;
  }

  .relation-Parent {
    background: #f5fff2;
    color: #2e7d32;
  }

  .relation-Sibling {
    background: #fff7e6;
    color: #ad6800;
  }

  .relation-Other {
    background: #f4f4f5;
    color: #444;
  }

  /* Address Display */
  .address-display {
    font-size: 0.95rem;
    line-height: 1.6;
  }

  /* Table Improvements */
  .table-hover tbody tr:hover {
    background-color: rgba(122, 29, 60, 0.05);
  }

  /* Mobile Responsive */
  @media (max-width: 768px) {
    .info-item {
      padding: 0.75rem;
    }

    .info-icon {
      width: 40px;
      height: 40px;
      font-size: 1rem;
    }
  }
</style>

<?= $this->endSection() ?>