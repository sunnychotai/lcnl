<nav class="navbar navbar-expand-lg navbar-dark bg-gold">
  <div class="container">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-auto lcnl-nav">
        <!-- Home with hover dropdown but also links to "/" -->
        <li class="nav-item dropdown">
          <a class="nav-link fw-semibold dropdown-toggle-home" href="<?= base_url('/') ?>">
            Home
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= base_url('mission') ?>">Our Mission</a></li>
            <li><a class="dropdown-item" href="<?= base_url('aboutus') ?>">About Us</a></li>
            <li><a class="dropdown-item" href="<?= base_url('committee') ?>">Committee</a></li>
          </ul>
        </li>

        <li class="nav-item"><a class="nav-link fw-semibold" href="<?= base_url('events') ?>">Events</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold" href="<?= base_url('gallery') ?>">Gallery</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold" href="<?= base_url('membership') ?>">Membership</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold" href="<?= base_url('bereavement') ?>">Bereavement</a></li>
        <li class="nav-item"><a class="nav-link fw-semibold" href="<?= base_url('contact') ?>">Contact</a></li>
      </ul>
    </div>
  </div>
</nav>
