<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/** @var \Config\Legacy50 $config */
$errors = session('errors') ?? [];
$old    = static fn(string $k, $default = '') => old($k, $default);
$err    = static fn(string $field): string => isset($errors[$field]) ? ' is-invalid' : '';
$req    = '<span class="mela-req" aria-hidden="true">*</span>';

// Shown inline under a field, matching the Mela form.
$fieldError = static function (string $field) use ($errors): string {
  return isset($errors[$field])
    ? '<div class="invalid-feedback d-block">' . esc($errors[$field]) . '</div>'
    : '';
};

$giftAidOpen = $old('gift_aid_treat') || $old('gift_aid_taxpayer');
?>

<?= $this->include('legacy50/_hero') ?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-9">

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

      <!-- ======================= The appeal ======================= -->
      <h2 class="h3 fw-bold text-brand mb-3">Help us make the Dhamecha Lohana Centre debt-free!</h2>

      <p>
        As we celebrate 50 wonderful years of LCNL, we have a very special opportunity to achieve
        something truly meaningful for our community.
      </p>
      <p>
        The <strong>Dhamecha Lohana Centre</strong> &ndash; our home, our community, our legacy &ndash;
        is owned by LCNL through the Lohana Charitable Foundation (LCF). Over the years, it has been
        a place where we have come together to celebrate, connect, create memories and strengthen
        the bonds of our community.
      </p>
      <p class="mb-4">
        Today, we have just <strong>&pound;<?= esc(number_format($config->targetAmount)) ?></strong>
        remaining on the loan for our Centre, on which we continue to pay interest. What better way
        to mark our 50th anniversary than to clear this outstanding loan and secure a debt-free
        future for our community home?
      </p>

      <!-- ======================= The maths ======================= -->
      <div class="legacy-sum mb-2" aria-label="How your donation is doubled">
        <div class="legacy-sum-part">
          <span class="label">Your donation</span>
          <span class="value">&pound;<?= esc($config->donation) ?></span>
        </div>
        <span class="legacy-sum-op" aria-hidden="true">+</span>
        <div class="legacy-sum-part">
          <span class="label">LCNL&rsquo;s contribution</span>
          <span class="value">&pound;<?= esc($config->match) ?></span>
        </div>
        <span class="legacy-sum-op" aria-hidden="true">=</span>
        <div class="legacy-sum-part is-total">
          <span class="label">Towards the loan</span>
          <span class="value">&pound;<?= esc(number_format($config->donation + $config->match)) ?></span>
        </div>
      </div>
      <p class="text-center fw-semibold text-brand mb-4">
        <?= esc($config->targetDonors) ?> donors &times; &pound;<?= esc(number_format($config->donation + $config->match)) ?>
        = &pound;<?= esc(number_format($config->targetAmount)) ?> &mdash; loan cleared!
      </p>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <div class="mela-info">
            <div class="mela-info-head">
              <span class="mela-info-icon is-accent"><i class="bi bi-award-fill"></i></span>
              <h2>Your name. Our history.</h2>
            </div>
            <p>
              To thank everyone who helps us reach this milestone, we will install a
              <strong>commemorative plaque</strong> at the Dhamecha Lohana Centre displaying the
              names of all <?= esc($config->targetDonors) ?> donors once the loan has been repaid.
            </p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mela-info">
            <div class="mela-info-head">
              <span class="mela-info-icon"><i class="bi bi-gift-fill"></i></span>
              <h2>Gift Aid makes it go further</h2>
            </div>
            <p>
              If you are a UK taxpayer and your donation qualifies for Gift Aid, LCF may be able to
              claim an additional <strong>25p for every &pound;1</strong> you donate.
            </p>
          </div>
        </div>
      </div>

      <div class="mela-deadline">
        <i class="bi bi-flag-fill"></i>
        <span>Our target: <strong><?= esc($config->targetLabel()) ?></strong></span>
        <?php if ($isOpen): ?>
          <a href="#pledgeForm" class="btn btn-brand btn-sm ms-auto flex-shrink-0">
            Make your pledge <i class="bi bi-arrow-down ms-1"></i>
          </a>
        <?php endif; ?>
      </div>

      <p>
        Whether you have been part of LCNL for decades or have only recently joined our community,
        this is an opportunity for everyone to play a part in securing our home for future generations.
      </p>
      <p class="mb-4">
        Complete the pledge form below. <strong>Once you submit it, you will be shown LCF&rsquo;s bank
        details</strong> to make your &pound;<?= esc($config->donation) ?> donation by bank transfer,
        and we will email them to you too.
      </p>

      <!-- ======================= Form ======================= -->
      <?php if (! $isOpen): ?>

        <div class="mela-form-card mt-4">
          <div class="card-body p-4 p-md-5 text-center mela-closed">
            <i class="bi bi-heart-fill"></i>
            <h2 class="h4 fw-bold mt-3">Pledges have closed</h2>
            <p class="text-muted mb-0"><?= esc($closedMessage) ?></p>
          </div>
        </div>

      <?php else: ?>

        <div class="mela-form-card mt-4" id="pledgeForm">
          <div class="mela-form-head">
            <h2 class="h4"><i class="bi bi-pencil-square me-2"></i>Donation pledge form</h2>
            <p>Fields marked <span class="mela-req">*</span> are required. It takes about two minutes.</p>
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
                <div class="col-sm-3">
                  <label for="title" class="form-label fw-semibold">Title</label>
                  <select class="form-select<?= $err('title') ?>" id="title" name="title" autocomplete="honorific-prefix">
                    <option value=""></option>
                    <?php foreach ($config->titles as $t): ?>
                      <option value="<?= esc($t) ?>" <?= $old('title') === $t ? 'selected' : '' ?>><?= esc($t) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-sm-4">
                  <label for="first_name" class="form-label fw-semibold">First name <?= $req ?></label>
                  <input type="text" class="form-control<?= $err('first_name') ?>" id="first_name" name="first_name"
                    value="<?= esc($old('first_name')) ?>" maxlength="100" autocomplete="given-name" required>
                  <?= $fieldError('first_name') ?>
                </div>

                <div class="col-sm-5">
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

            <!-- 2. Plaque -->
            <div class="mela-section">
              <div class="mela-section-head">
                <span class="mela-step-num">2</span>
                <h3>Your name on the plaque</h3>
              </div>

              <label for="plaque_name" class="form-label fw-semibold">Name for the plaque <?= $req ?></label>
              <div class="input-group">
                <input type="text" class="form-control form-control-lg<?= $err('plaque_name') ?>" id="plaque_name"
                  name="plaque_name" value="<?= esc($old('plaque_name')) ?>"
                  maxlength="<?= esc($config->plaqueMaxLength, 'attr') ?>" aria-describedby="plaque_help" required>
                <button type="button" class="btn btn-outline-secondary" id="plaqueUseName">Use my name</button>
              </div>
              <div class="form-text d-flex justify-content-between gap-2" id="plaque_help">
                <span>Exactly as you would like it engraved, e.g. &ldquo;Ramesh &amp; Nila Patel&rdquo; or &ldquo;The Patel Family&rdquo;.</span>
                <span class="text-nowrap"><span id="plaqueCount">0</span>/<?= esc($config->plaqueMaxLength) ?></span>
              </div>
              <?= $fieldError('plaque_name') ?>

              <div class="legacy-plaque mt-3" aria-hidden="true">
                <span class="legacy-plaque-label">Plaque preview</span>
                <span class="legacy-plaque-name" id="plaquePreview">Your name here</span>
              </div>
            </div>

            <!-- 3. Gift Aid -->
            <div class="mela-section">
              <div class="mela-section-head">
                <span class="mela-step-num">3</span>
                <h3>Gift Aid <span class="text-muted fw-normal small">(if you are a UK taxpayer)</span></h3>
              </div>

              <p class="small text-muted">
                Gift Aid lets LCF claim an extra 25p for every &pound;1 you give, at no cost to you.
                <strong>Donating as a family?</strong> Gift Aid can only be declared by one UK taxpayer
                giving their own money, so please complete this form in that person&rsquo;s name. The
                plaque can still show the family name.
              </p>

              <div class="mela-confirm form-check">
                <input class="form-check-input js-giftaid<?= $err('gift_aid') ?>" type="checkbox" value="1"
                  id="gift_aid_treat" name="gift_aid_treat" <?= $old('gift_aid_treat') ? 'checked' : '' ?>>
                <label class="form-check-label" for="gift_aid_treat">
                  <strong>Gift Aid eligibility:</strong> <?= esc($config->giftAidStatements[0]) ?>
                </label>
              </div>

              <div class="mela-confirm form-check">
                <input class="form-check-input js-giftaid<?= $err('gift_aid') ?>" type="checkbox" value="1"
                  id="gift_aid_taxpayer" name="gift_aid_taxpayer" <?= $old('gift_aid_taxpayer') ? 'checked' : '' ?>>
                <label class="form-check-label" for="gift_aid_taxpayer">
                  <strong>Gift Aid declaration:</strong> <?= esc($config->giftAidStatements[1]) ?>
                </label>
              </div>
              <?= $fieldError('gift_aid') ?>

              <div class="legacy-address mt-3<?= $giftAidOpen ? '' : ' d-none' ?>" id="giftAidAddress">
                <p class="small fw-semibold mb-2">
                  HMRC needs your home address to process Gift Aid.
                </p>
                <div class="row g-3">
                  <div class="col-sm-4">
                    <label for="house_name_number" class="form-label fw-semibold">House name or number <?= $req ?></label>
                    <input type="text" class="form-control<?= $err('house_name_number') ?>" id="house_name_number"
                      name="house_name_number" value="<?= esc($old('house_name_number')) ?>" maxlength="100"
                      autocomplete="address-line1">
                    <?= $fieldError('house_name_number') ?>
                  </div>
                  <div class="col-sm-8">
                    <label for="street" class="form-label fw-semibold">Street</label>
                    <input type="text" class="form-control<?= $err('street') ?>" id="street" name="street"
                      value="<?= esc($old('street')) ?>" maxlength="150" autocomplete="address-line2">
                  </div>
                  <div class="col-sm-8">
                    <label for="town" class="form-label fw-semibold">Town or city <?= $req ?></label>
                    <input type="text" class="form-control<?= $err('town') ?>" id="town" name="town"
                      value="<?= esc($old('town')) ?>" maxlength="100" autocomplete="address-level2">
                    <?= $fieldError('town') ?>
                  </div>
                  <div class="col-sm-4">
                    <label for="postcode" class="form-label fw-semibold">Postcode <?= $req ?></label>
                    <input type="text" class="form-control text-uppercase<?= $err('postcode') ?>" id="postcode"
                      name="postcode" value="<?= esc($old('postcode')) ?>" maxlength="10" autocomplete="postal-code">
                    <?= $fieldError('postcode') ?>
                  </div>
                </div>
              </div>
            </div>

            <!-- 4. Payment -->
            <div class="mela-section">
              <div class="mela-section-head">
                <span class="mela-step-num">4</span>
                <h3>Your payment</h3>
              </div>

              <div class="mb-3">
                <label for="payment_reference" class="form-label fw-semibold">
                  Payment reference <?= $req ?>
                </label>
                <input type="text" class="form-control text-uppercase<?= $err('payment_reference') ?>"
                  id="payment_reference" name="payment_reference" value="<?= esc($old('payment_reference')) ?>"
                  maxlength="<?= esc($config->paymentReferenceMaxLength, 'attr') ?>"
                  aria-describedby="reference_help" required>
                <div class="form-text" id="reference_help">
                  The reference you will put on your bank transfer, so we can match your donation.
                  We have suggested one &mdash; change it if you like. Up to
                  <?= esc($config->paymentReferenceMaxLength) ?> characters.
                </div>
                <?= $fieldError('payment_reference') ?>
              </div>

              <fieldset class="mb-3">
                <legend class="form-label fw-semibold fs-6">Have you already made the transfer? <?= $req ?></legend>
                <div class="form-check">
                  <input class="form-check-input<?= $err('payment_timing') ?>" type="radio" name="payment_timing"
                    id="timing_will_pay" value="will_pay" <?= $old('payment_timing') === 'will_pay' ? 'checked' : '' ?>>
                  <label class="form-check-label" for="timing_will_pay">
                    Not yet &mdash; I will pay within 7 days using the bank details shown after I submit
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input<?= $err('payment_timing') ?>" type="radio" name="payment_timing"
                    id="timing_paid" value="paid" <?= $old('payment_timing') === 'paid' ? 'checked' : '' ?>>
                  <label class="form-check-label" for="timing_paid">Yes, I have already paid</label>
                </div>
                <?= $fieldError('payment_timing') ?>
              </fieldset>

              <div class="mela-confirm form-check">
                <input class="form-check-input<?= $err('confirmed_donation') ?>" type="checkbox" value="1"
                  id="confirmed_donation" name="confirmed_donation" <?= $old('confirmed_donation') ? 'checked' : '' ?> required>
                <label class="form-check-label" for="confirmed_donation">
                  I confirm I am pledging a donation of <strong>&pound;<?= esc($config->donation) ?></strong>
                  to the Lohana Charitable Foundation <?= $req ?>
                </label>
                <?= $fieldError('confirmed_donation') ?>
              </div>
            </div>

            <!-- 5. Submit -->
            <div class="mela-section">
              <div class="mela-section-head">
                <span class="mela-step-num">5</span>
                <h3>Confirm and submit</h3>
              </div>

              <div class="mb-3">
                <label for="comments" class="form-label fw-semibold">
                  Anything else we should know? <span class="text-muted fw-normal">(optional)</span>
                </label>
                <textarea class="form-control" id="comments" name="comments" rows="3"
                  maxlength="2000"><?= esc($old('comments')) ?></textarea>
              </div>

              <div class="mela-confirm form-check">
                <input class="form-check-input<?= $err('privacy_consent') ?>" type="checkbox" value="1"
                  id="privacy_consent" name="privacy_consent" <?= $old('privacy_consent') ? 'checked' : '' ?> required>
                <label class="form-check-label" for="privacy_consent">
                  I agree that LCNL and the Lohana Charitable Foundation may hold my details to manage my
                  pledge, claim Gift Aid where I have declared it, and prepare the commemorative plaque <?= $req ?>
                  <span class="d-block text-muted small">
                    See our <a href="<?= base_url('privacy') ?>">privacy policy</a>.
                  </span>
                </label>
                <?= $fieldError('privacy_consent') ?>
              </div>

              <button type="submit" class="btn btn-brand btn-lg w-100 mt-2">
                <i class="bi bi-heart-fill me-1"></i> Submit my pledge
              </button>
            </div>
          </form>
        </div>

      <?php endif; ?>

      <p class="text-center text-muted small mt-4 mb-0">
        Together, let&rsquo;s celebrate 50 years of LCNL by building a debt-free future for the
        next 50 years and beyond. Questions? Email <a href="mailto:info@lcnl.org">info@lcnl.org</a>.
      </p>

    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var errors = document.getElementById('formErrors');
    if (errors) errors.focus();

    var first   = document.getElementById('first_name');
    var last    = document.getElementById('last_name');
    var plaque  = document.getElementById('plaque_name');
    var count   = document.getElementById('plaqueCount');
    var preview = document.getElementById('plaquePreview');
    var useName = document.getElementById('plaqueUseName');
    var ref     = document.getElementById('payment_reference');
    var address = document.getElementById('giftAidAddress');
    var gaBoxes = document.querySelectorAll('.js-giftaid');

    if (!plaque) return; // form closed

    function syncPlaque() {
      var value = plaque.value.trim();
      count.textContent = plaque.value.length;
      preview.textContent = value !== '' ? value : 'Your name here';
    }

    useName.addEventListener('click', function () {
      plaque.value = (first.value.trim() + ' ' + last.value.trim()).trim().slice(0, plaque.maxLength);
      syncPlaque();
      plaque.focus();
    });
    plaque.addEventListener('input', syncPlaque);

    // Suggest a reference from the surname until the donor types their own.
    var prefix = <?= json_encode($config->paymentReferencePrefix, JSON_HEX_TAG) ?>;
    var refTouched = ref.value.trim() !== '';
    function suggestRef() {
      if (refTouched) return;
      var surname = last.value.replace(/[^A-Za-z]/g, '').toUpperCase();
      ref.value = (prefix + ' ' + surname).trim().slice(0, ref.maxLength);
    }
    ref.addEventListener('input', function () { refTouched = ref.value.trim() !== ''; });
    last.addEventListener('input', suggestRef);

    // The address is only needed (and only asked for) when claiming Gift Aid.
    function syncGiftAid() {
      var on = Array.prototype.some.call(gaBoxes, function (b) { return b.checked; });
      address.classList.toggle('d-none', !on);
    }
    gaBoxes.forEach(function (b) { b.addEventListener('change', syncGiftAid); });

    syncPlaque();
    suggestRef();
    syncGiftAid();
  });
</script>

<?= $this->endSection() ?>
