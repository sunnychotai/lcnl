<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-4">
    <h1 class="fw-bold display-5 mb-2">
      <i class="bi bi-people-fill me-2"></i> LCNL Membership
    </h1>
    <p class="lead fs-5 mb-0">
      Join the Lohana Community of North London and be part of something bigger
    </p>
  </div>
</section>

<div class="container py-5">

  <!-- Membership Options -->
  <div class="row g-4 justify-content-center mb-5">

    <!-- New Registration -->
    <div class="col-md-6 col-lg-5">
      <div class="card h-100 shadow-lg border-0 rounded-4">
        <div class="card-body text-center p-4">
          <div class="mb-3">
            <i class="bi bi-person-plus-fill text-success" style="font-size:3rem;"></i>
          </div>
          <h3 class="fw-bold mb-3">Register Now!</h3>
          <p class="text-muted mb-4">
            Create your LCNL membership in minutes. Enter your details, confirm your email, 
            and start enjoying the benefits of being part of our community.
          </p>
          <a href="<?= base_url('membership/register') ?>" class="btn btn-success btn-lg rounded-pill px-4">
            <i class="bi bi-pencil-square me-2"></i> Register Now
          </a>
        </div>
      </div>
    </div>

    <!-- Existing Member Login -->
    <div class="col-md-6 col-lg-5">
      <div class="card h-100 shadow-lg border-0 rounded-4">
        <div class="card-body text-center p-4">
          <div class="mb-3">
            <i class="bi bi-box-arrow-in-right text-primary" style="font-size:3rem;"></i>
          </div>
          <h3 class="fw-bold mb-3">Member Login</h3>
          <p class="text-muted mb-4">
            Already a member? Log in to update your details, link family members, 
            and access exclusive LCNL content and event information.
          </p>
          <a href="<?= base_url('login') ?>" class="btn btn-primary btn-lg rounded-pill px-4">
            <i class="bi bi-key-fill me-2"></i> Login
          </a>
        </div>
      </div>
    </div>

  </div>

  <!-- Life Membership Benefits -->
  <div class="mb-5">
    <h2 class="fw-bold text-center mb-4">
      <i class="bi bi-trophy-fill text-accent me-2"></i> Life Membership Benefits
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

  <!-- Family Enrolment -->
  <div class="mb-5">
    <h2 class="fw-bold text-center mb-4">
      <i class="bi bi-people-heart-fill text-accent me-2"></i> Together as a Family
    </h2>

    <div class="row justify-content-center">
      <!-- Join LCNL (full width on desktop) -->
      <div class="col-12 col-lg-10">
        <div class="card h-100 shadow-lg border-0 rounded-4">
          <div class="card-body text-center p-4">
            <div class="mb-3">
              <i class="bi bi-people-fill text-brand" style="font-size:3rem;"></i>
            </div>
            <h3 class="fw-bold mb-3">Join LCNL</h3>
            <p class="text-muted mb-4">
              Adults can register with LCNL to access events, contribute to community programmes, 
              and be part of one of the largest Lohana groups outside India.  
              We also encourage households to enrol <strong>all family members aged 18 and under</strong> 
              into YLS to build the next generation of our community.
            </p>
            <a href="<?= base_url('membership/register') ?>" class="btn btn-brand btn-lg rounded-pill px-4 fw-bold">
              <i class="bi bi-people-fill me-2"></i> Join LCNL
            </a>
          </div>
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
