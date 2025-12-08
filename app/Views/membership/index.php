<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-2">
      <i class="bi bi-people-fill me-2"></i> LCNL Membership
    </h1>
    <p class="lead fs-5 mb-0">
      Join the Lohana Community of North London and be part of something bigger
    </p>
  </div>
</section>

<div class="container py-5">

  <?php
  $loggedIn = session()->get('isMemberLoggedIn');
  $memberName = session()->get('member_name');


  // SESSION
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



  // Badge colours
  $badgeClass = match ($status) {
    'Active' => 'bg-success',
    'Pending' => 'bg-warning text-dark',
    'Expired' => 'bg-secondary',
    'Cancelled' => 'bg-danger',
    default => 'bg-primary',
  };
  ?>


  <!-- ==========================================================
        NOT LOGGED IN — PUBLIC LANDING
       ========================================================== -->
  <?php if (!$loggedIn): ?>

    <!-- Primary CTA -->
    <div class="row justify-content-center mb-4">
      <div class="col-lg-8">
        <div class="cta-card lcnl-card shadow-lg border-0 rounded-4 overflow-hidden">
          <div class="card-body p-4 text-center">
            <div class="mb-3">
              <i class="bi bi-person-plus-fill text-brand" style="font-size: 3.5rem;"></i>
            </div>
            <h3 class="fw-bold mb-2">Ready to Join?</h3>
            <p class="text-muted mb-3">
              Register now to become a life member and unlock all the benefits of the LCNL community.
            </p>
            <a href="<?= base_url('membership/register') ?>" class="btn btn-brand btn-lg rounded-pill px-5 py-2 shadow">
              <i class="bi bi-pencil-square me-2"></i> Register Now
            </a>
            <p class="text-muted small mt-2 mb-0">
              <i class="bi bi-shield-check me-1"></i> Secure registration • Lifetime access • One-time payment
            </p>
          </div>
        </div>
      </div>
    </div>

  <?php else: ?>

    <!-- ==========================================================
     LOGGED IN — WELCOME + MEMBERSHIP STATUS (Unified Card)
     ========================================================== -->
    <div class="row justify-content-center mb-4">
      <div class="col-lg-8">

        <div class="lcnl-card shadow-sm border-0 rounded-4 p-4">

          <div class="d-flex align-items-center mb-3 gap-3">
            <i class="bi bi-check-circle-fill fs-1 text-success"></i>

            <div>
              <h4 class="fw-bold mb-1">
                Welcome back, <?= esc($memberName) ?>!
              </h4>

              <!-- MEMBER ID BADGE -->
              <span class="badge bg-brand text-white px-3 py-2 rounded-pill fs-6 mt-1">
                <i class="bi bi-person-vcard me-1"></i>
                Membership ID: <strong>LCNL<?= esc(session()->get('member_id')) ?></strong>
              </span>
            </div>
          </div>

          <!-- Divider -->
          <hr class="my-3" style="border-top: 2px solid rgba(0,0,0,0.08);">

          <!-- Membership Status Section -->
          <div class="d-flex flex-wrap justify-content-between align-items-center">

            <div class="mb-2">
              <p class="mb-1">
                <strong>Membership Type:</strong> <?= esc($type) ?>
              </p>
              <p class="mb-0">
                <strong>Status:</strong>
                <span class="badge <?= $badgeClass ?>"><?= esc($status) ?></span>
              </p>
            </div>

            <div class="text-end mt-3 mt-md-0">
              <a href="<?= base_url('account/dashboard') ?>" class="btn btn-success btn-sm rounded-pill px-4">
                <i class="bi bi-speedometer2 me-2"></i> Membership Dashboard
              </a>
            </div>

          </div>

        </div>

      </div>
    </div>


  <?php endif; ?>


  <!-- ==========================================================
      MAIN VALUE PROPOSITION (Dynamic based on membership type)
     ========================================================== -->
  <?php if (!$loggedIn): ?>

    <!-- Public View -->
    <div class="text-center mb-4">
      <h2 class="fw-bold mb-3">
        <i class="bi bi-star-fill text-accent me-2"></i>
        Become a Life Member Today
      </h2>
      <p class="lead text-muted mb-2" style="max-width: 700px; margin: 0 auto;">
        Join our vibrant community and enjoy <strong>lifetime benefits</strong> for a one-time fee of just
        <span class="text-brand fw-bold fs-4">£75</span>
      </p>
      <p class="text-muted small">
        Connect with fellow Lohanas, participate in cultural events, and help shape the future of our community.
      </p>
    </div>

  <?php elseif ($type === 'Standard'): ?>

    <!-- Standard Members Notice -->
    <div class="row justify-content-center mb-4">
      <div class="col-lg-8">

        <div class="lcnl-card shadow-sm border-0 rounded-4 p-4 text-center">

          <!-- Icon Header -->
          <div class="mb-2">
            <i class="bi bi-star-fill text-accent" style="font-size: 2.2rem;"></i>
          </div>

          <p class="lead text-muted mb-2" style="max-width: 700px; margin: 0 auto;">
            Life Membership upgrade for your registration is coming soon.
          </p>

          <p class="text-muted mb-3" style="max-width: 650px; margin: 0 auto;">
            You'll be able to become a <strong>LIFE member</strong> soon.
          </p>

          <!-- Soft Gold Divider -->
          <hr style="width:160px; margin:auto; border:0; border-top:2px solid var(--accent1); opacity:0.85;">

          <!-- Enquiries -->
          <p class="text-muted small mt-3">
            <i class="bi bi-envelope-open text-accent me-1"></i>
            Any enquiries? Email
            <a href="mailto:membership@lcnl.org" class="text-decoration-none text-brand fw-semibold">
              membership@lcnl.org
            </a>
          </p>

          <!-- Disabled Upgrade Button -->
          <button class="btn btn-accent btn-sm rounded-pill px-4 mt-2 opacity-75" style="cursor:not-allowed;" disabled>
            <i class="bi bi-arrow-up-circle me-1"></i>
            Upgrade to Life Membership (Coming Soon)
          </button>

        </div>

      </div>
    </div>


  <?php endif; ?>







  <hr class="my-4">

  <!-- Life Membership Benefits -->
  <div class="mb-4">
    <div class="text-center mb-4">
      <h2 class="fw-bold mb-2">
        <i class="bi bi-trophy-fill text-accent me-2"></i> Life Membership Benefits
      </h2>
      <p class="text-muted">Everything you get with LCNL Life Membership</p>
    </div>

    <div class="row g-3 justify-content-center">

      <div class="col-md-6 col-lg-4">
        <div class="benefit-card lcnl-card shadow-sm border-0 rounded-4 h-100">
          <div class="card-body text-center p-3">
            <div class="benefit-icon-wrapper mb-2">
              <i class="bi bi-person-check-fill benefit-icon"></i>
            </div>
            <h5 class="fw-bold mb-2">Voting Rights</h5>
            <p class="text-muted small mb-0">Have your say in community decisions and receive invitations to attend our
              Annual General Meeting.</p>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="benefit-card lcnl-card shadow-sm border-0 rounded-4 h-100">
          <div class="card-body text-center p-3">
            <div class="benefit-icon-wrapper mb-2">
              <i class="bi bi-calendar-event-fill benefit-icon"></i>
            </div>
            <h5 class="fw-bold mb-2">Priority Access</h5>
            <p class="text-muted small mb-0">Be the first to know about and access tickets for all LCNL events and
              celebrations.</p>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="benefit-card lcnl-card shadow-sm border-0 rounded-4 h-100">
          <div class="card-body text-center p-3">
            <div class="benefit-icon-wrapper mb-2">
              <i class="bi bi-journal-text benefit-icon"></i>
            </div>
            <h5 class="fw-bold mb-2">Annual Publications</h5>
            <p class="text-muted small mb-0">Receive our special Diwali magazine and stay updated with regular
              newsletters.</p>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="benefit-card lcnl-card shadow-sm border-0 rounded-4 h-100">
          <div class="card-body text-center p-3">
            <div class="benefit-icon-wrapper mb-2">
              <i class="bi bi-people-fill benefit-icon"></i>
            </div>
            <h5 class="fw-bold mb-2">Community Events</h5>
            <p class="text-muted small mb-0">Full participation in social, religious, and cultural events throughout the
              year.</p>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="benefit-card lcnl-card shadow-sm border-0 rounded-4 h-100">
          <div class="card-body text-center p-3">
            <div class="benefit-icon-wrapper mb-2">
              <i class="bi bi-heart-fill benefit-icon"></i>
            </div>
            <h5 class="fw-bold mb-2">Active Involvement</h5>
            <p class="text-muted small mb-0">Help shape the future of our community through active participation and
              engagement.</p>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="benefit-card lcnl-card shadow-sm border-0 rounded-4 h-100">
          <div class="card-body text-center p-3">
            <div class="benefit-icon-wrapper mb-2">
              <i class="bi bi-infinity benefit-icon"></i>
            </div>
            <h5 class="fw-bold mb-2">Lifetime Access</h5>
            <p class="text-muted small mb-0">One payment, lifetime benefits. No renewal fees or ongoing costs.</p>
          </div>
        </div>
      </div>

    </div>
  </div>

  <?php if (!session()->get('isMemberLoggedIn')): ?>
    <!-- Secondary CTA -->
    <div class="text-center my-4 py-3">
      <h3 class="fw-bold mb-2">Join Our Growing Community</h3>
      <p class="text-muted mb-3">Become a life member for just £75 and enjoy all these benefits</p>
      <a href="<?= base_url('membership/register') ?>" class="btn btn-brand btn-lg rounded-pill px-5 shadow-sm">
        <i class="bi bi-pencil-square me-2"></i> Register Now
      </a>
    </div>
  <?php endif; ?>

  <hr class="my-4">

  <!-- Member Login / Help / Contact -->
  <div class="row g-3 justify-content-center">

    <?php if (!session()->get('isMemberLoggedIn')): ?>
      <!-- Login Card for Existing Members -->
      <div class="col-md-6 col-lg-4">
        <div class="lcnl-card shadow-sm border-0 rounded-4 h-100">
          <div class="card-body text-center p-3">
            <i class="bi bi-box-arrow-in-right text-primary mb-2" style="font-size: 2.5rem;"></i>
            <h5 class="fw-bold mb-2">Already a Member?</h5>
            <p class="text-muted small mb-3">Log in to access your account and manage your membership details.</p>
            <a href="<?= base_url('membership/login') ?>" class="btn btn-outline-primary rounded-pill px-4">
              <i class="bi bi-key-fill me-2"></i> Member Login
            </a>
          </div>
        </div>
      </div>

      <!-- Existing Member - No Login Details -->
      <div class="col-md-6 col-lg-4">
        <div class="lcnl-card shadow-sm border-0 rounded-4 h-100 border-warning" style="border-width: 2px !important;">
          <div class="card-body text-center p-3">
            <i class="bi bi-question-circle-fill text-warning mb-2" style="font-size: 2.5rem;"></i>
            <h5 class="fw-bold mb-2">Don't Know Your Login?</h5>
            <p class="text-muted small mb-3">
              If you're an existing member and don't know your password, click below to reset it.
            </p>
            <a href="<?= base_url('membership/forgot') ?>" class="btn btn-outline-warning rounded-pill px-4 mb-2">
              <i class="bi bi-arrow-clockwise me-2"></i> Forgot Password
            </a>
            <hr class="my-2">
            <p class="text-muted small mb-2">
              <strong>Email not registered?</strong><br>
              Contact our membership team:
            </p>
            <a href="mailto:membership@lcnl.org" class="small text-decoration-none">
              <i class="bi bi-envelope me-1"></i> membership@lcnl.org
            </a>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Contact Card -->
    <div class="col-md-6 col-lg-4">
      <div class="lcnl-card shadow-sm border-0 rounded-4 h-100">
        <div class="card-body text-center p-3">
          <i class="bi bi-envelope-fill text-accent mb-2" style="font-size: 2.5rem;"></i>
          <h5 class="fw-bold mb-2">Have Questions?</h5>
          <p class="text-muted small mb-3">If you have any questions about membership, we're here to help.</p>
          <a href="mailto:membership@lcnl.org" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-envelope me-2"></i> Contact Us
          </a>
        </div>
      </div>
    </div>

  </div>

</div>

<?= $this->endSection() ?>

<style>
  /* CTA Card */
  .cta-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: 3px solid var(--bs-primary) !important;
    transition: transform .3s ease, box-shadow .3s ease;
  }

  .cta-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.15) !important;
  }

  /* Benefit Cards */
  .benefit-card {
    transition: transform .3s ease, box-shadow .3s ease;
    border-top: 4px solid var(--bs-primary);
  }

  .benefit-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12) !important;
  }

  .benefit-icon-wrapper {
    width: 70px;
    height: 70px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.1) 0%, rgba(var(--bs-primary-rgb), 0.05) 100%);
    border-radius: 50%;
  }

  .benefit-icon {
    font-size: 2.2rem;
    color: var(--bs-primary);
  }

  /* Warning border for migration card */
  .border-warning {
    border-color: #ffc107 !important;
  }

  /* Price Highlight */
  .text-brand {
    color: var(--bs-primary) !important;
  }

  /* Button Enhancements */
  .btn-brand {
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: all .3s ease;
  }

  .btn-brand:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .cta-card .card-body {
      padding: 2rem !important;
    }

    .benefit-icon-wrapper {
      width: 60px;
      height: 60px;
    }

    .benefit-icon {
      font-size: 2rem;
    }

    .btn-lg {
      font-size: 1rem;
      padding: 0.75rem 2rem !important;
    }
  }

  /* Alert styling */
  .alert-success {
    background: linear-gradient(135deg, #d1e7dd 0%, #f8f9fa 100%);
  }

  /* Smooth scrolling for internal links */
  html {
    scroll-behavior: smooth;
  }
</style>
