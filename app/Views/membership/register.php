<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero -->
<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-1"><i class="bi bi-person-plus-fill me-2"></i>Register</h1>
    <p class="mb-0 opacity-75">Join LCNL - Quick and secure sign up</p>
  </div>
</section>

<div class="container py-5">
  <?php if ($msg = session()->getFlashdata('message')): ?>
    <div class="alert alert-success shadow-sm alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i><?= esc($msg) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger shadow-sm alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <strong>Please fix the following issues:</strong>
      <ul class="mb-0 mt-2">
        <?php foreach ($errors as $err): ?>
          <li><?= esc($err) ?></li>
        <?php endforeach; ?>
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
      <div class="card shadow-lg border-0 auth-card">
        <div class="card-header bg-gradient py-3">
          <h3 class="mb-0 d-flex align-items-center">
            <i class="bi bi-person-lines-fill me-2"></i>
            Create your LCNL account
          </h3>
          <p class="mb-0 mt-1 small opacity-90">Please fill in all required fields marked with *</p>
        </div>

        <div class="card-body p-4 p-md-5">
          <form method="post" action="<?= route_to('membership.create') ?>" novalidate id="registerForm">
            <?= csrf_field() ?>
            <input type="text" name="website" value="" class="d-none" tabindex="-1" autocomplete="off"
              aria-hidden="true">

            <!-- Personal Information -->
            <div class="form-section">
              <h5 class="section-title mb-3">
                <i class="bi bi-person-badge me-2"></i>Personal Information
              </h5>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold" for="first_name">
                    First Name <span class="text-danger">*</span>
                  </label>
                  <div class="input-group has-validation">
                    <span class="input-group-text bg-light"><i class="bi bi-person-fill text-brand"></i></span>
                    <input type="text" id="first_name" name="first_name" value="<?= esc(old('first_name') ?? '') ?>"
                      class="form-control" autocomplete="given-name" placeholder="John" required>
                  </div>
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold" for="last_name">
                    Surname <span class="text-danger">*</span>
                  </label>
                  <div class="input-group has-validation">
                    <span class="input-group-text bg-light"><i class="bi bi-person-fill text-brand"></i></span>
                    <input type="text" id="last_name" name="last_name" value="<?= esc(old('last_name') ?? '') ?>"
                      class="form-control" autocomplete="family-name" placeholder="Smith" required>
                  </div>
                </div>

                <div class="col-12">
                  <label class="form-label fw-semibold" for="email">
                    Email Address <span class="text-danger">*</span>
                  </label>
                  <div class="input-group has-validation">
                    <span class="input-group-text bg-light"><i class="bi bi-envelope-fill text-brand"></i></span>
                    <input type="email" id="email" name="email" value="<?= esc(old('email') ?? '') ?>"
                      class="form-control" inputmode="email" autocomplete="email" placeholder="john.smith@example.com"
                      required>
                  </div>
                  <div class="form-text">We'll never share your email with anyone else.</div>
                </div>

                <div class="col-12">
                  <label class="form-label fw-semibold" for="mobile">Mobile Number</label>
                  <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-telephone-fill text-brand"></i></span>
                    <input type="tel" id="mobile" name="mobile" value="<?= esc(old('mobile') ?? '') ?>"
                      class="form-control" inputmode="tel" autocomplete="tel" placeholder="+447123456789"
                      pattern="^\+?\d{7,15}$">
                  </div>
                  <div class="form-text">
                    <i class="bi bi-info-circle me-1"></i>Use international format, e.g. +447...
                  </div>
                </div>
              </div>
            </div>

            <!-- Address Details -->
            <div class="form-section">
              <h5 class="section-title mb-3">
                <i class="bi bi-house-door-fill me-2"></i>Address Details
              </h5>

              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label fw-semibold" for="address1">
                    Address Line 1 <span class="text-danger">*</span>
                  </label>
                  <div class="input-group has-validation">
                    <span class="input-group-text bg-light"><i class="bi bi-geo-alt-fill text-brand"></i></span>
                    <input type="text" id="address1" name="address1" value="<?= esc(old('address1') ?? '') ?>"
                      class="form-control" autocomplete="address-line1" placeholder="123 High Street" required>
                  </div>
                </div>

                <div class="col-12">
                  <label class="form-label fw-semibold" for="address2">Address Line 2</label>
                  <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-geo-fill text-brand"></i></span>
                    <input type="text" id="address2" name="address2" value="<?= esc(old('address2') ?? '') ?>"
                      class="form-control" autocomplete="address-line2" placeholder="Apartment, suite, unit, etc.">
                  </div>
                </div>

                <div class="col-md-7">
                  <label class="form-label fw-semibold" for="city">
                    City / Town <span class="text-danger">*</span>
                  </label>
                  <div class="input-group has-validation">
                    <span class="input-group-text bg-light"><i class="bi bi-building text-brand"></i></span>
                    <input type="text" id="city" name="city" value="<?= esc(old('city') ?? '') ?>" class="form-control"
                      autocomplete="address-level2" placeholder="London" required>
                  </div>
                </div>

                <div class="col-md-5">
                  <label class="form-label fw-semibold" for="postcode">
                    Postcode <span class="text-danger">*</span>
                  </label>
                  <div class="input-group has-validation">
                    <span class="input-group-text bg-light"><i class="bi bi-mailbox text-brand"></i></span>
                    <input type="text" id="postcode" name="postcode" value="<?= esc(old('postcode') ?? '') ?>"
                      class="form-control" autocomplete="postal-code" placeholder="SW1A 1AA" maxlength="12" required>
                  </div>
                </div>
              </div>
            </div>



            <!-- Security -->
            <div class="form-section">
              <h5 class="section-title mb-3">
                <i class="bi bi-shield-lock-fill me-2"></i>Security
              </h5>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold" for="password">
                    Password <span class="text-danger">*</span>
                  </label>
                  <div class="input-group has-validation">
                    <span class="input-group-text bg-light"><i class="bi bi-key-fill text-brand"></i></span>
                    <input type="password" id="password" name="password" class="form-control"
                      autocomplete="new-password" placeholder="Enter password" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword"
                      aria-label="Show password">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                  <div class="form-text">
                    <i class="bi bi-info-circle me-1"></i>Use 8+ characters with letters, numbers & symbols.
                  </div>
                  <div class="password-strength mt-2" id="passwordStrength" style="display: none;">
                    <div class="progress" style="height: 4px;">
                      <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <small class="strength-text"></small>
                  </div>
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold" for="pass_confirm">
                    Confirm Password <span class="text-danger">*</span>
                  </label>
                  <div class="input-group has-validation">
                    <span class="input-group-text bg-light"><i class="bi bi-key-fill text-brand"></i></span>
                    <input type="password" id="pass_confirm" name="pass_confirm" class="form-control"
                      autocomplete="new-password" placeholder="Re-enter password" required>
                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirm"
                      aria-label="Show password">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                  <div class="invalid-feedback" id="confirmFeedback">
                    <i class="bi bi-x-circle me-1"></i>Passwords do not match.
                  </div>
                  <div class="valid-feedback" id="confirmSuccess">
                    <i class="bi bi-check-circle me-1"></i>Passwords match!
                  </div>
                </div>
              </div>
            </div>

            <!-- Consent -->
            <div class="form-section">
              <div class="form-check p-3 bg-light rounded border">
                <input class="form-check-input" type="checkbox" name="consent" id="consent" value="1" required>
                <label class="form-check-label" for="consent">
                  <strong>I consent</strong> to LCNL storing my details and contacting me about membership, events and
                  other community related activities.

                </label>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="d-grid gap-2">
              <button class="btn btn-accent btn-lg rounded-pill shadow-sm" type="submit">
                <i class="bi bi-check2-circle me-2"></i>Create My Account
              </button>
            </div>
          </form>
        </div>

        <div class="card-footer bg-light text-center py-3">
          <span class="text-muted">Already have an account?</span>
          <a class="fw-semibold ms-1" href="<?= base_url('membership/login') ?>">
            Sign in here <i class="bi bi-arrow-right-short"></i>
          </a>
        </div>
      </div>

      <div class="text-center mt-4">
        <a href="<?= base_url('/') ?>" class="btn btn-link text-decoration-none">
          <i class="bi bi-arrow-left-circle me-1"></i>Back to homepage
        </a>
      </div>
    </div>
  </div>
</div>

<script>
  (function () {
    'use strict';

    // Password Toggle Functionality
    function setupPasswordToggle(btnId, inputId) {
      const btn = document.getElementById(btnId);
      const input = document.getElementById(inputId);

      if (!btn || !input) return;

      btn.addEventListener('click', function () {
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';

        const icon = this.querySelector('i');
        icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        this.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
      });
    }

    setupPasswordToggle('togglePassword', 'password');
    setupPasswordToggle('toggleConfirm', 'pass_confirm');

    // Password Strength Indicator
    const passwordInput = document.getElementById('password');
    const strengthIndicator = document.getElementById('passwordStrength');

    if (passwordInput && strengthIndicator) {
      const progressBar = strengthIndicator.querySelector('.progress-bar');
      const strengthText = strengthIndicator.querySelector('.strength-text');

      passwordInput.addEventListener('input', function () {
        const password = this.value;

        if (password.length === 0) {
          strengthIndicator.style.display = 'none';
          return;
        }

        strengthIndicator.style.display = 'block';

        let strength = 0;
        const checks = {
          length: password.length >= 8,
          lowercase: /[a-z]/.test(password),
          uppercase: /[A-Z]/.test(password),
          number: /[0-9]/.test(password),
          special: /[^A-Za-z0-9]/.test(password)
        };

        strength = Object.values(checks).filter(Boolean).length;
        const percentage = (strength / 5) * 100;

        progressBar.style.width = percentage + '%';

        if (strength <= 2) {
          progressBar.className = 'progress-bar bg-danger';
          strengthText.textContent = 'Weak password';
          strengthText.className = 'strength-text text-danger';
        } else if (strength === 3) {
          progressBar.className = 'progress-bar bg-warning';
          strengthText.textContent = 'Fair password';
          strengthText.className = 'strength-text text-warning';
        } else if (strength === 4) {
          progressBar.className = 'progress-bar bg-info';
          strengthText.textContent = 'Good password';
          strengthText.className = 'strength-text text-info';
        } else {
          progressBar.className = 'progress-bar bg-success';
          strengthText.textContent = 'Strong password';
          strengthText.className = 'strength-text text-success';
        }
      });
    }

    // Password Confirmation Validation
    const pwd = document.getElementById('password');
    const confirm = document.getElementById('pass_confirm');
    const feedback = document.getElementById('confirmFeedback');
    const success = document.getElementById('confirmSuccess');

    function checkPasswordMatch() {
      if (!pwd || !confirm || !feedback) return;

      if (confirm.value.length === 0) {
        confirm.classList.remove('is-invalid', 'is-valid');
        return;
      }

      const match = pwd.value === confirm.value;

      if (match) {
        confirm.classList.remove('is-invalid');
        confirm.classList.add('is-valid');
      } else {
        confirm.classList.remove('is-valid');
        confirm.classList.add('is-invalid');
      }
    }

    if (pwd && confirm) {
      pwd.addEventListener('input', checkPasswordMatch);
      confirm.addEventListener('input', checkPasswordMatch);
    }

    // Form Validation Enhancement
    const form = document.getElementById('registerForm');

    if (form) {
      form.addEventListener('submit', function (e) {
        if (!form.checkValidity()) {
          e.preventDefault();
          e.stopPropagation();
        }

        form.classList.add('was-validated');
      });

      // Real-time validation for required fields
      const requiredInputs = form.querySelectorAll('input[required]');

      requiredInputs.forEach(input => {
        input.addEventListener('blur', function () {
          if (this.value.trim() === '') {
            this.classList.add('is-invalid');
          } else {
            this.classList.remove('is-invalid');
          }
        });

        input.addEventListener('input', function () {
          if (this.classList.contains('is-invalid') && this.value.trim() !== '') {
            this.classList.remove('is-invalid');
          }
        });
      });
    }

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
      setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      }, 5000);
    });

  })();
</script>

<?= $this->endSection() ?>

