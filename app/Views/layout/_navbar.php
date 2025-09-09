<nav class="navbar navbar-expand-lg navbar-light bg-lcnl">
    <div class="container">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-auto lcnl-nav">
        <!-- Home with hover dropdown but also links to "/" -->
        
        <li class="nav-item"><a class="nav-link fw-semibold" href="<?= base_url('events') ?>">Events</a></li>
        <!-- Home with hover dropdown but also links to "/" -->
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
    <li><a class="dropdown-item" href="<?= base_url('committee') ?>">Executive Committee</a></li>  
    <li><a class="dropdown-item" href="<?= base_url('/') ?>">Mahila Mandal</a></li>  
    <li><a class="dropdown-item" href="<?= base_url('/') ?>">Young Lohana Society</a></li>
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
