<footer class="text-white mt-5" style="background-color:var(--brand);">
  <div class="container py-4">
    <div class="row">
      <div class="col-md-5 mb-3">
        <h5 class="fw-bold">Lohana Community of North London</h5>
        <p class="small mb-2">
          Our aim is to bring together fellow Lohana community members in North London in celebrating religious Hindu festivals,
          enhancing cultural, educational, social and sporting opportunities for the North London Lohana Community (LCNL).
        </p>
      </div>

      <div class="col-md-4 mb-3">
        <h6 class="fw-bold">Quick Links</h6>
        <ul class="list-unstyled">
          <li><a class="text-white text-decoration-none" href="<?= base_url('events') ?>">Events</a></li>
          <li><a class="text-white text-decoration-none" href="<?= base_url('membership') ?>">Membership</a></li>
          <li><a class="text-white text-decoration-none" href="<?= base_url('bereavement') ?>">Bereavement</a></li>
          <li><a class="text-white text-decoration-none" href="<?= base_url('contact') ?>">Contact</a></li>
          <li><a class="text-white text-decoration-none" href="<?= base_url('faqs') ?>">Frequently Asked Questions</a></li>

          <?php if (session()->get('isLoggedIn')): ?>
            <li><a class="text-white text-decoration-none" href="<?= base_url('/auth/logout') ?>"><i class="bi bi-box-arrow-right me-1"></i> Logout</a></li>
          <?php else: ?>
            <li><a class="text-white text-decoration-none" href="<?= base_url('/auth/login') ?>">Admin Login</a></li>
          <?php endif; ?>
        </ul>
      </div>

      <div class="col-md-3 mb-3">
        <h6 class="fw-bold">Contact</h6>
        <div class="small">info@lcnl.org.uk</div>

        <div class="mt-2 d-flex align-items-center gap-2">
          <a href="https://www.facebook.com/groups/lcnlmahajan/" class="fs-4 text-white" aria-label="Facebook" target="_blank" rel="noopener">
            <i class="bi bi-facebook"></i>
          </a>
          <a href="https://www.instagram.com/lcnlmahajan/" class="fs-4 text-white" aria-label="Instagram" target="_blank" rel="noopener">
            <i class="bi bi-instagram"></i>
          </a>
          <a href="https://vimeo.com/lcnl" class="fs-4 text-white" aria-label="Vimeo" target="_blank" rel="noopener">
            <i class="bi bi-vimeo"></i>
          </a>
          <!-- New: YouTube handle -->
          <a href="https://www.youtube.com/@lcnlmahajan" class="fs-4 text-white" aria-label="YouTube @lcnlmahajan" target="_blank" rel="noopener">
            <i class="bi bi-youtube"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="text-center small mt-3 opacity-75">© <?= date('Y') ?> Lohana Community of North London</div>
  </div>
</footer>
