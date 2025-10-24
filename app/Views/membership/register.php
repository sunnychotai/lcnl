<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero -->
<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-1"><i class="bi bi-person-plus-fill me-2"></i>Register</h1>
    <p class="mb-0 opacity-75">Quick, mobile-first sign up</p>
  </div>
</section>

<div class="container py-4">
  <?php if ($msg = session()->getFlashdata('message')): ?>
    <div class="alert alert-success shadow-sm">
      <i class="bi bi-check-circle-fill me-2"></i><?= esc($msg) ?>
    </div>
  <?php endif; ?>

  <?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger shadow-sm">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      Please fix the issues below.
      <ul class="mb-0 mt-2">
        <?php foreach ($errors as $err): ?>
          <li><?= esc($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="card shadow-sm border-0 auth-card no-hover">
        <div class="card-header bg-accent1 text-white">
          <h4 class="mb-0">
            <i class="bi bi-person-lines-fill me-2"></i>Create your LCNL account
          </h4>
        </div>

        <div class="card-body p-4">
          <form method="post" action="<?= route_to('membership.create') ?>" novalidate>
            <?= csrf_field() ?>

            <div class="row g-3">
              <div class="col-sm-6">
                <label class="form-label fw-semibold" for="first_name">First Name*</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                  <input type="text"
                         id="first_name"
                         name="first_name"
                         value="<?= esc(old('first_name') ?? '') ?>"
                         class="form-control"
                         autocomplete="given-name"
                         required>
                </div>
              </div>

              <div class="col-sm-6">
                <label class="form-label fw-semibold" for="last_name">Surname*</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                  <input type="text"
                         id="last_name"
                         name="last_name"
                         value="<?= esc(old('last_name') ?? '') ?>"
                         class="form-control"
                         autocomplete="family-name"
                         required>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold" for="email">Email*</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                  <input type="email"
                         id="email"
                         name="email"
                         value="<?= esc(old('email') ?? '') ?>"
                         class="form-control"
                         inputmode="email"
                         autocomplete="email"
                         required>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold" for="mobile">Mobile (optional)</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                  <input type="tel"
                         id="mobile"
                         name="mobile"
                         value="<?= esc(old('mobile') ?? '') ?>"
                         class="form-control"
                         inputmode="tel"
                         autocomplete="tel"
                         placeholder="+447..."
                         pattern="^\+?\d{7,15}$">
                </div>
                <div class="form-text">Use international format, e.g. +447...</div>
              </div>

              <div class="col-sm-6">
                <label class="form-label fw-semibold" for="password">Password*</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                  <input type="password"
                         id="password"
                         name="password"
                         class="form-control"
                         autocomplete="new-password"
                         required>
                  <button class="btn btn-outline-secondary" type="button" id="togglePassword" aria-label="Show password">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
                <!-- Optional simple strength hint -->
                <div class="form-text" id="pwdHint">Use 8+ chars with a mix of letters & numbers.</div>
              </div>

              <div class="col-sm-6">
                <label class="form-label fw-semibold" for="pass_confirm">Confirm Password*</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                  <input type="password"
                         id="pass_confirm"
                         name="pass_confirm"
                         class="form-control"
                         autocomplete="new-password"
                         required>
                  <button class="btn btn-outline-secondary" type="button" id="toggleConfirm" aria-label="Show password">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
                <div class="invalid-feedback" id="confirmFeedback">Passwords do not match.</div>
              </div>

              <div class="col-sm-6">
                <label class="form-label fw-semibold" for="postcode">Postcode (optional)</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                  <input type="text"
                         id="postcode"
                         name="postcode"
                         value="<?= esc(old('postcode') ?? '') ?>"
                         class="form-control"
                         autocomplete="postal-code"
                         maxlength="12">
                </div>
              </div>

              <div class="col-12">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="consent" id="consent" value="1" required>
                  <label class="form-check-label" for="consent">
                    I consent to LCNL storing my details and contacting me about membership and events.
                  </label>
                </div>
              </div>

              <div class="col-12">
                <button class="btn btn-accent btn-lg rounded-pill px-4 w-100" type="submit">
                  <i class="bi bi-check2-circle me-2"></i> Create Account
                </button>
              </div>
            </div>
          </form>
        </div>

        <div class="card-footer bg-light-subtle text-center">
          <span class="small text-muted">Already a member?</span>
          <a class="small ms-1" href="<?= base_url('member/login') ?>">Login here</a>.
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

<!-- Page-specific tweaks -->
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
  /* Match feedback spacing */
  #confirmFeedback { display: none; }
</style>

<script>
  // Show / hide passwords
  (function () {
    function wireToggle(btnId, inputId) {
      const btn = document.getElementById(btnId);
      const input = document.getElementById(inputId);
      if (!btn || !input) return;
      btn.addEventListener('click', function () {
        const isPwd = input.getAttribute('type') === 'password';
        input.setAttribute('type', isPwd ? 'text' : 'password');
        this.innerHTML = isPwd ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
        this.setAttribute('aria-label', isPwd ? 'Hide password' : 'Show password');
      });
    }
    wireToggle('togglePassword', 'password');
    wireToggle('toggleConfirm', 'pass_confirm');

    // Simple client-side confirm check (non-blocking; server still validates)
    const pwd = document.getElementById('password');
    const confirm = document.getElementById('pass_confirm');
    const feedback = document.getElementById('confirmFeedback');
    function checkMatch() {
      if (!pwd || !confirm || !feedback) return;
      const mismatch = confirm.value.length > 0 && pwd.value !== confirm.value;
      feedback.style.display = mismatch ? 'block' : 'none';
      confirm.classList.toggle('is-invalid', mismatch);
    }
    if (pwd && confirm) {
      pwd.addEventListener('input', checkMatch);
      confirm.addEventListener('input', checkMatch);
    }
  })();
</script>

<?= $this->endSection() ?>
