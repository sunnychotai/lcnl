<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
use Config\Family as FamilyConfig;
$familyConfig = new FamilyConfig();
$relationMap = $familyConfig->relations;

// Calculate stats
$memberSince = new DateTime($m['created_at']);
$now = new DateTime();
$daysAsMember = $memberSince->diff($now)->days;
$yearsAsMember = floor($daysAsMember / 365);

$lastLogin = $m['last_login'] ? new DateTime($m['last_login']) : null;
$daysSinceLogin = $lastLogin ? $lastLogin->diff($now)->days : null;

$familyCount = count($family);

// Account health score (0-100)
$healthScore = 0;
if ($m['verified_at'])
  $healthScore += 40;
if ($m['last_login'])
  $healthScore += 30;
if ($m['consent_at'])
  $healthScore += 15;
if ($familyCount > 0)
  $healthScore += 15;
?>

<div class="container-fluid py-4">

  <!-- Header with gradient banner -->
  <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden header-gradient">
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

        <div class="text-white">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="avatar-circle">
              <span class="avatar-initials">
                <?= strtoupper(substr($m['first_name'], 0, 1) . substr($m['last_name'], 0, 1)) ?>
              </span>
            </div>
            <div>
              <h1 class="h3 fw-bold mb-1"><?= esc($m['first_name'] . ' ' . $m['last_name']) ?></h1>

              <div class="d-flex align-items-center gap-3">
                <span class="badge-lcnl-id bg-white text-brand">LCNL<?= (int) $m['id'] ?></span>
                <span class="badge badge-status-<?= $m['status'] ?> px-3 py-2">
                  <i class="bi bi-circle-fill pulse-dot"></i>
                  <?= ucfirst($m['status']) ?>
                </span>
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex gap-2">
          <a href="<?= base_url('admin/membership/' . $m['id'] . '/edit') ?>"
            class="btn btn-light btn-pill px-4 btn-hover-lift" data-tooltip="Edit this member's profile">
            <i class="bi bi-pencil me-2"></i>Edit
          </a>

          <a href="<?= base_url('admin/membership') ?>" class="btn btn-outline-light btn-pill px-4 btn-hover-lift"
            data-tooltip="Return to member list">
            <i class="bi bi-arrow-left me-2"></i>Back
          </a>
        </div>

      </div>
    </div>
  </div>

  <!-- Flash Messages -->
  <?php if ($msg = session()->getFlashdata('message')): ?>
    <div class="alert alert-success shadow-sm border-0 rounded-4 mb-4 alert-animated">
      <i class="bi bi-check-circle-fill me-2"></i><?= esc($msg) ?>
    </div>
  <?php endif; ?>

  <?php if ($err = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger shadow-sm border-0 rounded-4 mb-4 alert-animated">
      <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc($err) ?>
    </div>
  <?php endif; ?>

  <!-- Stats Dashboard -->
  <div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
      <div class="stat-card">
        <div class="stat-icon bg-gradient-primary">
          <i class="bi bi-calendar-check"></i>
        </div>
        <div class="stat-content">
          <div class="stat-value"><?= $daysAsMember ?></div>
          <div class="stat-label">Days as Member</div>
          <?php if ($yearsAsMember > 0): ?>
            <div class="stat-subtext"><?= $yearsAsMember ?> year<?= $yearsAsMember > 1 ? 's' : '' ?></div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6">
      <div class="stat-card">
        <div class="stat-icon bg-gradient-success">
          <i class="bi bi-people-fill"></i>
        </div>
        <div class="stat-content">
          <div class="stat-value"><?= $familyCount ?></div>
          <div class="stat-label">Family Members</div>
          <div class="stat-subtext">
            <?= $familyCount === 0 ? 'No family added' : ($familyCount === 1 ? '1 person' : $familyCount . ' people') ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6">
      <div class="stat-card">
        <div class="stat-icon bg-gradient-warning">
          <i class="bi bi-clock-history"></i>
        </div>
        <div class="stat-content">
          <div class="stat-value"><?= $daysSinceLogin !== null ? $daysSinceLogin : '—' ?></div>
          <div class="stat-label">Days Since Login</div>
          <div class="stat-subtext" data-timestamp="<?= $m['last_login'] ?? '' ?>">
            <?= $lastLogin ? '<span class="relative-time"></span>' : 'Never logged in' ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6">
      <div class="stat-card">
        <div class="stat-icon bg-gradient-info">
          <i class="bi bi-heart-pulse"></i>
        </div>
        <div class="stat-content">
          <div class="stat-value"><?= $healthScore ?>%</div>
          <div class="stat-label">Account Health</div>
          <div class="progress mt-2" style="height: 6px;">
            <div class="progress-bar bg-gradient-info" style="width: <?= $healthScore ?>%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">

    <!-- LEFT COLUMN -->
    <div class="col-lg-8">

      <!-- Contact Information -->
      <div class="card border-0 shadow-sm rounded-4 mb-4 card-hover">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-brand mb-0">
              <i class="bi bi-person-lines-fill me-2"></i>Contact Information
            </h5>
            <?php if ($m['verified_at']): ?>
              <span class="badge bg-success-subtle text-success px-3 py-2">
                <i class="bi bi-patch-check-fill me-1"></i>Verified
              </span>
            <?php endif; ?>
          </div>

          <div class="row g-4">

            <!-- Email -->
            <div class="col-md-6">
              <div class="info-item clickable" onclick="copyToClipboard('<?= esc($m['email']) ?>', this)">
                <div class="info-icon">
                  <i class="bi bi-envelope-fill"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Email Address</div>
                  <div class="info-value"><?= esc($m['email']) ?></div>
                </div>
                <div class="copy-indicator">
                  <i class="bi bi-clipboard"></i>
                </div>
              </div>
            </div>

            <!-- Mobile -->
            <div class="col-md-6">
              <div class="info-item <?= $m['mobile'] ? 'clickable' : '' ?>" <?= $m['mobile'] ? "onclick=\"copyToClipboard('" . esc($m['mobile']) . "', this)\"" : '' ?>>
                <div class="info-icon">
                  <i class="bi bi-telephone-fill"></i>
                </div>
                <div class="info-content">
                  <div class="info-label">Mobile Number</div>
                  <div class="info-value"><?= esc($m['mobile'] ?: 'Not provided') ?></div>
                </div>
                <?php if ($m['mobile']): ?>
                  <div class="copy-indicator">
                    <i class="bi bi-clipboard"></i>
                  </div>
                <?php endif; ?>
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
                  <?php
                  $dob = $m['date_of_birth'] ?? null;
                  $dobDisplay = (
                    empty($dob) ||
                    $dob === '0000-00-00' ||
                    $dob === '0000-00-00 00:00:00'
                  )
                    ? null
                    : date('d M Y', strtotime($dob));

                  // Calculate age
                  $age = null;
                  if ($dobDisplay) {
                    $dobDate = new DateTime($dob);
                    $age = $dobDate->diff($now)->y;
                  }
                  ?>

                  <div class="info-value">
                    <?= $dobDisplay ?? 'Not provided' ?>
                    <?php if ($age): ?>
                      <span class="badge bg-light text-dark ms-2"><?= $age ?> years old</span>
                    <?php endif; ?>
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

          <!-- Quick Actions Row -->
          <div class="mt-4 pt-4 border-top">
            <div class="d-flex gap-2 flex-wrap">
              <button class="btn btn-sm btn-brand-light btn-pill"
                onclick="window.location.href='mailto:<?= esc($m['email']) ?>'">
                <i class="bi bi-envelope me-1"></i>Send Email
              </button>
              <?php if ($m['mobile']): ?>
                <button class="btn btn-sm btn-brand-light btn-pill"
                  onclick="window.location.href='tel:<?= esc($m['mobile']) ?>'">
                  <i class="bi bi-telephone me-1"></i>Call
                </button>
              <?php endif; ?>
            </div>
          </div>

        </div>
      </div>

      <!-- Address Block -->
      <div class="card border-0 shadow-sm rounded-4 mb-4 card-hover">
        <div class="card-body p-4">
          <h5 class="fw-bold text-brand mb-4">
            <i class="bi bi-geo-alt-fill me-2"></i>Address
          </h5>

          <?php if ($m['address1'] || $m['city'] || $m['postcode']): ?>

            <div class="address-display p-4 bg-light rounded-3">
              <div class="d-flex align-items-start gap-3">
                <i class="bi bi-house-fill text-brand fs-4"></i>

                <div class="flex-grow-1">
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

                <?php if ($m['postcode']): ?>
                  <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($m['address1'] . ' ' . $m['postcode']) ?>"
                    target="_blank" class="btn btn-sm btn-outline-brand btn-pill">
                    <i class="bi bi-map me-1"></i>View Map
                  </a>
                <?php endif; ?>

              </div>
            </div>

          <?php else: ?>

            <div class="empty-state">
              <div class="empty-state-icon">
                <i class="bi bi-house-slash"></i>
              </div>
              <h6 class="empty-state-title">No Address Information</h6>
              <p class="empty-state-text">This member hasn't provided their address yet.</p>
              <button class="btn btn-sm btn-brand btn-pill mt-2"
                onclick="window.location.href='<?= base_url('admin/membership/' . $m['id'] . '/edit') ?>'">
                <i class="bi bi-plus-circle me-1"></i>Add Address
              </button>
            </div>

          <?php endif; ?>

        </div>
      </div>

      <!-- FAMILY MEMBERS - CARD LAYOUT -->
      <div class="card border-0 shadow-sm rounded-4 card-hover">
        <div class="card-body p-4">

          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-brand mb-0">
              <i class="bi bi-people-fill me-2"></i>Family Members
            </h5>

            <div class="d-flex align-items-center gap-2">
              <?php if (!empty($family)): ?>
                <span class="badge bg-brand rounded-pill px-3 py-2">
                  <?= count($family) ?> member<?= count($family) !== 1 ? 's' : '' ?>
                </span>
              <?php endif; ?>
              <button class="btn btn-sm btn-brand btn-pill"
                onclick="window.location.href='<?= base_url('admin/membership/' . $m['id'] . '/edit#family') ?>'">
                <i class="bi bi-plus-circle me-1"></i>Add Member
              </button>
            </div>
          </div>

          <?php if (!empty($family)): ?>

            <div class="row g-3">
              <?php foreach ($family as $f): ?>
                <?php
                $key = strtolower($f['relation']);
                $rel = $relationMap[$key] ?? null;
                $label = $rel['label'] ?? ucfirst($f['relation']);
                $icon = $rel['icon'] ?? 'bi-person';

                $age = $f['year_of_birth']
                  ? (date('Y') - (int) $f['year_of_birth'])
                  : null;

                // Generate initials
                $nameParts = explode(' ', $f['name']);
                $initials = strtoupper(
                  (isset($nameParts[0]) ? substr($nameParts[0], 0, 1) : '') .
                  (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : '')
                );
                ?>

                <div class="col-md-6">
                  <div class="family-card">
                    <div class="family-card-header">
                      <div class="family-avatar family-avatar-<?= esc($key) ?>">
                        <span><?= $initials ?></span>
                      </div>
                      <div class="family-info">
                        <div class="family-name"><?= esc($f['name']) ?></div>
                        <span class="relation-badge relation-<?= esc($key) ?>">
                          <i class="bi <?= esc($icon) ?>"></i>
                          <?= esc($label) ?>
                        </span>
                      </div>
                    </div>

                    <div class="family-card-body">
                      <?php if ($age): ?>
                        <div class="family-detail">
                          <i class="bi bi-calendar3"></i>
                          <span><?= $age ?> years old</span>
                          <span class="text-muted small">(born <?= $f['year_of_birth'] ?>)</span>
                        </div>
                      <?php endif; ?>

                      <?php if ($f['email']): ?>
                        <div class="family-detail">
                          <i class="bi bi-envelope"></i>
                          <span><?= esc($f['email']) ?></span>
                        </div>
                      <?php endif; ?>

                      <?php if ($f['telephone']): ?>
                        <div class="family-detail">
                          <i class="bi bi-telephone"></i>
                          <span><?= esc($f['telephone']) ?></span>
                        </div>
                      <?php endif; ?>

                      <?php if ($f['gender']): ?>
                        <div class="family-detail">
                          <i class="bi bi-gender-ambiguous"></i>
                          <span><?= esc(ucfirst($f['gender'])) ?></span>
                        </div>
                      <?php endif; ?>

                      <?php if ($f['notes']): ?>
                        <div class="family-detail">
                          <i class="bi bi-chat-left-text"></i>
                          <span class="text-muted"><?= esc($f['notes']) ?></span>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>

              <?php endforeach; ?>
            </div>

          <?php else: ?>

            <div class="empty-state">
              <div class="empty-state-icon">
                <i class="bi bi-people"></i>
              </div>
              <h6 class="empty-state-title">No Family Members Added</h6>
              <p class="empty-state-text">Add family members to keep track of relationships and contacts.</p>
              <button class="btn btn-brand btn-pill mt-2"
                onclick="window.location.href='<?= base_url('admin/membership/' . $m['id'] . '/edit#family') ?>'">
                <i class="bi bi-plus-circle me-1"></i>Add First Family Member
              </button>
            </div>

          <?php endif; ?>

        </div>
      </div>

    </div>

    <!-- ================================
         RIGHT SIDEBAR
    ================================= -->
    <div class="col-lg-4">

      <!-- Membership Type Box -->
      <div class="card border-0 shadow-sm rounded-4 mb-4 card-hover">
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
          $isLife = $type === 'Life';
          ?>

          <div class="membership-badge-large mb-3 <?= $isLife ? 'membership-life' : 'membership-standard' ?>">
            <div class="membership-icon">
              <i class="bi <?= $isLife ? 'bi-star-fill' : 'bi-shield-check' ?>"></i>
            </div>
            <div class="membership-content">
              <div class="membership-type"><?= esc($type) ?></div>
              <div class="membership-subtitle">
                <?= $isLife ? 'Lifetime Member' : 'Standard Membership' ?>
              </div>
            </div>
            <?php if ($isLife): ?>
              <div class="membership-confetti">
                <span>🎉</span>
              </div>
            <?php endif; ?>
          </div>

          <?php if ($yearsAsMember > 0): ?>
            <div class="membership-progress mb-3">
              <div class="d-flex justify-content-between mb-2">
                <span class="small text-muted">Membership Journey</span>
                <span class="small fw-semibold"><?= $yearsAsMember ?> year<?= $yearsAsMember > 1 ? 's' : '' ?></span>
              </div>
              <div class="progress" style="height: 8px;">
                <div class="progress-bar bg-gradient-brand" style="width: <?= min(($yearsAsMember / 10) * 100, 100) ?>%">
                </div>
              </div>
            </div>
          <?php endif; ?>

          <button class="btn btn-outline-brand btn-pill w-100" data-bs-toggle="modal"
            data-bs-target="#membershipTypeModal">
            <i class="bi bi-pencil-square me-1"></i>
            Update Membership Type
          </button>

        </div>
      </div>

      <!-- Activity Timeline -->
      <div class="card border-0 shadow-sm rounded-4 mb-4 card-hover">
        <div class="card-body p-4">

          <h6 class="fw-bold text-brand mb-4">
            <i class="bi bi-clock-history me-2"></i>Activity Timeline
          </h6>

          <div class="timeline">

            <?php if ($m['last_login']): ?>
              <div class="timeline-item" data-timestamp="<?= $m['last_login'] ?>">
                <div class="timeline-icon bg-success">
                  <i class="bi bi-box-arrow-in-right"></i>
                </div>
                <div class="timeline-content">
                  <div class="timeline-label">Last Login</div>
                  <div class="timeline-value">
                    <?= date('d M Y, g:i A', strtotime($m['last_login'])) ?>
                  </div>
                  <div class="timeline-relative"></div>
                </div>
              </div>
            <?php endif; ?>

            <?php if ($m['verified_at']): ?>
              <div class="timeline-item" data-timestamp="<?= $m['verified_at'] ?>">
                <div class="timeline-icon bg-info">
                  <i class="bi bi-patch-check-fill"></i>
                </div>
                <div class="timeline-content">
                  <div class="timeline-label">Email Verified</div>
                  <div class="timeline-value">
                    <?= date('d M Y, g:i A', strtotime($m['verified_at'])) ?>
                  </div>
                  <div class="timeline-relative"></div>
                </div>
              </div>
            <?php endif; ?>

            <?php if ($m['consent_at']): ?>
              <div class="timeline-item" data-timestamp="<?= $m['consent_at'] ?>">
                <div class="timeline-icon bg-warning">
                  <i class="bi bi-shield-check"></i>
                </div>
                <div class="timeline-content">
                  <div class="timeline-label">Consent Given</div>
                  <div class="timeline-value">
                    <?= date('d M Y, g:i A', strtotime($m['consent_at'])) ?>
                  </div>
                  <div class="timeline-relative"></div>
                </div>
              </div>
            <?php endif; ?>

            <div class="timeline-item" data-timestamp="<?= $m['created_at'] ?>">
              <div class="timeline-icon bg-primary">
                <i class="bi bi-person-plus-fill"></i>
              </div>
              <div class="timeline-content">
                <div class="timeline-label">Member Since</div>
                <div class="timeline-value">
                  <?= date('d M Y', strtotime($m['created_at'])) ?>
                </div>
                <div class="timeline-relative"></div>
              </div>
            </div>

            <?php if ($m['updated_at'] !== $m['created_at']): ?>
              <div class="timeline-item" data-timestamp="<?= $m['updated_at'] ?>">
                <div class="timeline-icon bg-secondary">
                  <i class="bi bi-pencil-square"></i>
                </div>
                <div class="timeline-content">
                  <div class="timeline-label">Last Updated</div>
                  <div class="timeline-value">
                    <?= date('d M Y, g:i A', strtotime($m['updated_at'])) ?>
                  </div>
                  <div class="timeline-relative"></div>
                </div>
              </div>
            <?php endif; ?>

          </div>
        </div>
      </div>

      <!-- Membership History -->
      <div class="card border-0 shadow-sm rounded-4 mb-4 card-hover">
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
            <div class="history-list">

              <?php foreach ($history as $h): ?>
                <div class="history-item">
                  <div class="history-badge">
                    <i class="bi bi-arrow-right"></i>
                  </div>
                  <div class="history-content">
                    <div class="history-change">
                      <span class="badge bg-light text-dark"><?= esc($h['old_type'] ?: 'None') ?></span>
                      <i class="bi bi-arrow-right mx-2 text-muted"></i>
                      <span class="badge bg-brand text-white"><?= esc($h['new_type']) ?></span>
                    </div>

                    <div class="history-meta">
                      <span class="history-date" data-timestamp="<?= $h['created_at'] ?>">
                        <?= date('d M Y, g:i A', strtotime($h['created_at'])) ?>
                      </span>

                      <?php if ($h['changed_by']): ?>
                        <span class="history-admin">
                          by Admin #<?= esc($h['changed_by']) ?>
                        </span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>

            </div>
          <?php else: ?>
            <div class="empty-state-small">
              <i class="bi bi-inbox"></i>
              <p>No membership changes recorded.</p>
            </div>
          <?php endif; ?>

        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card border-0 shadow-sm rounded-4 card-hover">
        <div class="card-body p-4">
          <h6 class="fw-bold text-brand mb-4">
            <i class="bi bi-lightning-charge-fill me-2"></i>Quick Actions
            <span class="badge bg-light text-muted ms-2 small">⌘K</span>
          </h6>

          <div class="d-grid gap-3">

            <?php if (($m['status'] ?? '') === 'active'): ?>
              <form method="post" action="<?= base_url('admin/membership/' . $m['id'] . '/disable') ?>"
                onsubmit="return confirm('Are you sure you want to disable this member? They will lose access immediately.');">
                <?= csrf_field() ?>
                <button class="btn btn-outline-danger w-100 btn-pill action-btn">
                  <i class="bi bi-slash-circle me-2"></i>Disable Member
                </button>
              </form>
            <?php else: ?>
              <form method="post" action="<?= base_url('admin/membership/' . $m['id'] . '/activate') ?>">
                <?= csrf_field() ?>
                <button class="btn btn-success w-100 btn-pill action-btn">
                  <i class="bi bi-check2-circle me-2"></i>Activate Member
                </button>
              </form>
            <?php endif; ?>

            <?php if (($m['status'] ?? '') === 'pending'): ?>
              <div class="divider-text">
                <span>Activation</span>
              </div>

              <form method="post" action="<?= base_url('admin/membership/' . $m['id'] . '/queue-activation') ?>"
                onsubmit="return confirm('Re-send activation email to this member?');">
                <?= csrf_field() ?>
                <button class="btn btn-warning w-100 btn-pill action-btn">
                  <i class="bi bi-envelope-arrow-up me-2"></i>
                  Re-send Activation Email
                </button>
              </form>
            <?php endif; ?>

            <div class="divider-text">
              <span>Communication</span>
            </div>

            <form method="post" action="<?= base_url('admin/membership/' . $m['id'] . '/resend') ?>">
              <?= csrf_field() ?>
              <button class="btn btn-outline-brand w-100 btn-pill action-btn">
                <i class="bi bi-envelope me-2"></i>Resend Verification
              </button>
            </form>

            <div class="divider-text">
              <span>Profile</span>
            </div>

            <a href="<?= base_url('admin/membership/' . $m['id'] . '/edit') ?>"
              class="btn btn-brand w-100 btn-pill action-btn">
              <i class="bi bi-pencil-square me-2"></i>Edit Profile
            </a>

          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Update Membership Type Modal -->
<div class="modal fade" id="membershipTypeModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form action="<?= base_url('admin/membership/' . $m['id'] . '/update-type') ?>" method="post" class="modal-content">
      <?= csrf_field() ?>

      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-award-fill me-2 text-brand"></i>
          Update Membership Type
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <div class="mb-3">
          <label class="form-label fw-semibold">Membership Type</label>

          <div class="membership-type-options">
            <label class="membership-option">
              <input type="radio" name="membership_type" value="Standard" <?= $type === 'Standard' ? 'checked' : '' ?>>
              <div class="option-card">
                <i class="bi bi-shield-check"></i>
                <span class="option-title">Standard</span>
                <span class="option-desc">Regular membership access</span>
              </div>
            </label>

            <label class="membership-option">
              <input type="radio" name="membership_type" value="Life" <?= $type === 'Life' ? 'checked' : '' ?>>
              <div class="option-card">
                <i class="bi bi-star-fill"></i>
                <span class="option-title">Life Member</span>
                <span class="option-desc">Lifetime membership benefits</span>
              </div>
            </label>
          </div>
        </div>

        <div class="alert alert-info border-0 small mb-0">
          <i class="bi bi-info-circle-fill me-1"></i>
          Changes take effect immediately upon saving.
        </div>

      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-brand rounded-pill px-4">
          <i class="bi bi-check2-circle me-1"></i>Save Changes
        </button>
      </div>

    </form>
  </div>
</div>

<!-- Toast Notification -->
<div class="toast-container position-fixed top-0 end-0 p-3">
  <div id="copyToast" class="toast" role="alert">
    <div class="toast-body bg-success text-white rounded-3 d-flex align-items-center gap-2">
      <i class="bi bi-check-circle-fill fs-5"></i>
      <span class="copy-toast-message">Copied to clipboard!</span>
    </div>
  </div>
</div>

<!-- ENHANCED STYLES -->
<style>
  /* Header Enhancements */
  .header-gradient {
    background: linear-gradient(135deg, var(--brand) 0%, #982d52 100%);
    position: relative;
    overflow: hidden;
  }

  .header-gradient::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: float 6s ease-in-out infinite;
  }

  @keyframes float {

    0%,
    100% {
      transform: translateY(0) rotate(0deg);
    }

    50% {
      transform: translateY(-20px) rotate(5deg);
    }
  }

  .avatar-circle {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
  }

  .avatar-circle:hover {
    transform: scale(1.05);
    border-color: rgba(255, 255, 255, 0.5);
  }

  .avatar-initials {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    letter-spacing: 1px;
  }

  .badge-status-active {
    background: #10b981 !important;
    color: white !important;
  }

  .badge-status-pending {
    background: #f59e0b !important;
    color: white !important;
  }

  .badge-status-inactive {
    background: #6b7280 !important;
    color: white !important;
  }

  .pulse-dot {
    font-size: 0.5rem;
    margin-right: 0.25rem;
    animation: pulse 2s ease-in-out infinite;
  }

  @keyframes pulse {

    0%,
    100% {
      opacity: 1;
    }

    50% {
      opacity: 0.5;
    }
  }

  /* Stats Dashboard */
  .stat-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
  }

  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  }

  .stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
  }

  .bg-gradient-primary {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
  }

  .bg-gradient-success {
    background: linear-gradient(135deg, #10b981, #059669);
  }

  .bg-gradient-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
  }

  .bg-gradient-info {
    background: linear-gradient(135deg, #06b6d4, #0891b2);
  }

  .bg-gradient-brand {
    background: linear-gradient(135deg, var(--brand), #982d52);
  }

  .stat-content {
    flex: 1;
  }

  .stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 0.25rem;
  }

  .stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
  }

  .stat-subtext {
    font-size: 0.75rem;
    color: #9ca3af;
    margin-top: 0.25rem;
  }

  /* Card Hover Effects */
  .card-hover {
    transition: all 0.3s ease;
  }

  .card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
  }

  /* Enhanced Info Items */
  .info-item {
    display: flex;
    gap: 1rem;
    padding: 1.25rem;
    background: #f8f9fa;
    border-radius: .875rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .info-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 3px;
    height: 100%;
    background: linear-gradient(135deg, var(--brand), #982d52);
    transform: scaleY(0);
    transition: transform 0.3s ease;
  }

  .info-item:hover::before {
    transform: scaleY(1);
  }

  .info-item:hover {
    background: #e9ecef;
    padding-left: 1.5rem;
  }

  .info-item.clickable {
    cursor: pointer;
  }

  .info-item.clickable:hover {
    background: #e0e7ff;
  }

  .copy-indicator {
    margin-left: auto;
    color: var(--brand);
    opacity: 0;
    transition: all 0.3s ease;
    font-size: 1.25rem;
  }

  .info-item.clickable:hover .copy-indicator {
    opacity: 0.7;
  }

  .info-item.copied .copy-indicator {
    opacity: 1;
    transform: scale(1.2);
  }

  .info-icon {
    width: 48px;
    height: 48px;
    border-radius: .875rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--brand), #982d52);
    color: white;
    font-size: 1.25rem;
    flex-shrink: 0;
    transition: all 0.3s ease;
  }

  .info-item:hover .info-icon {
    transform: rotate(5deg) scale(1.05);
  }

  .info-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6b7280;
    font-weight: 600;
    margin-bottom: 0.25rem;
  }

  .info-value {
    font-size: 0.95rem;
    color: #1f2937;
    font-weight: 500;
  }

  /* Family Cards */
  .family-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 1rem;
    padding: 1.25rem;
    transition: all 0.3s ease;
  }

  .family-card:hover {
    border-color: var(--brand);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
  }

  .family-card-header {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f3f4f6;
  }

  .family-avatar {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1rem;
    color: white;
    flex-shrink: 0;
    background: linear-gradient(135deg, #6366f1, #4f46e5);
  }

  .family-avatar-spouse {
    background: linear-gradient(135deg, #ec4899, #db2777);
  }

  .family-avatar-son {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
  }

  .family-avatar-daughter {
    background: linear-gradient(135deg, #06b6d4, #0891b2);
  }

  .family-avatar-mother {
    background: linear-gradient(135deg, #10b981, #059669);
  }

  .family-avatar-father {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
  }

  .family-avatar-grandparent {
    background: linear-gradient(135deg, #f59e0b, #d97706);
  }

  .family-avatar-sibling {
    background: linear-gradient(135deg, #14b8a6, #0d9488);
  }

  .family-info {
    flex: 1;
    min-width: 0;
  }

  .family-name {
    font-weight: 600;
    color: #1f2937;
    font-size: 1rem;
    margin-bottom: 0.375rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .family-card-body {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .family-detail {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #4b5563;
  }

  .family-detail i {
    color: var(--brand);
    flex-shrink: 0;
  }

  /* Relation Badges */
  .relation-badge {
    font-size: .75rem;
    border-radius: 999px;
    padding: .25rem .625rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    background: #f4f4f5;
    color: #444;
  }

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

  /* Membership Badge */
  .membership-badge-large {
    padding: 1.5rem;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
  }

  .membership-standard {
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    border: 2px solid #d1d5db;
  }

  .membership-life {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 2px solid #fbbf24;
    animation: glow 3s ease-in-out infinite;
  }

  @keyframes glow {

    0%,
    100% {
      box-shadow: 0 0 20px rgba(251, 191, 36, 0.3);
    }

    50% {
      box-shadow: 0 0 30px rgba(251, 191, 36, 0.5);
    }
  }

  .membership-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    flex-shrink: 0;
  }

  .membership-standard .membership-icon {
    background: linear-gradient(135deg, var(--brand), #982d52);
    color: white;
  }

  .membership-life .membership-icon {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
  }

  .membership-type {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
  }

  .membership-subtitle {
    font-size: 0.875rem;
    color: #6b7280;
  }

  .membership-confetti {
    position: absolute;
    right: 1rem;
    font-size: 2rem;
    animation: bounce 2s ease-in-out infinite;
  }

  @keyframes bounce {

    0%,
    100% {
      transform: translateY(0);
    }

    50% {
      transform: translateY(-10px);
    }
  }

  /* Timeline Enhancements */
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
    background: linear-gradient(180deg, var(--brand), transparent);
  }

  .timeline-item {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
  }

  .timeline-item:hover {
    transform: translateX(4px);
  }

  .timeline-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    position: relative;
    z-index: 1;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  }

  .timeline-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6b7280;
    font-weight: 600;
    margin-bottom: 0.125rem;
  }

  .timeline-value {
    font-size: 0.875rem;
    color: #1f2937;
    font-weight: 500;
  }

  .timeline-relative {
    font-size: 0.75rem;
    color: #9ca3af;
    font-style: italic;
    margin-top: 0.25rem;
  }

  /* History List */
  .history-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .history-item {
    display: flex;
    gap: 0.75rem;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 0.75rem;
    border-left: 3px solid var(--brand);
    transition: all 0.3s ease;
  }

  .history-item:hover {
    background: #f3f4f6;
    transform: translateX(4px);
  }

  .history-badge {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--brand), #982d52);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .history-change {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
  }

  .history-meta {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    font-size: 0.75rem;
    color: #6b7280;
  }

  /* Empty States */
  .empty-state {
    text-align: center;
    padding: 3rem 1.5rem;
  }

  .empty-state-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1rem;
    border-radius: 50%;
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #9ca3af;
  }

  .empty-state-title {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
  }

  .empty-state-text {
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 0;
  }

  .empty-state-small {
    text-align: center;
    padding: 2rem 1rem;
    color: #9ca3af;
  }

  .empty-state-small i {
    font-size: 2rem;
    display: block;
    margin-bottom: 0.5rem;
    opacity: 0.5;
  }

  .empty-state-small p {
    font-size: 0.875rem;
    margin: 0;
  }

  /* Button Enhancements */
  .btn-hover-lift {
    transition: all 0.3s ease;
  }

  .btn-hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }

  .btn-brand-light {
    background: rgba(var(--brand-rgb), 0.1);
    color: var(--brand);
    border: none;
  }

  .btn-brand-light:hover {
    background: rgba(var(--brand-rgb), 0.2);
    color: var(--brand);
  }

  .action-btn {
    transition: all 0.3s ease;
    font-weight: 500;
  }

  .action-btn:hover {
    transform: translateX(4px);
  }

  .divider-text {
    text-align: center;
    position: relative;
    margin: 1rem 0;
  }

  .divider-text span {
    background: white;
    padding: 0 1rem;
    color: #9ca3af;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    position: relative;
    z-index: 1;
  }

  .divider-text::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #e5e7eb;
  }

  /* Modal Enhancements */
  .membership-type-options {
    display: grid;
    gap: 1rem;
  }

  .membership-option {
    cursor: pointer;
    margin: 0;
  }

  .membership-option input[type="radio"] {
    display: none;
  }

  .option-card {
    padding: 1.25rem;
    border: 2px solid #e5e7eb;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
  }

  .option-card i {
    font-size: 1.5rem;
    color: var(--brand);
  }

  .option-title {
    font-weight: 600;
    color: #1f2937;
  }

  .option-desc {
    font-size: 0.875rem;
    color: #6b7280;
    margin-left: auto;
  }

  .membership-option input[type="radio"]:checked+.option-card {
    border-color: var(--brand);
    background: rgba(var(--brand-rgb), 0.05);
  }

  /* Animations */
  .alert-animated {
    animation: slideInDown 0.5s ease;
  }

  @keyframes slideInDown {
    from {
      transform: translateY(-100%);
      opacity: 0;
    }

    to {
      transform: translateY(0);
      opacity: 1;
    }
  }

  /* Toast */
  .toast-container {
    z-index: 9999;
  }

  #copyToast .toast-body {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }

  /* Responsive */
  @media (max-width: 768px) {
    .stat-value {
      font-size: 1.5rem;
    }

    .stat-icon {
      width: 48px;
      height: 48px;
      font-size: 1.25rem;
    }

    .family-card {
      margin-bottom: 1rem;
    }

    .info-item {
      flex-direction: column;
      gap: 0.75rem;
    }
  }

  /* Custom Scrollbar */
  ::-webkit-scrollbar {
    width: 8px;
    height: 8px;
  }

  ::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
  }

  ::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, var(--brand), #982d52);
    border-radius: 10px;
  }

  ::-webkit-scrollbar-thumb:hover {
    background: var(--brand);
  }
</style>

<!-- JAVASCRIPT ENHANCEMENTS -->
<script>
  document.addEventListener('DOMContentLoaded', function () {

    // Copy to Clipboard Function
    window.copyToClipboard = function (text, element) {
      navigator.clipboard.writeText(text).then(function () {
        // Add copied class
        element.classList.add('copied');

        // Show toast
        const toast = new bootstrap.Toast(document.getElementById('copyToast'));
        const message = element.querySelector('.info-label').textContent;
        document.querySelector('.copy-toast-message').textContent = `${message} copied!`;
        toast.show();

        // Remove copied class after animation
        setTimeout(() => {
          element.classList.remove('copied');
        }, 2000);
      });
    };

    // Calculate Relative Time
    function getRelativeTime(timestamp) {
      const now = new Date();
      const past = new Date(timestamp);
      const diffMs = now - past;
      const diffSecs = Math.floor(diffMs / 1000);
      const diffMins = Math.floor(diffSecs / 60);
      const diffHours = Math.floor(diffMins / 60);
      const diffDays = Math.floor(diffHours / 24);

      if (diffSecs < 60) return 'just now';
      if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
      if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
      if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
      if (diffDays < 30) return `${Math.floor(diffDays / 7)} week${Math.floor(diffDays / 7) > 1 ? 's' : ''} ago`;
      if (diffDays < 365) return `${Math.floor(diffDays / 30)} month${Math.floor(diffDays / 30) > 1 ? 's' : ''} ago`;
      return `${Math.floor(diffDays / 365)} year${Math.floor(diffDays / 365) > 1 ? 's' : ''} ago`;
    }

    // Update all relative times
    function updateRelativeTimes() {
      // Timeline items
      document.querySelectorAll('.timeline-item[data-timestamp]').forEach(item => {
        const timestamp = item.getAttribute('data-timestamp');
        const relativeEl = item.querySelector('.timeline-relative');
        if (relativeEl && timestamp) {
          relativeEl.textContent = getRelativeTime(timestamp);
        }
      });

      // Stat card relative time
      const statRelativeEl = document.querySelector('[data-timestamp] .relative-time');
      if (statRelativeEl) {
        const timestamp = statRelativeEl.closest('[data-timestamp]').getAttribute('data-timestamp');
        if (timestamp) {
          statRelativeEl.textContent = getRelativeTime(timestamp);
        }
      }
    }

    // Initial update and refresh every minute
    updateRelativeTimes();
    setInterval(updateRelativeTimes, 60000);

    // Smooth scroll for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#' && document.querySelector(href)) {
          e.preventDefault();
          document.querySelector(href).scrollIntoView({
            behavior: 'smooth'
          });
        }
      });
    });

    // Add keyboard shortcut hints (optional)
    document.addEventListener('keydown', function (e) {
      // Cmd/Ctrl + K to focus quick actions
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        const quickActions = document.querySelector('.card-body h6:contains("Quick Actions")');
        if (quickActions) {
          quickActions.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    });

    // Animate elements on scroll
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }
      });
    }, observerOptions);

    // Observe cards for animation
    document.querySelectorAll('.card, .stat-card').forEach(card => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
      observer.observe(card);
    });

    // Add tooltip functionality
    document.querySelectorAll('[data-tooltip]').forEach(element => {
      element.setAttribute('title', element.getAttribute('data-tooltip'));
    });

  });
</script>

<?= $this->endSection() ?>

