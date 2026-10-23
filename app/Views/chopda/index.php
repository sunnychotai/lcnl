<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/** @var \Config\ChopdaPujan $config */
$errors = session('errors') ?? [];
$old    = static fn(string $k, $default = '') => old($k, $default);
$err    = static fn(string $field): string => isset($errors[$field]) ? ' is-invalid' : '';
$req    = '<span class="mela-req" aria-hidden="true">*</span>';

// Shown inline under a field, matching the Mela and Legacy 50 forms.
$fieldError = static function (string $field) use ($errors): string {
  return isset($errors[$field])
    ? '<div class="invalid-feedback d-block">' . esc($errors[$field]) . '</div>'
    : '';
};

$flier = static fn(int $w): string => base_url($config->flier . '-' . $w . '.webp');
?>

<?php ob_start(); ?>
<div class="mela-hero-pills justify-content-start">
  <span class="pill"><i class="bi bi-calendar-event"></i> <?= esc($config->eventLabel()) ?></span>
  <span class="pill is-accent"><i class="bi bi-currency-pound"></i> <?= esc($config->fee) ?> per pooja</span>
</div>
<?php $headerExtra = ob_get_clean(); ?>
<?= view('partials/page_header', [
  'overlay'    => 'crimsonsunset',
  'eyebrow'    => 'LCNL cordially invites you to celebrate',
  'heading'    => 'Chopda Pujan 2026',
  'subheading' => 'A time for new beginnings, blessings and prosperity',
  'extra'      => $headerExtra,
]) ?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-xl-10">

      <?php if ($msg = session('error')): ?>
        <div class="alert alert-warning d-flex align-items-start gap-2" role="alert">
          <i class="bi bi-exclamation-triangle-fill mt-1"></i>
          <div><?= esc($msg) ?></div>
        </div>
      <?php endif; ?>

      <?php if (! empty($errors)): ?>
        <div class="alert alert-danger" role="alert" tabindex="-1" id="formErrors">
          <h2 class="h6 fw-bold mb-2">
            <i class="bi bi-exclamation-octagon-fill me-1"></i>Please check the following
          </h2>
          <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
              <li><?= esc($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <!-- ======================= The event ======================= -->
      <div class="row g-4 align-items-start mb-4">
        <div class="col-md-5">
          <a href="<?= base_url($config->flier . '.jpg') ?>" target="_blank" rel="noopener" class="chopda-flier"
            title="Open the full-size flier">
            <img src="<?= $flier(480) ?>" srcset="<?= $flier(480) ?> 480w, <?= $flier(1024) ?> 1024w"
              sizes="(min-width: 768px) 40vw, 100vw" width="480" height="720" decoding="async"
              alt="Chopda Pujan flier: <?= esc($config->eventLabel(), 'attr') ?>, registration <?= esc($config->registrationTimes, 'attr') ?>, pooja <?= esc($config->poojaTimes, 'attr') ?>, at <?= esc($config->venueLine(), 'attr') ?>. Nyochavar £<?= esc($config->fee, 'attr') ?> per pooja; <?= esc($config->yajmansPerPooja, 'attr') ?> Yajmans can sit for one pooja.">
          </a>
          <p class="small text-muted text-center mt-2 mb-0">
            <i class="bi bi-arrows-fullscreen me-1"></i>Tap the flier to see it full size
          </p>
        </div>

        <div class="col-md-7">
          <h2 class="h3 fw-bold text-brand mb-3">Join us for Chopda Pujan</h2>
          <p>
            Mark the start of the New Year with the traditional worship of the books of account,
            seeking Lord Ganesh and Maa Lakshmi&rsquo;s blessings for <em>shubh</em> and <em>labh</em>
            in the year ahead. The pooja will be conducted by
            <strong><?= esc($config->conductedBy) ?></strong>.
          </p>

          <div class="mela-facts chopda-facts">
            <div class="mela-fact">
              <i class="bi bi-calendar-event"></i>
              <span class="label">Date</span>
              <span class="value"><?= esc($config->eventLabel()) ?></span>
            </div>
            <div class="mela-fact">
              <i class="bi bi-cup-hot"></i>
              <span class="label">Registration</span>
              <span class="value"><?= esc($config->registrationTimes) ?></span>
            </div>
            <div class="mela-fact">
              <i class="bi bi-clock"></i>
              <span class="label">Pooja</span>
              <span class="value"><?= esc($config->poojaTimes) ?></span>
            </div>
            <div class="mela-fact">
              <i class="bi bi-geo-alt"></i>
              <span class="label">Venue</span>
              <span class="value"><?= esc($config->venue['name']) ?>, <?= esc($config->venue['address1']) ?>, <?= esc($config->venue['postcode']) ?></span>
            </div>
          </div>

          <div class="mela-info mb-3">
            <div class="mela-info-head">
              <span class="mela-info-icon is-accent"><i class="bi bi-currency-pound"></i></span>
              <h2>Nyochavar &pound;<?= esc($config->fee) ?> per pooja</h2>
            </div>
            <ul>
              <li><strong><?= esc($config->yajmansPerPooja) ?> Yajmans</strong> can sit for one pooja.</li>
              <li>Family and guests are very welcome to attend and observe.</li>
              <li>Please pay in advance by bank transfer &mdash; the details are shown once you register.</li>
            </ul>
          </div>

          <div class="mela-info">
            <div class="mela-info-head">
              <span class="mela-info-icon"><i class="bi bi-book"></i></span>
              <h2>What to bring</h2>
            </div>
            <p>
              All pooja samagri is provided. Please bring your own <strong>book</strong> (chopda) and
              <strong>swaroop of Lord Ganesh</strong>.
            </p>
          </div>
        </div>
      </div>

      <!-- ======================= Ceremony guides ======================= -->
      <div class="lcnl-card border-0 shadow-sm mb-4">
        <div class="card-body p-4 text-center">
          <h2 class="h5 fw-bold text-brand mb-1"><i class="bi bi-file-earmark-pdf me-1"></i> Ceremony guide</h2>
          <p class="text-muted small mb-3">Follow the pooja step by step in your preferred language.</p>
          <div class="d-flex flex-wrap justify-content-center gap-2">
            <?php foreach ($config->documents as $label => $path): ?>
              <?php if ($path !== '' && is_file(FCPATH . $path)): ?>
                <a href="<?= base_url($path) ?>" class="btn btn-outline-brand" target="_blank" rel="noopener">
                  <i class="bi bi-download me-1"></i> <?= esc($label) ?> (PDF)
                </a>
              <?php else: ?>
                <span class="btn btn-outline-secondary disabled" aria-disabled="true">
                  <i class="bi bi-hourglass-split me-1"></i> <?= esc($label) ?> &mdash; coming soon
                </span>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <?php if ($config->livestreamUrl !== ''): ?>
        <!-- ======================= Livestream ======================= -->
        <div class="lcnl-card border-0 shadow-sm mb-4">
          <div class="card-body p-4">
            <h2 class="h5 fw-bold text-brand mb-3 text-center"><i class="bi bi-broadcast me-1"></i> Watch live</h2>
            <div class="ratio ratio-16x9">
              <iframe src="<?= esc($config->livestreamUrl, 'attr') ?>" title="Chopda Pujan 2026 livestream"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen loading="lazy"></iframe>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($isOpen): ?>
        <div class="mela-deadline">
          <i class="bi bi-hourglass-split"></i>
          <span>Online registration closes <strong><?= esc($config->closingLabel()) ?></strong></span>
          <a href="#registerForm" class="btn btn-brand btn-sm ms-auto flex-shrink-0">
            Register <i class="bi bi-arrow-down ms-1"></i>
          </a>
        </div>
      <?php endif; ?>

      <!-- ======================= Form ======================= -->
      <?php if (! $isOpen): ?>

        <div class="mela-form-card mt-4">
          <div class="card-body p-4 p-md-5 text-center mela-closed">
            <i class="bi bi-journal-check"></i>
            <h2 class="h4 fw-bold mt-3">Registration has closed</h2>
            <p class="text-muted mb-0"><?= esc($closedMessage) ?></p>
          </div>
        </div>

      <?php else: ?>

        <div class="mela-form-card mt-4" id="registerForm">
          <div class="mela-form-head">
            <h2 class="h4"><i class="bi bi-pencil-square me-2"></i>Register for a pooja</h2>
            <p>One form books one pooja (&pound;<?= esc($config->fee) ?>). Fields marked <span class="mela-req">*</span> are required.</p>
          </div>

          <form method="post" action="<?= esc(site_url($config->slug)) ?>" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="form_time" value="<?= time() ?>">

            <!-- Honeypot: hidden from people, so anything here is a bot. -->
            <div class="d-none" aria-hidden="true">
              <label for="website">Leave this field blank</label>
              <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <!-- 1. About you -->
            <div class="mela-section">
              <div class="mela-section-head">
                <span class="mela-step-num">1</span>
                <h3>About you</h3>
              </div>

              <div class="row g-3">
                <div class="col-sm-6">
                  <label for="first_name" class="form-label fw-semibold">First name <?= $req ?></label>
                  <input type="text" class="form-control<?= $err('first_name') ?>" id="first_name" name="first_name"
                    value="<?= esc($old('first_name')) ?>" maxlength="100" autocomplete="given-name" required>
                  <?= $fieldError('first_name') ?>
                </div>

                <div class="col-sm-6">
                  <label for="last_name" class="form-label fw-semibold">Last name <?= $req ?></label>
                  <input type="text" class="form-control<?= $err('last_name') ?>" id="last_name" name="last_name"
                    value="<?= esc($old('last_name')) ?>" maxlength="100" autocomplete="family-name" required>
                  <?= $fieldError('last_name') ?>
                </div>

                <div class="col-md-7">
                  <label for="email" class="form-label fw-semibold">Email address <?= $req ?></label>
                  <input type="email" class="form-control<?= $err('email') ?>" id="email" name="email"
                    value="<?= esc($old('email')) ?>" maxlength="255" autocomplete="email" inputmode="email" required>
                  <div class="form-text">Your confirmation and the bank details are sent here.</div>
                  <?= $fieldError('email') ?>
                </div>

                <div class="col-md-5">
                  <label for="phone" class="form-label fw-semibold">Mobile number <?= $req ?></label>
                  <input type="tel" class="form-control<?= $err('phone') ?>" id="phone" name="phone"
                    value="<?= esc($old('phone')) ?>" maxlength="30" autocomplete="tel" inputmode="tel" required>
                  <?= $fieldError('phone') ?>
                </div>
              </div>
            </div>

            <!-- 2. Attendees -->
            <div class="mela-section">
              <div class="mela-section-head">
                <span class="mela-step-num">2</span>
                <h3>Who is coming</h3>
              </div>

              <label for="num_attendees" class="form-label fw-semibold">How many people are attending? <?= $req ?></label>
              <input type="number" class="form-control chopda-attendees<?= $err('num_attendees') ?>" id="num_attendees"
                name="num_attendees" value="<?= esc($old('num_attendees', '2')) ?>" min="1"
                max="<?= esc($config->maxAttendees, 'attr') ?>" inputmode="numeric"
                aria-describedby="attendees_rule attendees_help" required>
              <p class="chopda-rule mt-2 mb-1" id="attendees_rule">
                <i class="bi bi-exclamation-circle-fill me-1"></i>
                Only <?= esc($config->yajmansPerPooja) ?> Yajmans can sit in 1 pooja. Others are welcome to
                attend but won&rsquo;t be able to participate in the pooja.
              </p>
              <div class="form-text" id="attendees_help">
                Please include the Yajmans and everyone coming with you, so we can plan breakfast.
              </div>
              <?= $fieldError('num_attendees') ?>
            </div>

            <!-- 3. Payment -->
            <div class="mela-section">
              <div class="mela-section-head">
                <span class="mela-step-num">3</span>
                <h3>Your payment</h3>
              </div>

              <p class="small text-muted">
                The nyochavar is <strong>&pound;<?= esc($config->fee) ?></strong>, paid by bank transfer to LCNL.
                We will show you the bank details as soon as you register and email them to you too.
                Your place is confirmed once your payment arrives.
              </p>

              <div class="mb-3">
                <label for="payment_reference" class="form-label fw-semibold">
                  Payment reference <?= $req ?>
                </label>
                <input type="text" class="form-control text-uppercase<?= $err('payment_reference') ?>"
                  id="payment_reference" name="payment_reference" value="<?= esc($old('payment_reference')) ?>"
                  maxlength="<?= esc($config->paymentReferenceMaxLength, 'attr') ?>"
                  aria-describedby="reference_help" required>
                <div class="form-text" id="reference_help">
                  The reference you will put on your bank transfer, so we can match your payment.
                  We suggest <?= esc($config->paymentReferencePrefix) ?> + your first initial + surname
                  (e.g. <?= esc($config->paymentReferencePrefix) ?>SCHOTAI) &mdash; change it if you like. Up to
                  <?= esc($config->paymentReferenceMaxLength) ?> characters.
                </div>
                <?= $fieldError('payment_reference') ?>
              </div>

              <fieldset>
                <legend class="form-label fw-semibold fs-6">Have you already made the transfer? <?= $req ?></legend>
                <div class="form-check">
                  <input class="form-check-input<?= $err('payment_timing') ?>" type="radio" name="payment_timing"
                    id="timing_will_pay" value="will_pay" <?= $old('payment_timing') === 'will_pay' ? 'checked' : '' ?>>
                  <label class="form-check-label" for="timing_will_pay">
                    Not yet &mdash; I will pay by <?= esc($config->closingLabel()) ?> using the bank details shown after I register
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input<?= $err('payment_timing') ?>" type="radio" name="payment_timing"
                    id="timing_paid" value="paid" <?= $old('payment_timing') === 'paid' ? 'checked' : '' ?>>
                  <label class="form-check-label" for="timing_paid">Yes, I have already paid</label>
                </div>
                <?= $fieldError('payment_timing') ?>
              </fieldset>
            </div>

            <!-- 4. Submit -->
            <div class="mela-section">
              <div class="mela-section-head">
                <span class="mela-step-num">4</span>
                <h3>Confirm and register</h3>
              </div>

              <div class="mb-3">
                <label for="comments" class="form-label fw-semibold">
                  Anything else we should know? <span class="text-muted fw-normal">(optional)</span>
                </label>
                <textarea class="form-control" id="comments" name="comments" rows="3" maxlength="2000"
                  placeholder="e.g. names of the Yajmans, accessibility needs"><?= esc($old('comments')) ?></textarea>
              </div>

              <div class="mela-confirm form-check">
                <input class="form-check-input<?= $err('privacy_consent') ?>" type="checkbox" value="1"
                  id="privacy_consent" name="privacy_consent" <?= $old('privacy_consent') ? 'checked' : '' ?> required>
                <label class="form-check-label" for="privacy_consent">
                  I agree that LCNL may hold my details to manage my registration for Chopda Pujan <?= $req ?>
                  <span class="d-block text-muted small">
                    See our <a href="<?= base_url('privacy') ?>">privacy policy</a>.
                  </span>
                </label>
                <?= $fieldError('privacy_consent') ?>
              </div>

              <button type="submit" class="btn btn-brand btn-lg w-100 mt-2">
                <i class="bi bi-journal-check me-1"></i> Register and see payment details
              </button>
            </div>
          </form>
        </div>

      <?php endif; ?>

      <div class="mt-4">
        <?= $this->include('chopda/_contacts') ?>
      </div>

    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var errors = document.getElementById('formErrors');
    if (errors) errors.focus();

    var first = document.getElementById('first_name');
    var last  = document.getElementById('last_name');
    var ref   = document.getElementById('payment_reference');

    if (!ref) return; // form closed

    // Suggest prefix + first initial + surname, e.g. CP26SCHOTAI, until the
    // registrant types their own reference.
    var prefix = <?= json_encode($config->paymentReferencePrefix, JSON_HEX_TAG) ?>;
    var refTouched = ref.value.trim() !== '';
    function suggestRef() {
      if (refTouched) return;
      var letters = function (v) { return v.replace(/[^A-Za-z]/g, '').toUpperCase(); };
      ref.value = (prefix + letters(first.value).charAt(0) + letters(last.value)).slice(0, ref.maxLength);
    }
    ref.addEventListener('input', function () { refTouched = ref.value.trim() !== ''; });
    first.addEventListener('input', suggestRef);
    last.addEventListener('input', suggestRef);

    suggestRef();
  });
</script>

<?= $this->endSection() ?>
