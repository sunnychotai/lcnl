<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('partials/page_header', [
  'overlay'    => 'steel',
  'heading'    => 'Join LCNL',
  'subheading' => 'Lohana Community North London',
]) ?>

<?php
// Server errors are keyed by field; each is shown under its field and summarised above the form
$fieldErrors = session()->getFlashdata('errors') ?? [];
$emailExists = (bool) session()->getFlashdata('email_exists');

// old() escapes for HTML by default; take it raw so esc() is applied exactly once
$old = static fn(string $f): string => esc((string) old($f, '', false), 'attr');
$error = static fn(string $f): string => esc($fieldErrors[$f] ?? '');
// Appended inside class="…": closes the class and adds aria-invalid when the field has an error
$invalid = static fn(string $f): string => isset($fieldErrors[$f]) ? ' is-invalid" aria-invalid="true' : '';

$labels = [
  'first_name' => 'First name', 'last_name' => 'Surname', 'email' => 'Email address',
  'mobile' => 'Mobile number', 'address1' => 'Address line 1', 'address2' => 'Address line 2',
  'city' => 'Town or city', 'postcode' => 'Postcode', 'password' => 'Password', 'consent' => 'Consent',
];
?>

<div class="container py-4 py-md-5">
  <div class="join-page">

    <?= view('membership/_steps', ['step' => 1]) ?>

    <p class="join-signin">
      Already a member? <a href="<?= base_url('membership/login') ?>">Log in</a>.
      Joined before but never logged in here? <a href="<?= base_url('membership/forgot') ?>">Set your password</a>.
    </p>

    <?php if ($emailExists): ?>
      <div class="alert alert-info join-summary border-info" role="alert" id="existingAccount" tabindex="-1">
        <h2><i class="bi bi-person-check-fill me-2"></i>You’re already registered with us</h2>
        <p class="mb-3">
          There is already an LCNL account for <strong><?= $old('email') ?></strong>.
          If you have never logged in, or can’t remember your password, set a new one and you’ll be straight in.
        </p>
        <a href="<?= base_url('membership/forgot') ?>" class="btn btn-brand rounded-pill px-4 me-2 mb-2">Set my password</a>
        <a href="<?= base_url('membership/login') ?>" class="btn btn-outline-brand rounded-pill px-4 mb-2">Log in</a>
      </div>
    <?php elseif ($fieldErrors): ?>
      <div class="alert alert-danger join-summary" role="alert" id="formErrors" tabindex="-1">
        <h2>Please check the following</h2>
        <ul>
          <?php foreach ($fieldErrors as $field => $message): ?>
            <li>
              <?php if (isset($labels[$field])): ?>
                <a href="#<?= esc($field, 'attr') ?>"><?= esc($message) ?></a>
              <?php else: ?>
                <?= esc($message) ?>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="join-card">
      <h2 class="h4 fw-bold mb-2">Create your account</h2>
      <p class="join-intro">
        Registering is free. Once you’ve confirmed your email you can become a
        <strong>Life Member</strong> for a one-off £75, by card or bank transfer.
      </p>

      <form method="post" action="<?= route_to('membership.create') ?>" novalidate id="registerForm" class="join-form">
        <?= csrf_field() ?>
        <input type="text" name="website" value="" class="d-none" tabindex="-1" autocomplete="off" aria-hidden="true">

        <fieldset class="join-fieldset">
          <legend>About you</legend>

          <div class="row g-3">
            <div class="col-sm-6">
              <label class="form-label" for="first_name">First name</label>
              <input type="text" id="first_name" name="first_name" value="<?= $old('first_name') ?>"
                class="form-control<?= $invalid('first_name') ?>" autocomplete="given-name"
                aria-describedby="first_name-error" required data-validate
                data-msg-required="Please enter your first name.">
              <div class="join-error" id="first_name-error"><?= $error('first_name') ?></div>
            </div>

            <div class="col-sm-6">
              <label class="form-label" for="last_name">Surname</label>
              <input type="text" id="last_name" name="last_name" value="<?= $old('last_name') ?>"
                class="form-control<?= $invalid('last_name') ?>" autocomplete="family-name"
                aria-describedby="last_name-error" required data-validate
                data-msg-required="Please enter your surname.">
              <div class="join-error" id="last_name-error"><?= $error('last_name') ?></div>
            </div>

            <div class="col-12">
              <label class="form-label" for="email">Email address</label>
              <div class="form-text mt-0 mb-2" id="email-hint">We’ll send a link to this address to confirm it’s yours.</div>
              <input type="email" id="email" name="email" value="<?= $old('email') ?>"
                class="form-control<?= $invalid('email') ?>" autocomplete="email" inputmode="email"
                autocapitalize="off" spellcheck="false"
                aria-describedby="email-hint email-error" required data-validate
                data-msg-required="Please enter your email address."
                data-msg-format="Please enter an email address like name@example.com.">
              <div class="join-error" id="email-error"><?= $error('email') ?></div>
            </div>

            <div class="col-12">
              <label class="form-label" for="mobile">Mobile number <span class="join-optional">(optional)</span></label>
              <div class="form-text mt-0 mb-2" id="mobile-hint">For last-minute event updates. A UK number like 07700 900123 is fine.</div>
              <input type="tel" id="mobile" name="mobile" value="<?= $old('mobile') ?>"
                class="form-control<?= $invalid('mobile') ?>" autocomplete="tel" inputmode="tel"
                pattern="^\+?[\d\s\-\(\)]{7,20}$"
                aria-describedby="mobile-hint mobile-error" data-validate
                data-msg-format="Please enter a valid mobile number, e.g. 07700 900123.">
              <div class="join-error" id="mobile-error"><?= $error('mobile') ?></div>
            </div>
          </div>
        </fieldset>

        <fieldset class="join-fieldset">
          <legend>Your address</legend>
          <p class="join-legend-hint">We post the Diwali magazine and AGM notices to members.</p>

          <div class="row g-3">
            <div class="col-12">
              <label class="form-label" for="address1">Address line 1</label>
              <input type="text" id="address1" name="address1" value="<?= $old('address1') ?>"
                class="form-control<?= $invalid('address1') ?>" autocomplete="address-line1"
                aria-describedby="address1-error" required data-validate
                data-msg-required="Please enter the first line of your address.">
              <div class="join-error" id="address1-error"><?= $error('address1') ?></div>
            </div>

            <div class="col-12">
              <label class="form-label" for="address2">Address line 2 <span class="join-optional">(optional)</span></label>
              <input type="text" id="address2" name="address2" value="<?= $old('address2') ?>"
                class="form-control<?= $invalid('address2') ?>" autocomplete="address-line2"
                aria-describedby="address2-error">
              <div class="join-error" id="address2-error"><?= $error('address2') ?></div>
            </div>

            <div class="col-sm-7">
              <label class="form-label" for="city">Town or city</label>
              <input type="text" id="city" name="city" value="<?= $old('city') ?>"
                class="form-control<?= $invalid('city') ?>" autocomplete="address-level2"
                aria-describedby="city-error" required data-validate
                data-msg-required="Please enter your town or city.">
              <div class="join-error" id="city-error"><?= $error('city') ?></div>
            </div>

            <div class="col-sm-5">
              <label class="form-label" for="postcode">Postcode</label>
              <input type="text" id="postcode" name="postcode" value="<?= $old('postcode') ?>"
                class="form-control<?= $invalid('postcode') ?>" autocomplete="postal-code"
                autocapitalize="characters" spellcheck="false" maxlength="12"
                aria-describedby="postcode-error" required data-validate
                data-msg-required="Please enter your postcode.">
              <div class="join-error" id="postcode-error"><?= $error('postcode') ?></div>
            </div>
          </div>
        </fieldset>

        <fieldset class="join-fieldset">
          <legend>Choose a password</legend>

          <label class="form-label" for="password">Password</label>
          <div class="form-text mt-0 mb-2" id="password-hint">At least 8 characters.</div>
          <div class="input-group">
            <input type="password" id="password" name="password"
              class="form-control<?= $invalid('password') ?>" autocomplete="new-password"
              minlength="8" aria-describedby="password-hint password-error" required data-validate
              data-msg-required="Please choose a password."
              data-msg-short="Your password needs at least 8 characters.">
            <button class="btn join-toggle" type="button" data-toggle-password="password" aria-pressed="false"
              aria-controls="password">Show</button>
          </div>
          <div class="join-error" id="password-error"><?= $error('password') ?></div>
        </fieldset>

        <div class="join-consent mb-4">
          <input class="form-check-input<?= $invalid('consent') ?>" type="checkbox" name="consent" id="consent" value="1"
            <?= old('consent') ? 'checked' : '' ?> aria-describedby="consent-error" required data-validate
            data-msg-required="Please tick the box to agree, so we can register you.">
          <div>
            <label for="consent">
              I agree to LCNL keeping my details and contacting me about my membership, events and community news.
            </label>
            <div class="join-error" id="consent-error"><?= $error('consent') ?></div>
          </div>
        </div>

        <button class="btn btn-brand btn-lg rounded-pill w-100" type="submit">Create my account</button>
      </form>
    </div>

    <p class="text-center text-muted small mt-4 mb-0">
      Questions about membership? Email <a href="mailto:membership@lcnl.org">membership@lcnl.org</a>
    </p>
  </div>
</div>

<script>
  (function () {
    'use strict';

    const form = document.getElementById('registerForm');
    if (!form) return;

    const fields = Array.from(form.querySelectorAll('[data-validate]'));
    const submit = form.querySelector('[type="submit"]');

    // Show / hide password
    form.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
      const input = document.getElementById(btn.dataset.togglePassword);
      btn.addEventListener('click', function () {
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.textContent = show ? 'Hide' : 'Show';
        btn.setAttribute('aria-pressed', String(show));
      });
    });

    function messageFor(el) {
      const v = el.validity;
      if (v.valid) return '';
      if (v.valueMissing) return el.dataset.msgRequired;
      if (v.typeMismatch || v.patternMismatch) return el.dataset.msgFormat;
      if (v.tooShort) return el.dataset.msgShort;
      return el.validationMessage;
    }

    function setError(el, message) {
      const box = document.getElementById(el.id + '-error');
      if (box) box.textContent = message || '';
      el.classList.toggle('is-invalid', !!message);
      if (message) {
        el.setAttribute('aria-invalid', 'true');
      } else {
        el.removeAttribute('aria-invalid');
      }
    }

    // Same summary the server renders: a list of links that jump to each field
    function showSummary(errors) {
      let summary = document.getElementById('formErrors');
      if (!summary) {
        summary = document.createElement('div');
        summary.id = 'formErrors';
        summary.className = 'alert alert-danger join-summary';
        summary.setAttribute('role', 'alert');
        summary.tabIndex = -1;
        const card = form.closest('.join-card');
        card.parentNode.insertBefore(summary, card);
      }

      const heading = document.createElement('h2');
      heading.textContent = 'Please check the following';
      const list = document.createElement('ul');

      errors.forEach(function (err) {
        const link = document.createElement('a');
        link.href = '#' + err.el.id;
        link.textContent = err.message;
        link.addEventListener('click', function (e) {
          e.preventDefault();
          err.el.focus();
        });
        const item = document.createElement('li');
        item.appendChild(link);
        list.appendChild(item);
      });

      summary.replaceChildren(heading, list);
      summary.focus();
    }

    form.addEventListener('submit', function (e) {
      const errors = [];

      fields.forEach(function (el) {
        const message = messageFor(el);
        setError(el, message);
        if (message) errors.push({ el: el, message: message });
      });

      if (errors.length) {
        e.preventDefault();
        showSummary(errors);
        return;
      }

      submit.disabled = true;
      submit.textContent = 'Creating your account…';
    });

    // Clear a field's error as soon as it is fixed — never flag it while still typing
    fields.forEach(function (el) {
      const clear = function () {
        if (el.classList.contains('is-invalid') && el.validity.valid) setError(el, '');
      };
      el.addEventListener('input', clear);
      el.addEventListener('change', clear);
    });

    // Server summary links: move focus to the field, not just scroll
    document.querySelectorAll('#formErrors a[href^="#"]').forEach(function (link) {
      link.addEventListener('click', function (e) {
        const target = document.getElementById(link.getAttribute('href').slice(1));
        if (target) {
          e.preventDefault();
          target.focus();
        }
      });
    });

    const notice = document.getElementById('formErrors') || document.getElementById('existingAccount');
    if (notice) notice.focus();
  })();
</script>

<?= $this->endSection() ?>
