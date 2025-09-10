<nav class="navbar navbar-expand-lg navbar-light bg-lcnl">
  <div class="container">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-auto lcnl-nav">
      
      <li class="nav-item">
          <a class="nav-link fw-semibold" href="<?= base_url('/') ?>">Home</a>
        </li>

        <li class="nav-item">
          <a class="nav-link fw-semibold" href="<?= base_url('events') ?>">Events</a>
        </li>

        <!-- Committees Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link fw-semibold dropdown-toggle" 
             href="#" 
             id="committeeDropdown" 
             role="button" 
             data-bs-toggle="dropdown" 
             aria-expanded="false">
            Committees
          </a>
          <ul class="dropdown-menu" aria-labelledby="committeeDropdown">
            <li><a class="dropdown-item" href="<?= base_url('/committee') ?>">Executive Committee</a></li>  
            <li><a class="dropdown-item" href="<?= base_url('/mahila') ?>">Mahila Mandal</a></li>  
            <li><a class="dropdown-item" href="<?= base_url('/yls') ?>">Young Lohana Society</a></li>
            <li><a class="dropdown-item" href="<?= base_url('/') ?>">Youth Committee</a></li>
            <li><a class="dropdown-item" href="<?= base_url('/') ?>">Senior Mens</a></li>
            <li><a class="dropdown-item" href="<?= base_url('/') ?>">Senior Ladies</a></li>
            <li><a class="dropdown-item" href="<?= base_url('/') ?>">Raghuvanshi Charitable Trust</a></li>
            <li><a class="dropdown-item" href="<?= base_url('/') ?>">Lohana Charitable Foundation</a></li>
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

<?php if (session()->get('isLoggedIn') && session()->get('user_role') === 'ADMIN'): ?>
  <nav class="navbar navbar-expand-lg navbar-dark bg-darkblue py-2 shadow-sm">
    <div class="container-fluid">
      
      <!-- Collapsible menu -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-center" id="adminNav">
        <ul class="navbar-nav gap-4">
          <li class="nav-item d-flex align-items-center">
            <span class="navbar-text text-light fw-semibold me-3">
              👋 Welcome, <?= esc(session()->get('firstname') ?? 'Admin') ?>
            </span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">
              <i class="bi bi-speedometer2 me-1"></i> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admin/committee') ?>">
              <i class="bi bi-people-fill me-1"></i> Committees
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admin/events') ?>">
              <i class="bi bi-calendar-event-fill me-1"></i> Events
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admin/faqs') ?>">
              <i class="bi bi-question-circle-fill me-1"></i> FAQs
            </a>
          </li>
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




