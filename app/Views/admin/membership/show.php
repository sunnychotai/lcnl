<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
use Config\Family as FamilyConfig;
$familyConfig = new FamilyConfig();
$relationMap = $familyConfig->relations;
?>

<div class="container-fluid py-4">

  <!-- Header with gradient banner -->
  <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden"
    style="background: linear-gradient(135deg, var(--brand) 0%, #982d52 100%);">
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
                <span class="badge-lcnl-id bg-white text-brand">LCNL<?= (int) $m['id'] ?></span>
                <span class="badge bg-<?= $m['status'] === 'active'
                  ? 'success'
                  : ($m['status'] === 'pending' ? 'warning text-dark' : 'secondary') ?> px-3 py-2">
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

  <!-- Flash Messages -->
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

    <!-- LEFT COLUMN -->
    <div class="col-lg-8">

      <!-- Contact Information -->
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
          <h5 class="fw-bold text-brand mb-4">
            <i class="bi bi-person-lines-fill me-2"></i>Contact Information
          </h5>

          <div class="row g-4">

            <!-- Email -->
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

            <!-- Mobile -->
            <div class="col-md-6">
              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-telephone-fill"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Mobile Number</div>
                  <div class="info-value"><?= esc($m['mobile'] ?: 'Not provided') ?></div>
                </div>
              </div>
            </div>

            <!-- DOB -->
            <div class="col-md-6">
              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-cake2-fill"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Date of Birth</div>
                  <div class="info-value">
                    <?= $m['date_of_birth']
                      ? date('d M Y', strtotime($m['date_of_birth']))
                      : 'Not provided' ?>
                  </div>
                </div>
              </div>
            </div>

            <!-- Gender -->
            <div class="col-md-6">
              <div class="info-item">
                <div class="info-icon">
                  <i class="bi bi-gender-ambiguous"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Gender</div>
                  <div class="info-value">
                    <?= esc(
                      ucwords(str_replace('_', ' ', $m['gender'] ?: 'Not specified'))
                    ) ?>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Address Block -->
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
                  <?= $m['address1'] ? '<div>' . esc($m['address1']) . '</div>' : '' ?>
                  <?= $m['address2'] ? '<div>' . esc($m['address2']) . '</div>' : '' ?>

                  <?php if ($m['city'] || $m['postcode']): ?>
                    <div class="mt-1">
                      <?= esc($m['city']) ?>
                      <?= $m['city'] && $m['postcode'] ? ', ' : '' ?>
                      <?= esc($m['postcode']) ?>
                    </div>
                  <?php endif; ?>
                </div>

              </div>
            </div>

          <?php else: ?>

            <div class="text-center text-muted py-5">
              <i class="bi bi-house-slash fs-1 mb-3 d-block"></i>
              No address information provided
            </div>

          <?php endif; ?>

        </div>
      </div>

      <!-- FAMILY MEMBERS -->
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
                    <th>Name</th>
                    <th>Relation</th>
                    <th>Age</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Notes</th>
                  </tr>
                </thead>

                <tbody>

                  <?php foreach ($family as $f): ?>
                    <?php
                    $key = strtolower($f['relation']);
                    $rel = $relationMap[$key] ?? null;
                    $label = $rel['label'] ?? ucfirst($f['relation']);
                    $icon = $rel['icon'] ?? 'bi-person';

                    $age = $f['year_of_birth']
                      ? (date('Y') - (int) $f['year_of_birth'])
                      : null;
                    ?>

                    <tr>
                      <td class="fw-semibold"><?= esc($f['name']) ?></td>

                      <td>
                        <span class="relation-badge relation-<?= esc($key) ?>">
                          <i class="bi <?= esc($icon) ?> me-1"></i>
                          <?= esc($label) ?>
                        </span>
                      </td>

                      <td class="text-muted">
                        <?= $age ? "{$age} yrs" : '-' ?>
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

    <!-- ================================
         RIGHT SIDEBAR
    ================================= -->
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
                  <div class="timeline-value">
                    <?= date('d M Y, g:i A', strtotime($m['last_login'])) ?>
                  </div>
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
                  <div class="timeline-value">
                    <?= date('d M Y, g:i A', strtotime($m['verified_at'])) ?>
                  </div>
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
                  <div class="timeline-value">
                    <?= date('d M Y, g:i A', strtotime($m['consent_at'])) ?>
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <div class="timeline-item">
              <div class="timeline-icon bg-primary">
                <i class="bi bi-person-plus-fill"></i>
              </div>
              <div class="timeline-content">
                <div class="timeline-label">Member Since</div>
                <div class="timeline-value">
                  <?= date('d M Y', strtotime($m['created_at'])) ?>
                </div>
              </div>
            </div>

            <?php if ($m['updated_at'] !== $m['created_at']): ?>
              <div class="timeline-item">
                <div class="timeline-icon bg-secondary">
                  <i class="bi bi-pencil-square"></i>
                </div>
                <div class="timeline-content">
                  <div class="timeline-label">Last Updated</div>
                  <div class="timeline-value">
                    <?= date('d M Y, g:i A', strtotime($m['updated_at'])) ?>
                  </div>
                </div>
              </div>
            <?php endif; ?>

          </div>
        </div>
      </div>

      <!-- Membership Type Box -->
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">

          <h6 class="fw-bold text-brand mb-3">
            <i class="bi bi-award-fill me-2"></i>Membership Type
          </h6>

          <!-- Determine current membership -->
          <?php
          $membershipModel = new \App\Models\MembershipModel();
          $membership = $membershipModel->where('member_id', $m['id'])
            ->orderBy('id', 'DESC')
            ->first();

          $typeRaw = $membership['membership_type'] ?? 'Standard';
          $type = ucfirst(strtolower($typeRaw));
          ?>

          <div class="p-3 rounded-3 bg-light mb-3 border-start border-4"
            style="border-color: var(--accent1) !important;">

            <p class="fw-semibold mb-1">
              <i class="bi bi-patch-check-fill text-accent me-1"></i>
              Current Type: <strong><?= esc($type) ?></strong>
            </p>

            <?php if ($type === 'Life'): ?>
              <p class="text-muted small mb-0">This member is a registered Life Member.</p>
            <?php else: ?>
              <p class="text-muted small mb-0">This member currently has Standard Membership.</p>
            <?php endif; ?>
          </div>

          <button class="btn btn-outline-brand btn-pill w-100" data-bs-toggle="modal"
            data-bs-target="#membershipTypeModal">
            <i class="bi bi-pencil-square me-1"></i>
            Update Membership Type
          </button>

        </div>
      </div>

      <!-- Membership History -->
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">

          <h6 class="fw-bold text-brand mb-3">
            <i class="bi bi-clock-fill me-2"></i>Membership History
          </h6>

          <?php
          $historyModel = new \App\Models\MembershipHistoryModel();
          $history = $historyModel
            ->where('member_id', $m['id'])
            ->orderBy('id', 'DESC')
            ->findAll();
          ?>

          <?php if ($history): ?>
            <ul class="list-group list-group-flush">

              <?php foreach ($history as $h): ?>
                <li class="list-group-item px-0 py-2 small">

                  <div>
                    <strong><?= esc($h['old_type'] ?: 'None') ?></strong>
                    →
                    <strong class="text-brand"><?= esc($h['new_type']) ?></strong>
                  </div>

                  <div class="text-muted">
                    <?= date('d M Y, g:i A', strtotime($h['created_at'])) ?>
                  </div>

                  <?php if ($h['changed_by']): ?>
                    <div class="text-muted small">
                      Updated by Admin ID: <?= esc($h['changed_by']) ?>
                    </div>
                  <?php endif; ?>

                </li>
              <?php endforeach; ?>

            </ul>
          <?php else: ?>
            <p class="text-muted small mb-0">No membership changes recorded.</p>
          <?php endif; ?>

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
              <form method="post" action="<?= base_url('admin/membership/' . $m['id'] . '/disable') ?>"
                onsubmit="return confirm('Disable this member?');">
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

<!-- STYLES -->
<style>
  .relation-badge {
    font-size: .8rem;
    border-radius: 999px;
    padding: .25rem .75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    background: #f4f4f5;
    color: #444;
  }

  /* Auto-colour relation badges */
  .relation-spouse {
    background: #fff1f3;
    color: #ba1b42;
  }

  .relation-son {
    background: #eef8ff;
    color: #0a66c2;
  }

  .relation-daughter {
    background: #eef8ff;
    color: #0a66c2;
  }

  .relation-mother {
    background: #f5fff2;
    color: #2e7d32;
  }

  .relation-father {
    background: #f5fff2;
    color: #2e7d32;
  }

  .relation-grandparent {
    background: #fff7e6;
    color: #ad6800;
  }

  .relation-sibling {
    background: #fff7e6;
    color: #ad6800;
  }

  .relation-other {
    background: #e9ecef;
    color: #555;
  }

  .info-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: .75rem;
  }

  .info-item:hover {
    background: #e9ecef;
  }

  .info-icon {
    width: 45px;
    height: 45px;
    border-radius: .75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--brand), #982d52);
    color: white;
    font-size: 1.25rem;
  }

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
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  .timeline-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
  }
</style>


<!-- Update Membership Type Modal -->
<div class="modal fade" id="membershipTypeModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form action="<?= base_url('admin/membership/' . $m['id'] . '/update-type') ?>" method="post" class="modal-content">
      <?= csrf_field() ?>

      <div class="modal-header">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-award-fill me-2 text-brand"></i>
          Update Membership Type
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <div class="mb-3">
          <label class="form-label fw-semibold">Membership Type</label>

          <select name="membership_type" class="form-select rounded-pill">
            <option value="Standard" <?= $type === 'Standard' ? 'selected' : '' ?>>Standard</option>
            <option value="Life" <?= $type === 'Life' ? 'selected' : '' ?>>Life Member</option>
          </select>
        </div>

        <div class="alert alert-info small mb-0">
          <i class="bi bi-info-circle-fill me-1"></i>
          Updating the membership type will immediately update the member's access.
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-brand rounded-pill px-4">
          <i class="bi bi-check2-circle me-1"></i>Save Changes
        </button>
      </div>

    </form>
  </div>
</div>

<?= $this->endSection() ?>

