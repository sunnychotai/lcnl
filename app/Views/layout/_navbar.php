<nav class="navbar navbar-expand-lg navbar-light bg-lcnl">
  <div class="container">
    <!-- Main nav toggler -->
    <button class="navbar-toggler d-flex d-lg-none align-items-center gap-2 px-3"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNav"
            aria-controls="mainNav"
            aria-expanded="false"
            aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
      <span class="navbar-toggler-text fw-semibold">Menu</span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-auto lcnl-nav">
        <li class="nav-item"><a class="nav-link fw-semibold" href="<?= base_url('/') ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold" href="<?= base_url('events') ?>">Events</a></li>

        <!-- Committees Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link fw-semibold dropdown-toggle" href="#" id="committeeDropdown" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            Committees
          </a>
          <ul class="dropdown-menu" aria-labelledby="committeeDropdown">
            <li><a class="dropdown-item" href="<?= base_url('/committee') ?>">Executive Committee</a></li>
            <li><a class="dropdown-item" href="<?= base_url('/mahila') ?>">Mahila Committee</a></li>
            <li><a class="dropdown-item" href="<?= base_url('/yls') ?>">Young Lohana Society</a></li>
            <li><a class="dropdown-item" href="<?= base_url('/youth') ?>">Youth Committee</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/lcf') ?>">Lohana Charitable Foundation</a></li>

            <!-- <li><a class="dropdown-item" href="">Senior Mens</a></li>
            <li><a class="dropdown-item" href="">Senior Ladies</a></li>
            <li><a class="dropdown-item" href="">Raghuvanshi Charitable Trust</a></li>
            <li><a class="dropdown-item" href="">Lohana Charitable Foundation</a></li> -->
          </ul>
        </li>

        <li class="nav-item"><a class="nav-link fw-semibold" href="<?= base_url('membership') ?>">Membership</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold" href="<?= base_url('bereavement') ?>">Bereavement</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold" href="<?= base_url('contact') ?>">Contact</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold" href="<?= base_url('aboutus') ?>">About Us</a></li>
      </ul>
    </div>
  </div>
</nav>

<?php if (session()->get('isAdminLoggedIn') && session()->get('admin_role') === 'ADMIN'): ?>
  <nav class="navbar navbar-expand-lg navbar-dark bg-darkblue py-2 shadow-sm">
    <div class="container-fluid">
      <!-- Admin nav toggler -->
      <button class="navbar-toggler d-flex d-lg-none align-items-center gap-2 px-3"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#adminNav"
              aria-controls="adminNav"
              aria-expanded="false"
              aria-label="Toggle admin navigation">
        <span class="navbar-toggler-icon"></span>
        <span class="navbar-toggler-text fw-semibold">Menu</span>
      </button>

      <div class="collapse navbar-collapse justify-content-center" id="adminNav">
        <ul class="navbar-nav gap-4">
          <li class="nav-item d-flex align-items-center">
            <span class="navbar-text text-light fw-semibold me-3">
              👋 Welcome, <?= esc(session()->get('firstname') ?? 'Admin') ?>
            </span>
          </li>

          <!-- System Section -->
<li class="nav-item">
  <a class="nav-link" href="<?= base_url('admin/system/dashboard') ?>">
    <i class="bi bi-speedometer2 me-1"></i> Dashboard
  </a>
</li>
<li class="nav-item">
  <a class="nav-link" href="<?= base_url('admin/system/users') ?>">
    <i class="bi bi-person-gear me-1"></i> Users
  </a>
</li>
<li class="nav-item">
  <a class="nav-link" href="<?= base_url('admin/system/emails') ?>">
    <i class="bi bi-envelope-fill me-1"></i> Emails
  </a>
</li>

<!-- Content Section -->
<li class="nav-item">
  <a class="nav-link" href="<?= base_url('admin/content/committee') ?>">
    <i class="bi bi-people-fill me-1"></i> Committees
  </a>
</li>
<li class="nav-item">
  <a class="nav-link" href="<?= base_url('admin/content/events') ?>">
    <i class="bi bi-calendar-event-fill me-1"></i> Events
  </a>
</li>
<li class="nav-item">
  <a class="nav-link" href="<?= base_url('admin/content/faqs') ?>">
    <i class="bi bi-question-circle-fill me-1"></i> FAQs
  </a>
</li>

<!-- Membership Section -->
<li class="nav-item">
  <a class="nav-link" href="<?= base_url('admin/membership/members') ?>">
    <i class="bi bi-person-lines-fill me-1"></i> Members
  </a>
</li>


          <!-- Logout -->
          <li class="nav-item">
            <a class="nav-link text-warning" href="<?= base_url('auth/logout') ?>">
              <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
<?php endif; ?>

<!-- Optional: toggler label script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  function wireLabel(toggler) {
    const label  = toggler?.querySelector('.navbar-toggler-text');
    const target = document.querySelector(toggler?.getAttribute('data-bs-target'));
    if (!toggler || !label || !target) return;
    target.addEventListener('shown.bs.collapse', () => label.textContent = 'Close');
    target.addEventListener('hidden.bs.collapse', () => label.textContent = 'Menu');
  }
  document.querySelectorAll('.navbar-toggler').forEach(wireLabel);
});
</script>

<style>
.navbar-toggler { min-height:44px; }
.navbar-toggler-text { letter-spacing:.02em; }
</style>
