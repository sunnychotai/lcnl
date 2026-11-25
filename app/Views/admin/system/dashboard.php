<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-ocean d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">Administration Dashboard</h1>
    <p class="lead fs-5 mb-0">LCNL Site Admin</p>
  </div>
</section>

<div class="container py-5">

  <!-- =======================
       Quick Stats
  ======================== -->
  <h4 class="fw-bold mb-3">Quick Stats</h4>
  <div class="row g-4 mb-5">
    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center h-100 bg-light">
        <div class="card-body">
          <i class="bi bi-person-check-fill fs-2 text-success mb-2"></i>
          <h6 class="fw-bold mb-0">Active Members</h6>
          <p class="display-6 fw-bold mb-0"><?= (int) ($stats['active_members'] ?? 0) ?></p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center h-100 bg-light">
        <div class="card-body">
          <i class="bi bi-hourglass-split fs-2 text-warning mb-2"></i>
          <h6 class="fw-bold mb-0">Pending Members</h6>
          <p class="display-6 fw-bold mb-0"><?= (int) ($stats['pending_members'] ?? 0) ?></p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center h-100 bg-light">
        <div class="card-body">
          <i class="bi bi-envelope-paper-fill fs-2 text-primary mb-2"></i>
          <h6 class="fw-bold mb-0">Emails Sent</h6>
          <p class="display-6 fw-bold mb-0"><?= (int) ($stats['emails_sent'] ?? 0) ?></p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center h-100 bg-light">
        <div class="card-body">
          <i class="bi bi-clock-history fs-2 text-dark mb-2"></i>
          <h6 class="fw-bold mb-0">Last Admin Login</h6>
          <p class="fw-bold mb-0 small"><?= esc($stats['last_login'] ?? '-') ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- =======================
       Admin Actions
  ======================== -->
  <?php if (canAccess('content', 'users', 'email_queue', 'membership')): ?>
    <h4 class="fw-bold mb-3">Admin Actions</h4>
    <div class="row g-4 mb-5">

    <?php if (canAccess('content')): ?>
      <!-- Committee -->
      <div class="col-md-3">
        <a href="<?= base_url('admin/content/committee') ?>" class="text-decoration-none">
          <div class="card shadow-sm h-100 text-center border-0 hover-card">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
              <i class="bi bi-people-fill text-brand fs-1 mb-3"></i>
              <h5 class="card-title text-dark mb-1">Committee</h5>
              <p class="text-muted small">Manage committee members</p>
            </div>
          </div>
        </a>
      </div>

      <!-- FAQs -->
      <div class="col-md-3">
        <a href="<?= base_url('admin/content/faqs') ?>" class="text-decoration-none">
          <div class="card shadow-sm h-100 text-center border-0 hover-card">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
              <i class="bi bi-question-circle-fill text-brand fs-1 mb-3"></i>
              <h5 class="card-title text-dark mb-1">FAQs</h5>
              <p class="text-muted small">Manage FAQs</p>
            </div>
          </div>
        </a>
      </div>
    <?php endif; ?>

    <?php if (canAccess('users')): ?>
      <!-- Users -->
      <div class="col-md-3">
        <a href="<?= base_url('admin/system/users') ?>" class="text-decoration-none">
          <div class="card shadow-sm h-100 text-center border-0 hover-card">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
              <i class="bi bi-person-fill-gear text-brand fs-1 mb-3"></i>
              <h5 class="card-title text-dark mb-1">Users</h5>
              <p class="text-muted small">Admin only: manage accounts</p>
            </div>
          </div>
        </a>
      </div>
    <?php endif; ?>

    <?php if (canAccess('email_queue')): ?>
      <!-- Emails -->
      <div class="col-md-3">
        <a href="<?= base_url('admin/system/emails') ?>" class="text-decoration-none">
          <div class="card shadow-sm h-100 text-center border-0 hover-card">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
              <i class="bi bi-envelope-fill text-brand fs-1 mb-3"></i>
              <h5 class="card-title text-dark mb-1">Emails</h5>
              <p class="text-muted small">View &amp; retry queued emails</p>
            </div>
          </div>
        </a>
      </div>
    <?php endif; ?>

    <?php if (canAccess('membership')): ?>
      <!-- Members -->
      <div class="col-md-3">
        <a href="<?= base_url('admin/membership') ?>" class="text-decoration-none">
          <div class="card shadow-sm h-100 text-center border-0 hover-card">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
              <i class="bi bi-person-badge-fill text-brand fs-1 mb-3"></i>
              <h5 class="card-title text-dark mb-1">Members</h5>
              <p class="text-muted small">Review &amp; approve members</p>
            </div>
          </div>
        </a>
      </div>
    <?php endif; ?>

    </div>
  <?php endif; ?>

  <!-- =======================
       Events & Campaigns
  ======================== -->
  <?php if (canAccess('content', 'chopda_pujan', 'christmas_mehfil', 'connexions', 'legacy50', 'ticketing_survey')): ?>
    <h4 class="fw-bold mb-3">Events &amp; Campaigns</h4>
    <div class="row g-4">

    <?php if (canAccess('chopda_pujan')): ?>
      <!-- Chopda Pujan 2026 -->
      <div class="col-md-3">
        <a href="<?= base_url('admin/chopda-pujan') ?>" class="text-decoration-none">
          <div class="card shadow-sm h-100 text-center border-0 hover-card">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
              <i class="bi bi-journal-check text-brand fs-1 mb-3"></i>
              <h5 class="card-title text-dark mb-1">Chopda Pujan 2026</h5>
              <p class="text-muted small">Registrations, payments &amp; CSV export</p>
            </div>
          </div>
        </a>
      </div>
    <?php endif; ?>

    <?php if (canAccess('christmas_mehfil')): ?>
      <!-- Christmas Mehfil 2026 -->
      <div class="col-md-3">
        <a href="<?= base_url('admin/christmas-mehfil') ?>" class="text-decoration-none">
          <div class="card shadow-sm h-100 text-center border-0 hover-card">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
              <i class="bi bi-ticket-perforated text-brand fs-1 mb-3"></i>
              <h5 class="card-title text-dark mb-1">Christmas Mehfil 2026</h5>
              <p class="text-muted small">Ticket bookings, payments &amp; CSV export</p>
            </div>
          </div>
        </a>
      </div>
    <?php endif; ?>

    <?php if (canAccess('connexions')): ?>
      <!-- Connexions Singles Mix & Mingle 2026 -->
      <div class="col-md-3">
        <a href="<?= base_url('admin/connexions') ?>" class="text-decoration-none">
          <div class="card shadow-sm h-100 text-center border-0 hover-card">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
              <i class="bi bi-heart text-brand fs-1 mb-3"></i>
              <h5 class="card-title text-dark mb-1">Connexions Mix &amp; Mingle</h5>
              <p class="text-muted small">Card bookings, attendees &amp; door list</p>
            </div>
          </div>
        </a>
      </div>
    <?php endif; ?>

    <?php if (canAccess('content')): ?>
      <!-- Golf Tournament 2026 -->
      <div class="col-md-3">
        <a href="<?= base_url('admin/content/golf') ?>" class="text-decoration-none">
          <div class="card shadow-sm h-100 text-center border-0 hover-card">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
              <i class="bi bi-flag-fill text-success fs-1 mb-3"></i>
              <h5 class="card-title text-dark mb-1">Golf Event 2026</h5>
              <p class="text-muted small">Registrations, stats &amp; CSV export</p>
            </div>
          </div>
        </a>
      </div>

      <!-- Events -->
      <div class="col-md-3">
        <a href="<?= base_url('admin/content/events') ?>" class="text-decoration-none">
          <div class="card shadow-sm h-100 text-center border-0 hover-card">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
              <i class="bi bi-calendar-event text-brand fs-1 mb-3"></i>
              <h5 class="card-title text-dark mb-1">Events</h5>
              <p class="text-muted small">Manage community events</p>
            </div>
          </div>
        </a>
      </div>

      <!-- Event Registrations -->
      <div class="col-md-3">
        <a href="<?= base_url('admin/content/events/event-registrations') ?>" class="text-decoration-none">
          <div class="card shadow-sm h-100 text-center border-0 hover-card">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
              <i class="bi bi-clipboard-data-fill text-brand fs-1 mb-3"></i>
              <h5 class="card-title text-dark mb-1">Event Registrations</h5>
              <p class="text-muted small">Summary by event + attendee counts</p>
            </div>
          </div>
        </a>
      </div>
    <?php endif; ?>

    <?php if (canAccess('legacy50')): ?>
      <!-- Legacy 50 pledges (pledges hold home addresses and Gift Aid declarations) -->
      <div class="col-md-3">
        <a href="<?= base_url('admin/legacy50') ?>" class="text-decoration-none">
          <div class="card shadow-sm h-100 text-center border-0 hover-card">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
              <i class="bi bi-heart-fill text-brand fs-1 mb-3"></i>
              <h5 class="card-title text-dark mb-1">Legacy 50 Pledges</h5>
              <p class="text-muted small">Donations, Gift Aid &amp; plaque exports</p>
            </div>
          </div>
        </a>
      </div>
    <?php endif; ?>

    <?php if (canAccess('ticketing_survey')): ?>
      <!-- Committee ticketing questionnaire (answers carry names) -->
      <div class="col-md-3">
        <a href="<?= base_url('admin/surveys/ticketing') ?>" class="text-decoration-none">
          <div class="card shadow-sm h-100 text-center border-0 hover-card">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
              <i class="bi bi-clipboard-data text-brand fs-1 mb-3"></i>
              <h5 class="card-title text-dark mb-1">Ticketing Questionnaire</h5>
              <p class="text-muted small">Committee feedback, priorities &amp; CSV export</p>
            </div>
          </div>
        </a>
      </div>
    <?php endif; ?>

    </div>
  <?php endif; ?>

</div>

<?= $this->endSection() ?>

