<?php
/**
 * Active-state helper.
 *
 * Matches the current path exactly, or as a parent of it, so /events/56 still
 * highlights Events. Home is matched exactly only — every path is "under" '/'.
 * Used for both .active (visual) and aria-current="page" (assistive).
 */
$currentPath = trim(uri_string(), '/');

$navIsActive = static function (string ...$paths) use ($currentPath): bool {
  foreach ($paths as $path) {
    $path = trim($path, '/');

    if ($path === '') {
      if ($currentPath === '') {
        return true;
      }
      continue;
    }

    if ($currentPath === $path || str_starts_with($currentPath, $path . '/')) {
      return true;
    }
  }

  return false;
};
?>
<nav class="navbar navbar-expand-lg navbar-light bg-lcnl">
  <div class="container">
    <!-- Main nav toggler -->
    <button class="navbar-toggler d-flex d-lg-none align-items-center gap-2 px-3" type="button"
      data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false"
      aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
      <span class="navbar-toggler-text fw-semibold">Menu</span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-auto lcnl-nav">
        <li class="nav-item"><a class="nav-link fw-semibold<?= $navIsActive('/') ? ' active' : '' ?>"<?= $navIsActive('/') ? ' aria-current="page"' : '' ?> href="<?= base_url('/') ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold<?= $navIsActive('events') ? ' active' : '' ?>"<?= $navIsActive('events') ? ' aria-current="page"' : '' ?> href="<?= base_url('events') ?>">Events</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold<?= $navIsActive('gallery') ? ' active' : '' ?>"<?= $navIsActive('gallery') ? ' aria-current="page"' : '' ?> href="<?= base_url('gallery') ?>">Gallery</a></li>

        <li class="nav-item"><a class="nav-link fw-semibold<?= $navIsActive('membership') ? ' active' : '' ?>"<?= $navIsActive('membership') ? ' aria-current="page"' : '' ?> href="<?= base_url('membership') ?>">Membership</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold<?= $navIsActive('legacy50') ? ' active' : '' ?>"<?= $navIsActive('legacy50') ? ' aria-current="page"' : '' ?> href="<?= base_url('legacy50') ?>">Legacy 50</a></li>

        <!-- Services Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link fw-semibold dropdown-toggle<?= $navIsActive('bereavement', 'tabletennis', 'dlc-hire') ? ' active' : '' ?>"
            href="#" id="servicesDropdown" role="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            Services
          </a>
          <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
            <li><a class="dropdown-item<?= $navIsActive('/bereavement') ? ' active' : '' ?>"<?= $navIsActive('/bereavement') ? ' aria-current="page"' : '' ?> href="<?= base_url('/bereavement') ?>">Bereavement</a></li>
            <li><a class="dropdown-item<?= $navIsActive('/tabletennis') ? ' active' : '' ?>"<?= $navIsActive('/tabletennis') ? ' aria-current="page"' : '' ?> href="<?= base_url('/tabletennis') ?>">Table Tennis</a></li>
            <li><a class="dropdown-item<?= $navIsActive('/dlc-hire') ? ' active' : '' ?>"<?= $navIsActive('/dlc-hire') ? ' aria-current="page"' : '' ?> href="<?= base_url('/dlc-hire') ?>">Hall Hire</a></li>
            <!-- <li><a class="dropdown-item" href="">Senior Mens</a></li>
            <li><a class="dropdown-item" href="">Senior Ladies</a></li>
            <li><a class="dropdown-item" href="">Raghuvanshi Charitable Trust</a></li>
            <li><a class="dropdown-item" href="">Lohana Charitable Foundation</a></li> -->
          </ul>
        </li>


        <li class="nav-item"><a class="nav-link fw-semibold<?= $navIsActive('contact') ? ' active' : '' ?>"<?= $navIsActive('contact') ? ' aria-current="page"' : '' ?> href="<?= base_url('contact') ?>">Contact</a></li>
        <!-- About Us also lights up on the committee pages, which it links to -->
        <li class="nav-item"><a class="nav-link fw-semibold<?= $navIsActive('aboutus', 'committee', 'mahila', 'yls', 'youth', 'lcf') ? ' active' : '' ?>"<?= $navIsActive('aboutus') ? ' aria-current="page"' : '' ?> href="<?= base_url('aboutus') ?>">About Us</a></li>
      </ul>
    </div>
  </div>
</nav>

<?php if (session()->get('isAdminLoggedIn')): ?>
  <nav class="navbar navbar-expand-lg navbar-dark bg-darkblue py-2 shadow-sm">
    <div class="container-fluid">
      <!-- Admin nav toggler -->
      <button class="navbar-toggler d-flex d-lg-none align-items-center gap-2 px-3" type="button"
        data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false"
        aria-label="Toggle admin navigation">
        <span class="navbar-toggler-icon"></span>
        <span class="navbar-toggler-text fw-semibold">Menu</span>
      </button>

      <div class="collapse navbar-collapse justify-content-center" id="adminNav">
        <ul class="navbar-nav gap-4">

          <li class="nav-item d-flex align-items-center">
            <span class="navbar-text text-light fw-semibold me-3">
              👋 Welcome, <?= esc(session()->get('admin_name') ?? 'Admin') ?>
            </span>
          </li>

          <?php if (hasRole('ADMIN', 'WEBSITE', 'FINANCE', 'MEMBERSHIP', 'EVENTS')): ?>
            <!-- System Dashboard -->
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('admin/system/dashboard') ?>">
                <i class="bi bi-speedometer2 me-1"></i> Dashboard
              </a>
            </li>
          <?php endif; ?>

          <?php if (hasRole('ADMIN')): ?>
            <!-- System: users, email queue, cron logs -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="adminSystemDropdown" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-gear-fill me-1"></i> Admin
              </a>
              <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="adminSystemDropdown">
                <li>
                  <a class="dropdown-item" href="<?= base_url('admin/system/users') ?>">
                    <i class="bi bi-person-gear me-1"></i> Users
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="<?= base_url('admin/system/emails') ?>">
                    <i class="bi bi-envelope-fill me-1"></i> Emails
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="<?= base_url('admin/system/cron-logs') ?>">
                    <i class="bi bi-clock-history me-1"></i> Cron Jobs
                  </a>
                </li>
              </ul>
            </li>

            <!-- Legacy 50 pledges -->
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('admin/legacy50') ?>">
                <i class="bi bi-heart-fill me-1"></i> Legacy 50
              </a>
            </li>

            <!-- Photo gallery -->
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('admin/gallery') ?>">
                <i class="bi bi-images me-1"></i> Gallery
              </a>
            </li>
          <?php endif; ?>


          <?php if (hasRole('ADMIN', 'WEBSITE')): ?>
            <!-- Committees -->
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('admin/content/committee') ?>">
                <i class="bi bi-people-fill me-1"></i> Committees
              </a>
            </li>

            <!-- FAQs -->
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('admin/content/faqs') ?>">
                <i class="bi bi-question-circle-fill me-1"></i> FAQs
              </a>
            </li>
          <?php endif; ?>


          <?php if (hasRole('ADMIN', 'EVENTS')): ?>
            <!-- Events -->
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('admin/content/events') ?>">
                <i class="bi bi-calendar-event-fill me-1"></i> Events
              </a>
            </li>

            <!-- Chopda Pujan registrations -->
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('admin/chopda-pujan') ?>">
                <i class="bi bi-journal-check me-1"></i> Chopda Pujan
              </a>
            </li>
          <?php endif; ?>


          <?php if (hasRole('ADMIN', 'MEMBERSHIP')): ?>
            <!-- Members -->
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('admin/membership') ?>">
                <i class="bi bi-person-lines-fill me-1"></i> Members
              </a>
            </li>
          <?php endif; ?>

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
      const label = toggler?.querySelector('.navbar-toggler-text');
      const target = document.querySelector(toggler?.getAttribute('data-bs-target'));
      if (!toggler || !label || !target) return;
      target.addEventListener('shown.bs.collapse', () => label.textContent = 'Close');
      target.addEventListener('hidden.bs.collapse', () => label.textContent = 'Menu');
    }
    document.querySelectorAll('.navbar-toggler').forEach(wireLabel);
  });
</script>

<style>
  .navbar-toggler {
    min-height: 44px;
  }

  .navbar-toggler-text {
    letter-spacing: .02em;
  }
</style>
