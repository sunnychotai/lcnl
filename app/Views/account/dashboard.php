<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$memberId = session()->get('member_id');

// MODELS
$memberModel = new \App\Models\MemberModel();
$membershipModel = new \App\Models\MembershipModel();

// 1️⃣ Get STATUS from MEMBERS table
$memberRow = $memberModel->find($memberId);
$statusRaw = $memberRow['status'] ?? 'active';
$status = ucfirst(strtolower($statusRaw));

// 2️⃣ Get MEMBERSHIP TYPE from MEMBERSHIPS table (latest record)
$membershipRow = $membershipModel
  ->where('member_id', $memberId)
  ->orderBy('id', 'DESC')
  ->first();

$typeRaw = $membershipRow['membership_type'] ?? 'Standard';
$type = ucfirst(strtolower($typeRaw));
?>


<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center" style="min-height: 200px;">
  <div class="container position-relative">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1 class="fw-bold display-5 mb-3 text-white">
          Welcome back, <?= esc($memberName) ?> 👋
        </h1>
        <?php if (!empty($events)): ?>
          <div
            class="d-inline-flex align-items-center bg-white bg-opacity-25 backdrop-blur rounded-pill px-4 py-2 text-white">
            <i class="bi bi-calendar-event-fill me-2 text-accent"></i>
            <span class="fw-semibold">Next Event:</span>
            <span class="ms-2"><?= esc($events[0]['title']) ?></span>
            <span class="mx-2">•</span>
            <span><?= date('d M Y', strtotime($events[0]['event_date'])) ?></span>
          </div>
        <?php endif; ?>
      </div>
      <!-- Replace the progress circle section in the hero with this: -->
      <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
        <?php
        $totalTasks = count($tasks['todo']) + count($tasks['done']);
        $doneTasks = count($tasks['done']);
        $progress = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;
        ?>
        <div class="d-inline-block bg-white rounded-4 shadow-lg p-4">
          <div class="d-flex align-items-center gap-3">
            <div class="position-relative">
              <svg width="80" height="80">
                <circle cx="40" cy="40" r="32" fill="none" stroke="#e9ecef" stroke-width="6"></circle>
                <circle cx="40" cy="40" r="32" fill="none" stroke="var(--brand)" stroke-width="6"
                  stroke-dasharray="<?= $progress * 2.01 ?> 201" stroke-linecap="round" transform="rotate(-90 40 40)">
                </circle>
              </svg>
              <div class="position-absolute top-50 start-50 translate-middle">
                <div class="fw-bold text-brand" style="font-size: 1.25rem; line-height: 1;"><?= $progress ?>%</div>
              </div>
            </div>
            <div class="text-start">
              <div class="fw-bold text-brand">Profile Complete</div>
              <div class="small text-muted"><?= $doneTasks ?> of <?= $totalTasks ?> tasks</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</section>
<div class="container py-4">
  <!-- Welcome + Member ID -->
  <div class="row justify-content-center my-4">
    <div class="col-lg-12">
      <div
        class="lcnl-card shadow-sm border-0 rounded-4 p-4 d-flex align-items-center justify-content-between flex-wrap">

        <div class="d-flex align-items-center gap-3 mb-3 mb-md-0">
          <i class="bi bi-person-check-fill fs-1 text-brand"></i>
          <div>


            <!-- LCNL Membership ID Badge -->
            <span class="badge bg-brand text-white px-3 py-2 rounded-pill fs-6">
              <i class="bi bi-person-vcard me-1"></i>
              Member ID: <strong>LCNL<?= esc(session()->get('member_id')) ?></strong>
            </span>
          </div>
        </div>

      </div>
    </div>
  </div>




  <!-- Flash messages -->
  <?php if ($msg = session()->getFlashdata('message')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4">
      <i class="bi bi-check-circle-fill me-2"></i><?= esc($msg) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if ($err = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4">
      <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc($err) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Quick Actions Grid -->
  <div class="row g-3 mb-4">
    <!-- My Profile Card -->
    <div class="col-md-6">
      <div class="card border-0 shadow-sm rounded-4 h-100 lcnl-card-hover"
        style="background: linear-gradient(135deg, var(--brand) 0%, #982d52 100%);">
        <div class="card-body p-4 text-white">
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="rounded-circle bg-white bg-opacity-25 p-3">
              <i class="bi bi-person-circle fs-2"></i>
            </div>
            <div>
              <h4 class="fw-bold mb-1">My Profile</h4>
              <p class="mb-0 small opacity-75">Manage your information</p>
            </div>
          </div>
          <a href="<?= route_to('account.profile.edit') ?>" class="btn btn-light btn-pill px-4 w-100">
            <i class="bi bi-pencil-square me-2"></i>Edit Profile
          </a>
        </div>
      </div>
    </div>

    <!-- Family Members Card -->
    <div class="col-md-6">
      <div class="card border-0 shadow-sm rounded-4 h-100 lcnl-card-hover"
        style="background: linear-gradient(135deg, #0a66c2 0%, #084d92 100%);">
        <div class="card-body p-4 text-white">
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="rounded-circle bg-white bg-opacity-25 p-3">
              <i class="bi bi-people-fill fs-2"></i>
            </div>
            <div>
              <h4 class="fw-bold mb-1">Family Members</h4>
              <p class="mb-0 small opacity-75">Manage your dependents</p>
            </div>
          </div>
          <a href="<?= route_to('account.family') ?>" class="btn btn-light btn-pill px-4 w-100">
            <i class="bi bi-arrow-right-circle me-2"></i>Manage Family
          </a>
        </div>
      </div>
    </div>

    <!-- Membership Type Card -->
    <div class="col-md-12">
      <div class="card border-0 shadow-sm rounded-4 lcnl-card-hover"
        style="background: linear-gradient(135deg, #d4af37 0%, #b8902c 100%);">
        <div class="card-body p-4 text-white">

          <!-- Header -->
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="rounded-circle bg-white bg-opacity-25 p-3">
              <i class="bi bi-award-fill fs-2 text-white"></i>
            </div>
            <div>
              <h4 class="fw-bold mb-1 text-white">Membership</h4>
              <p class="mb-0 small opacity-75">Your LCNL membership status</p>
            </div>
          </div>

          <?php if ($type === 'Life'): ?>
            <!-- LIFE MEMBER -->
            <div class="p-3 rounded-3 bg-white bg-opacity-10 mb-3 border-start border-accent1"
              style="border-width: 4px !important;">
              <p class="mb-0">
                <i class="bi bi-patch-check-fill me-1 text-white"></i>
                You are a <strong>LIFE Member</strong>.
              </p>
            </div>

            <button class="btn btn-light btn-pill px-4 w-100 fw-semibold text-brand" disabled>
              <i class="bi bi-award me-2 text-brand"></i>
              Life Membership Active
            </button>

          <?php else: ?>

            <?php if (strtoupper($membershipType ?? 'Standard') === 'STANDARD'): ?>
              <div class="card border-warning shadow-sm mb-4">
                <div class="card-body">
                  <h5 class="mb-1">Upgrade to Life Membership</h5>
                  <p class="text-muted mb-3">
                    One-off £75 payment • Lifetime membership
                  </p>

                  <form method="post" action="<?= route_to('account.membership.upgrade.checkout') ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-warning">
                      <i class="bi bi-credit-card me-1"></i>
                      Upgrade now
                    </button>
                  </form>
                </div>
              </div>
            <?php endif; ?>



            <!-- STANDARD MEMBER -->
            <div class="p-3 rounded-3 bg-white bg-opacity-10 mb-3 border-start border-accent1"
              style="border-width: 4px !important;">
              <p class="mb-0">
                <i class="bi bi-info-circle-fill me-1 text-white"></i>
                You currently have a <strong>Standard Membership</strong>.
              </p>
              <p class="mb-0 small opacity-75 mt-1">
                To upgrade to <strong>LIFE Membership (£75)</strong>, please make a bank transfer using the details below.
              </p>

              <div class="mt-3 small text-white">
                <div><strong>Bank:</strong> Lohana Community North London</div>
                <div><strong>Sort Code:</strong> 40-23-13</div>
                <div><strong>Account No:</strong> 21497995</div>
                <div class="mt-2">
                  <strong>Reference:</strong> MEMBERSHIP-LCNL<?= esc(session()->get('member_id')) ?>
                </div>
              </div>
            </div>

            <button class="btn btn-outline-light btn-pill px-4 w-100 opacity-75 fw-semibold" disabled
              style="cursor:not-allowed;">
              <i class="bi bi-bank me-2"></i>
              Pay £75 to Upgrade to Life Membership
            </button>

          <?php endif; ?>

        </div>
      </div>
    </div>


  </div>

  <!-- OUTSTANDING TASKS -->
  <?php if (!empty($tasks['todo'])): ?>
    <div class="mb-5">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
          <h3 class="fw-bold text-brand mb-1">
            <i class="bi bi-exclamation-circle-fill me-2 text-warning"></i>Action Required
          </h3>
          <p class="text-muted small mb-0">Complete these tasks to finish your profile setup</p>
        </div>
        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
          <?= count($tasks['todo']) ?> pending
        </span>
      </div>

      <div class="row g-3">
        <?php foreach ($tasks['todo'] as $t): ?>
          <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 lcnl-card-hover border-start border-warning border-4">
              <div class="card-body p-4">
                <div class="d-flex gap-3">
                  <div class="flex-shrink-0">
                    <div class="rounded-3 bg-warning bg-opacity-10 p-3 d-flex align-items-center justify-content-center"
                      style="width: 60px; height: 60px;">
                      <i class="bi <?= esc($t['icon']) ?> fs-3 text-warning"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <h5 class="fw-bold mb-2"><?= esc($t['title']) ?></h5>
                    <p class="text-muted small mb-3"><?= esc($t['desc']) ?></p>
                    <a href="<?= esc($t['url']) ?>" class="btn btn-brand btn-pill btn-sm">
                      Complete Now <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php else: ?>
    <div class="mb-5">
      <div class="card border-0 shadow-sm rounded-4"
        style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);">
        <div class="card-body p-4">
          <div class="d-flex align-items-center gap-4">
            <div class="rounded-circle bg-success p-4">
              <i class="bi bi-check-circle-fill fs-1 text-white"></i>
            </div>
            <div>
              <h4 class="fw-bold text-success mb-2">All Set! 🎉</h4>
              <p class="mb-0 text-dark">You've completed all required tasks. Your profile is looking great!</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- COMPLETED TASKS (Compact) -->
  <?php if (!empty($tasks['done'])): ?>
    <div class="mb-5">
      <h5 class="fw-bold text-brand mb-3">
        <i class="bi bi-check2-all me-2 text-success"></i>Completed Tasks
      </h5>
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-3">
          <div class="row g-2">
            <?php foreach ($tasks['done'] as $t): ?>
              <div class="col-md-6 col-lg-4">
                <a href="<?= esc($t['url']) ?>" class="text-decoration-none">
                  <div class="d-flex align-items-center gap-2 p-3 rounded-3 bg-light hover-bg-success-subtle transition">
                    <i class="bi <?= esc($t['icon']) ?> text-success"></i>
                    <span class="small fw-semibold text-dark flex-grow-1"><?= esc($t['title']) ?></span>
                    <i class="bi bi-check-circle-fill text-success"></i>
                  </div>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- UPCOMING EVENTS -->
  <div class="mb-5">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <div>
        <h3 class="fw-bold text-brand mb-1">
          <i class="bi bi-calendar-event-fill me-2"></i>Upcoming Events
        </h3>
        <p class="text-muted small mb-0">Don't miss out on these community gatherings</p>
      </div>
      <a href="<?= base_url('events') ?>" class="btn btn-outline-brand btn-pill btn-sm">
        View All <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>

    <?php if (!empty($events)): ?>
      <div class="row g-3">
        <?php foreach (array_slice($events, 0, 3) as $idx => $ev): ?>
          <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden lcnl-card-hover">
              <!-- Event Date Badge -->
              <div class="position-relative"
                style="background: linear-gradient(135deg, var(--brand) 0%, #982d52 100%); height: 100px;">
                <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
                  <div class="fw-bold" style="font-size: 2rem; line-height: 1;">
                    <?= date('d', strtotime($ev['event_date'])) ?>
                  </div>
                  <div class="small fw-semibold">
                    <?= date('M Y', strtotime($ev['event_date'])) ?>
                  </div>
                </div>
              </div>

              <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><?= esc($ev['title']) ?></h5>
                <div class="mb-3">
                  <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                    <i class="bi bi-clock-fill text-brand"></i>
                    <span><?= date('g:i A', strtotime($ev['event_date'])) ?></span>
                  </div>
                  <div class="d-flex align-items-center gap-2 text-muted small">
                    <i class="bi bi-geo-alt-fill text-brand"></i>
                    <span><?= esc($ev['location'] ?? 'LCNL Hall') ?></span>
                  </div>
                </div>
                <a href="<?= base_url('events/' . $ev['id']) ?>" class="btn btn-brand btn-pill btn-sm w-100">
                  View Details <i class="bi bi-arrow-right ms-1"></i>
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="card border-0 shadow-sm rounded-4 bg-light">
        <div class="card-body p-4 text-center">
          <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
          <h5 class="fw-bold mt-3 mb-2">No Upcoming Events</h5>
          <p class="text-muted mb-0">Check back soon for new community events!</p>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<style>
  .lcnl-card-hover {
    transition: all 0.3s ease;
  }

  .lcnl-card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
  }

  .hover-bg-success-subtle {
    transition: all 0.2s ease;
  }

  .hover-bg-success-subtle:hover {
    background-color: #d4edda !important;
  }

  .backdrop-blur {
    backdrop-filter: blur(10px);
  }

  .transition {
    transition: all 0.2s ease;
  }

  @media (max-width: 768px) {
    .display-5 {
      font-size: 1.75rem;
    }
  }
</style>

<?= $this->endSection() ?>

