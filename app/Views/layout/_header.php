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
            <span class="fw-bold fs-4 text-dark d-block">Lohana Community - North London</span>
          </a>

          <!-- Socials + Login (keep everything inside this flex) -->
          <div class="d-flex flex-wrap align-items-center gap-3 pt-1">

            <!-- Socials -->
            <div class="d-flex align-items-center gap-2 socials">
              <a href="https://www.facebook.com/groups/lcnlmahajan/" class="fs-4" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
              <a href="https://www.instagram.com/lcnlmahajan/" class="fs-4" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
              <a href="https://vimeo.com/lcnl" class="fs-4" aria-label="Vimeo"><i class="bi bi-vimeo"></i></a>
            </div>

            <span class="vr d-none d-lg-inline-block"></span>

            <!-- Member auth buttons -->
            <?php if (session()->get('member_id')): ?>
             <a href="<?= url_to('account.dashboard') ?>" class="btn btn-sm btn-outline-brand rounded-pill">
  <i class="bi bi-speedometer2 me-1"></i> Dashboard
</a>

              <a href="<?= base_url('member/logout') ?>" class="btn btn-sm btn-link text-danger">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
              </a>
            <?php else: ?>
              <a href="<?= base_url('member/login') ?>" class="btn btn-sm btn-brand rounded-pill">
                <i class="bi bi-box-arrow-in-right me-1"></i> Member Login
              </a>
            <?php endif; ?>

          </div>
          <!-- /Socials + Login -->

        </div>
      </div>
      <!-- /Right block -->

    </div>
  </div>
</header>

<script>
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".hero-lcnl-watermark").forEach(container => {
    const count = 15; // number of scattered logos per section
    const placed = []; // keep track of positions

    for (let i = 0; i < count; i++) {
      let tries = 0, placedOk = false, x, y, size;

      while (!placedOk && tries < 50) { // max attempts
        size = 50 + Math.random() * 150; // px
        x = Math.random() * 100; // %
        y = Math.random() * 100; // %

        // Convert % to pixels relative to container
        const rect = container.getBoundingClientRect();
        const px = (x / 100) * rect.width;
        const py = (y / 100) * rect.height;

        // Check overlap with already placed logos
        let overlap = false;
        for (const p of placed) {
          const dx = px - p.x;
          const dy = py - p.y;
          const dist = Math.sqrt(dx * dx + dy * dy);
          if (dist < (size/2 + p.size/2) * 0.9) { // 0.9 = tolerance
            overlap = true;
            break;
          }
        }

        if (!overlap) {
          placed.push({ x: px, y: py, size });
          placedOk = true;
        }
        tries++;
      }

      if (placedOk) {
        const img = document.createElement("img");
        img.src = "/assets/patterns/lcnl-watermark.svg";
        img.className = "random-logo";
        img.style.width = size + "px";
        img.style.top = y + "%";
        img.style.left = x + "%";
        img.style.transform =
          `translate(-50%, -50%) rotate(${Math.random() * 360}deg)`;
        container.appendChild(img);
      }
    }
  });
});
</script>