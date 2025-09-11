<header class="lcnl-header bg-white py-2 border-bottom">
  <div class="container">
    <div class="row align-items-center g-3">
      <!-- Logo (left) -->
      <div class="col-auto">
        <a href="<?= base_url('/') ?>" class="d-inline-block">
          <img src="<?= base_url('assets/img/lcnl-logo.png') ?>" alt="LCNL" class="lcnl-logo">
        </a>
      </div>

      <!-- Right block -->
      <div class="col">
        <div class="d-flex flex-column align-items-end text-end">
          <!-- Title -->
          <a href="<?= base_url('/') ?>" class="text-decoration-none site-title mb-2 mb-lg-3">
            <span class="fw-bold fs-4 text-dark d-block">
              Lohana Community of North London
            </span>
          </a>

          <!-- Socials + Login -->
          <div class="d-flex flex-wrap align-items-center gap-3 pt-1">
   <div class="d-flex align-items-center gap-2 socials">
  <a href="https://www.facebook.com/groups/lcnlmahajan/" class="fs-4" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
  <a href="https://www.instagram.com/lcnlmahajan/" class="fs-4" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
  <a href="https://vimeo.com/lcnl" class="fs-4" aria-label="Vimeo"><i class="bi bi-vimeo"></i></a>

  
</div>


           <?php if (session()->get('isLoggedIn')): ?>
  <a href="<?= base_url('/auth/logout') ?>" class="btn btn-brand btn-sm">
    <i class="bi bi-box-arrow-right me-1"></i> Logout
  </a>
<?php else: ?>
  <a href="<?= base_url('/auth/login') ?>" class="btn btn-brand btn-sm">
    <i class="bi bi-person-circle me-1"></i> Login
  </a>
<?php endif; ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</header>
