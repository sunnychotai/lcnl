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
<h2 class="fw-bold text-center mb-4">
  <i class="bi bi-envelope-heart-fill text-accent me-2"></i>
  Join the LCNL Mailing List
</h2>

<div class="text-center mx-auto mb-4" style="max-width:700px;">
  <p class="lead text-muted">
    Our <strong>Membership section</strong> is coming soon — but you don’t have to wait to get involved!  
    By joining the LCNL mailing list, you’ll be the first to hear about <em>events, festivals, announcements, and community news</em>.
  </p>
  <p class="small text-muted">
    Stay connected and celebrate with us — sign up today and be kept updated with all things <strong>Lohana Community of North London</strong>.
  </p>
</div>

<!-- Membership Options -->
<div class="row g-3 justify-content-center mb-4">
 

  <?php if (! session()->get('isMemberLoggedIn')): ?>
    <!-- New Registration (only if not logged in) -->
    <div class="col-md-5 col-lg-4">
      <div class="lcnl-card h-100 shadow-sm border-0 rounded-3" style="max-width:380px; margin:auto;">
        <div class="card-body text-center p-3">
          <div class="mb-2">
            <i class="bi bi-person-plus-fill text-success" style="font-size:2rem;"></i>
          </div>
          <h5 class="fw-bold mb-2">Register Now</h5>
          <p class="text-muted mb-3 small">
            Join the LCNL mailing in minutes.
          </p>
          <a href="<?= base_url('membership/register') ?>" class="btn btn-success rounded-pill px-3 py-1">
            <i class="bi bi-pencil-square me-1"></i> Register
          </a>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Member Access -->
  <div class="col-md-5 col-lg-4">
    <div class="lcnl-card h-100 shadow-sm border-0 rounded-3" style="max-width:380px; margin:auto;">
      <div class="card-body text-center p-3">

        <?php if (session()->get('isMemberLoggedIn')): ?>
          <!-- Logged-in: Show Dashboard card -->
          <div class="mb-2">
            <i class="bi bi-speedometer2 text-success" style="font-size:2rem;"></i>
          </div>
          <h5 class="fw-bold mb-2">Member Dashboard</h5>
          <p class="text-muted mb-3 small">
            Welcome back <?= esc(session()->get('member_name')) ?>!
          </p>
          <a href="<?= base_url('account/dashboard') ?>" class="btn btn-success rounded-pill px-3 py-1">
            <i class="bi bi-house-door-fill me-1"></i> Dashboard
          </a>

        <?php else: ?>
          <!-- Not logged in: Show Login card -->
          <div class="mb-2">
            <i class="bi bi-box-arrow-in-right text-primary" style="font-size:2rem;"></i>
          </div>
          <h5 class="fw-bold mb-2">Member Login</h5>
          <p class="text-muted mb-3 small">
            Already signed up? Log in to update your details.
          </p>
          <a href="<?= base_url('member/login') ?>" class="btn btn-primary rounded-pill px-3 py-1">
            <i class="bi bi-key-fill me-1"></i> Login
          </a>
        <?php endif; ?>

      </div>
    </div>
  </div>

</div>

<hr>

  <!-- Life Membership Benefits -->
  <div class="mb-5">
    <h2 class="fw-bold text-center mb-4">
      <i class="bi bi-trophy-fill text-accent me-2"></i> Life Membership Benefits - Coming Soon
    </h2>
    <div class="row g-4 justify-content-center membership-benefits">

      <div class="col-md-4">
        <div class="benefit-card text-center p-4">
          <i class="bi bi-person-check-fill mb-3 benefit-icon"></i>
          <h5 class="fw-bold">Voting Rights</h5>
          <p class="text-muted small">Have your say and receive invitations to the AGM.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="benefit-card text-center p-4">
          <i class="bi bi-calendar-event-fill mb-3 benefit-icon"></i>
          <h5 class="fw-bold">Priority Access</h5>
          <p class="text-muted small">Be the first to access tickets for all LCNL events.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="benefit-card text-center p-4">
          <i class="bi bi-journal-text mb-3 benefit-icon"></i>
          <h5 class="fw-bold">Annual Publications</h5>
          <p class="text-muted small">Receive our Diwali magazine and regular newsletters.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="benefit-card text-center p-4">
          <i class="bi bi-people-fill mb-3 benefit-icon"></i>
          <h5 class="fw-bold">Community Involvement</h5>
          <p class="text-muted small">Participate in social, religious, and cultural events.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="benefit-card text-center p-4">
          <i class="bi bi-heart-fill mb-3 benefit-icon"></i>
          <h5 class="fw-bold">Hands-On Role</h5>
          <p class="text-muted small">Help shape the future of our community through active involvement.</p>
        </div>
      </div>

    </div>
  </div>


  <!-- Contact / Updates -->
  <div class="text-center">
    <p class="text-muted small">
      If you have any questions or queries about membership, please contact us at 
      <a href="mailto:membership@lcnl.org">membership@lcnl.org</a>.
    </p>
  </div>

</div>

<?= $this->endSection() ?>
