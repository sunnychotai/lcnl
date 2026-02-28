<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero -->
<section class="hero-lcnl-watermark hero-overlay-midnight-teal d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-1">
      <i class="bi bi-shield-lock-fill me-2"></i>Admin Login
    </h1>
    <p class="mb-0 opacity-75">Committee &amp; Staff Access</p>
  </div>
</section>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">

      <?php if ($err = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger shadow-sm">
          <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc($err) ?>
        </div>
      <?php endif; ?>

      <?php if ($ok = session()->getFlashdata('success')): ?>
        <div class="alert alert-success shadow-sm">
          <i class="bi bi-check-circle-fill me-2"></i><?= esc($ok) ?>
        </div>
      <?php endif; ?>

      <div class="card shadow-sm border-0 auth-card no-hover">
        <div class="card-header bg-brand text-white">
          <h4 class="mb-0">
            <i class="bi bi-person-lock me-2"></i>Secure Access
          </h4>
        </div>

        <div class="card-body p-4">
          <form action="<?= base_url('auth/attemptLogin') ?>" method="post" novalidate>
            <?= csrf_field() ?>

            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">Email</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                <input type="email" id="email" name="email" class="form-control" value="<?= esc(old('email') ?? '') ?>"
                  inputmode="email" autocomplete="email" placeholder="you@lcnl.org" required>
              </div>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label fw-semibold">Password</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                <input type="password" id="password" name="password" class="form-control"
                  autocomplete="current-password" placeholder="••••••••" required>
                <button class="btn btn-outline-secondary" type="button" id="togglePassword" aria-label="Show password">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                <label class="form-check-label" for="remember">Remember me</label>
              </div>

              <!-- Admin forgot password:
                   CI4 Router::hasRoute() doesn't exist, so don't call it here.
                   Show link only if you actually have the route implemented. -->
              <?php $showForgot = false; ?>
              <?php if ($showForgot): ?>
                <a class="small text-decoration-none" href="<?= base_url('auth/forgot') ?>">
                  Forgot password?
                </a>
              <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-brand w-100">
              <i class="bi bi-box-arrow-in-right me-1"></i>Login
            </button>
          </form>
        </div>

        <div class="card-footer bg-light-subtle text-center small">
          For authorised LCNL administrators only.
        </div>
      </div>

      <div class="text-center mt-3">
        <a href="<?= base_url('/') ?>" class="text-decoration-none">
          <i class="bi bi-arrow-left-circle me-1"></i>Back to site
        </a>
      </div>

    </div>
  </div>
</div>

<style>
  .auth-card {
    border-left: 6px solid var(--brand);
    border-radius: var(--radius);
  }

  .auth-card .input-group-text {
    background: #fff;
    border-right: 0;
  }

  .auth-card .form-control {
    border-left: 0;
  }

  .auth-card .input-group .form-control:focus {
    box-shadow: none;
  }
</style>

<script>
  (function () {
    const btn = document.getElementById('togglePassword');
    const pwd = document.getElementById('password');
    if (!btn || !pwd) return;

    btn.addEventListener('click', function () {
      const isPwd = pwd.getAttribute('type') === 'password';
      pwd.setAttribute('type', isPwd ? 'text' : 'password');
      this.innerHTML = isPwd ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
      this.setAttribute('aria-label', isPwd ? 'Hide password' : 'Show password');
    });
  })();
</script>

<?= $this->endSection() ?>

