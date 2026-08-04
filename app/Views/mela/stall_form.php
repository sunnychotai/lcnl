<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/** @var \Config\MelaStalls $config */
$errors = session('errors') ?? [];
$old    = static fn(string $k, $default = '') => old($k, $default);

$err = static fn(string $field): string => isset($errors[$field]) ? ' is-invalid' : '';
$req = '<span class="mela-req" aria-hidden="true">*</span>';
?>

<!-- ======================= Hero ======================= -->
<section class="hero-lcnl-watermark hero-overlay-ruby d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-2">Stall Holder Booking</h1>
    <p class="lead fs-5 mb-0">Golden Jubilee 50th Anniversary &mdash; Event 3: Mela</p>

    <div class="mela-hero-pills">
      <span class="pill"><i class="bi bi-calendar-event"></i> <?= esc($config->eventLabel()) ?></span>
      <span class="pill"><i class="bi bi-clock"></i> <?= esc($config->eventTimes) ?></span>
      <span class="pill"><i class="bi bi-geo-alt"></i> <?= esc($config->venue['name']) ?>, <?= esc($config->venue['postcode']) ?></span>
      <span class="pill is-accent"><i class="bi bi-tag-fill"></i> &pound;<?= esc($config->fee) ?> per stall</span>
    </div>
  </div>
</section>

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

      <!-- ======================= Key facts ======================= -->
      <h2 class="visually-hidden">Key details</h2>
      <div class="mela-facts">
        <div class="mela-fact">
          <i class="bi bi-calendar2-check"></i>
          <span class="label">Date</span>
          <span class="value"><?= esc((new DateTimeImmutable($config->eventDate))->format('D j M Y')) ?></span>
        </div>
        <div class="mela-fact">
          <i class="bi bi-clock-history"></i>
          <span class="label">Open to public</span>
          <span class="value"><?= esc($config->eventTimes) ?></span>
        </div>
        <div class="mela-fact">
          <i class="bi bi-box-seam"></i>
          <span class="label">Pitch size</span>
          <span class="value"><?= esc($config->stallSize) ?></span>
        </div>
        <div class="mela-fact">
          <i class="bi bi-cash-coin"></i>
          <span class="label">Stall fee</span>
          <span class="value">&pound;<?= esc($config->fee) ?></span>
        </div>
        <div class="mela-fact">
          <i class="bi bi-hourglass-split"></i>
          <span class="label">Bookings close</span>
          <span class="value"><?= esc((new DateTimeImmutable($config->closingDate))->format('D j M')) ?></span>
        </div>
      </div>

      <?php if ($isOpen): ?>
        <div class="mela-deadline">
          <i class="bi bi-info-circle-fill"></i>
          <span>
            Please book and pay by <strong>midnight on <?= esc($config->closingLabel()) ?></strong>.
          </span>
          <a href="#bookingForm" class="btn btn-brand btn-sm ms-auto flex-shrink-0">
            Go to the form <i class="bi bi-arrow-down ms-1"></i>
          </a>
        </div>
      <?php endif; ?>

      <!-- ======================= Briefing ======================= -->
      <p class="mb-4">
        Thank you for your interest in booking a stall at the
        <strong><?= esc($config->eventName) ?></strong>. The event is open to people of all
        ages, with bingo, karaoke, sports, children&rsquo;s rides and much more throughout
        the day.
      </p>

      <div class="row g-3 mb-4">

        <div class="col-md-6">
          <div class="mela-info">
            <div class="mela-info-head">
              <span class="mela-info-icon"><i class="bi bi-geo-alt-fill"></i></span>
              <h2>Where</h2>
            </div>
            <address class="mb-0">
              <?php foreach ($config->venueLines() as $i => $line): ?>
                <?= $i === 0 ? '<strong>' . esc($line) . '</strong>' : esc($line) ?><br>
              <?php endforeach; ?>
            </address>
          </div>
        </div>

        <div class="col-md-6">
          <div class="mela-info">
            <div class="mela-info-head">
              <span class="mela-info-icon"><i class="bi bi-truck"></i></span>
              <h2>Setting up</h2>
            </div>
            <ul class="mb-0">
              <li>Access from <strong><?= esc($config->setUpFrom) ?></strong>.</li>
              <li>One vehicle may enter the main car park to unload.</li>
              <li>
                It <strong>must be moved by <?= esc($config->carParkClear) ?></strong> to the
                grass parking area, so the car park stays clear for visitors.
              </li>
            </ul>
          </div>
        </div>

        <div class="col-md-6">
          <div class="mela-info">
            <div class="mela-info-head">
              <span class="mela-info-icon is-accent"><i class="bi bi-box-seam"></i></span>
              <h2>Your pitch</h2>
            </div>
            <p class="mb-2">
              Approximately <strong><?= esc($config->stallSize) ?></strong>.
              <strong>No equipment is provided</strong> &mdash; bring your own table, gazebo,
              chairs, electricity or anything else you need.
            </p>
            <p class="mb-0 text-muted small">
              Pitches are allocated by LCNL based on when you booked and where your stall
              fits best. Food stalls are kept together, clothing near each other, and so on.
            </p>
          </div>
        </div>

        <div class="col-md-6">
          <div class="mela-info">
            <div class="mela-info-head">
              <span class="mela-info-icon"><i class="bi bi-shield-check"></i></span>
              <h2>Insurance and responsibility</h2>
            </div>
            <p class="mb-2">
              LCNL accepts <strong>no responsibility</strong> for the products or services
              offered at your stall. You must hold any insurance, licences or certifications
              needed to operate it.
            </p>
            <p class="mb-0 text-muted small">
              If we receive enquiries or complaints about your stall we will pass on your
              contact details so customers can reach you directly. LCNL will not deal with
              complaints on your behalf.
            </p>
          </div>
        </div>

        <div class="col-12">
          <div class="mela-info">
            <div class="mela-info-head">
              <span class="mela-info-icon is-accent"><i class="bi bi-egg-fried"></i></span>
              <h2>Food stalls</h2>
            </div>
            <p class="mb-0">
              If you sell food or drink you must upload a copy of your current
              <strong>Food Hygiene Certificate</strong> with this form, and bring a copy on
              the day as you may be asked to present it. A clear photo taken on your phone
              is fine.
            </p>
          </div>
        </div>
      </div>

      <!-- ======================= Payment ======================= -->
      <h2 class="h4 fw-bold text-brand mb-3">
        <i class="bi bi-bank me-1"></i> How to pay
      </h2>

      <div class="mela-pay mb-3">
        <div class="mela-pay-head">
          <span><i class="bi bi-credit-card-2-front-fill me-1"></i> Bank transfer, in advance</span>
          <span class="fee">&pound;<?= esc($config->fee) ?></span>
        </div>
        <div class="mela-pay-body">
          <div class="mela-pay-row">
            <span class="k">Account name</span>
            <span class="d-flex align-items-center gap-2">
              <span class="v" id="payAccName"><?= esc($config->bank['accountName']) ?></span>
              <button type="button" class="mela-copy" data-copy="#payAccName">Copy</button>
            </span>
          </div>
          <div class="mela-pay-row">
            <span class="k">Account number</span>
            <span class="d-flex align-items-center gap-2">
              <span class="v" id="payAccNo"><?= esc($config->bank['accountNumber']) ?></span>
              <button type="button" class="mela-copy" data-copy="#payAccNo">Copy</button>
            </span>
          </div>
          <div class="mela-pay-row">
            <span class="k">Sort code</span>
            <span class="d-flex align-items-center gap-2">
              <span class="v" id="paySort"><?= esc($config->bank['sortCode']) ?></span>
              <button type="button" class="mela-copy" data-copy="#paySort">Copy</button>
            </span>
          </div>
          <div class="mela-pay-row">
            <span class="k">Payment reference</span>
            <span class="d-flex align-items-center gap-2">
              <span class="v text-brand" id="paymentRef"><?= esc($config->paymentReference($old('company_name'))) ?></span>
              <button type="button" class="mela-copy" data-copy="#paymentRef">Copy</button>
            </span>
          </div>
        </div>
      </div>

      <div class="alert alert-warning d-flex align-items-start gap-2">
        <i class="bi bi-exclamation-triangle-fill mt-1"></i>
        <div>
          <strong>Please pay before completing this form.</strong>
          Without payment your stall is not booked. The reference above updates as you type
          your company name below &mdash; please use it exactly so we can match your payment.
        </div>
      </div>

      <!-- ======================= Form ======================= -->
      <?php if (! $isOpen): ?>

        <div class="mela-form-card mt-4">
          <div class="card-body p-4 p-md-5 text-center mela-closed">
            <i class="bi bi-clock-history"></i>
            <h2 class="h4 fw-bold mt-3">
              <?= $isFull ? 'All stalls have been allocated' : 'Stall bookings are closed' ?>
            </h2>
            <p class="text-muted mb-4"><?= esc($closedMessage) ?></p>
            <?= $this->include('mela/_contacts') ?>
          </div>
        </div>

      <?php else: ?>

        <div class="mela-form-card mt-4" id="bookingForm">
          <div class="mela-form-head">
            <h2 class="h4"><i class="bi bi-pencil-square me-2"></i>Booking form</h2>
            <p>
              Fields marked <span class="mela-req">*</span> are required.
              It takes about two minutes.
            </p>
          </div>

          <form method="post" action="<?= esc(current_url()) ?>" enctype="multipart/form-data" novalidate>
            <?= csrf_field() ?>

            <!-- Honeypot: hidden from people, so anything here is a bot. -->
            <div class="d-none" aria-hidden="true">
              <label for="website">Leave this field blank</label>
              <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <!-- 1. About the stall -->
            <div class="mela-section">
              <div class="mela-section-head">
                <span class="mela-step-num">1</span>
                <h3>About your stall</h3>
              </div>

              <div class="mb-3">
                <label for="company_name" class="form-label fw-semibold">
                  Company or stall name <?= $req ?>
                </label>
                <input type="text" class="form-control form-control-lg<?= $err('company_name') ?>"
                  id="company_name" name="company_name" value="<?= esc($old('company_name')) ?>"
                  maxlength="200" required>
                <div class="form-text">
                  Trading as an individual? Use your own name. This also forms your payment reference.
                </div>
                <?php if (isset($errors['company_name'])): ?>
                  <div class="invalid-feedback d-block"><?= esc($errors['company_name']) ?></div>
                <?php endif; ?>
              </div>

              <div class="mb-3">
                <label for="category" class="form-label fw-semibold">Type of stall <?= $req ?></label>
                <select class="form-select form-select-lg<?= $err('category') ?>" id="category"
                  name="category" required>
                  <option value="">Please choose&hellip;</option>
                  <?php foreach ($config->categories as $value => $label): ?>
                    <option value="<?= esc($value) ?>" <?= $old('category') === $value ? 'selected' : '' ?>>
                      <?= esc($label) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <div class="form-text">Helps us group similar stalls together.</div>
                <?php if (isset($errors['category'])): ?>
                  <div class="invalid-feedback d-block"><?= esc($errors['category']) ?></div>
                <?php endif; ?>
              </div>

              <div class="mb-3<?= $old('category') === 'other' ? '' : ' d-none' ?>" id="categoryOtherWrap">
                <label for="category_other" class="form-label fw-semibold">
                  Please specify the type of stall <?= $req ?>
                </label>
                <input type="text" class="form-control<?= $err('category_other') ?>"
                  id="category_other" name="category_other"
                  value="<?= esc($old('category_other')) ?>" maxlength="200">
                <?php if (isset($errors['category_other'])): ?>
                  <div class="invalid-feedback d-block"><?= esc($errors['category_other']) ?></div>
                <?php endif; ?>
              </div>

              <div class="mela-confirm form-check">
                <input class="form-check-input" type="checkbox" value="1" id="is_food_stall"
                  name="is_food_stall" <?= $old('is_food_stall') ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_food_stall">
                  <strong>This stall sells food or drink</strong>
                  <span class="d-block text-muted small">
                    Tick this if any food or drink is sold or given away. You will then need
                    to upload your Food Hygiene Certificate in step 3.
                  </span>
                </label>
              </div>

              <div class="mb-0">
                <label for="items_description" class="form-label fw-semibold">
                  What will you be selling or exhibiting? <?= $req ?>
                </label>
                <textarea class="form-control<?= $err('items_description') ?>" id="items_description"
                  name="items_description" rows="3" maxlength="2000"
                  required><?= esc($old('items_description')) ?></textarea>
                <?php if (isset($errors['items_description'])): ?>
                  <div class="invalid-feedback d-block"><?= esc($errors['items_description']) ?></div>
                <?php endif; ?>
              </div>
            </div>

            <!-- 2. Contact -->
            <div class="mela-section">
              <div class="mela-section-head">
                <span class="mela-step-num">2</span>
                <h3>Your contact details</h3>
              </div>
              <p class="text-muted small">
                These are the details we pass on if a customer needs to contact you about your stall.
              </p>

              <div class="row g-3">
                <div class="col-md-6">
                  <label for="contact_name" class="form-label fw-semibold">Your name <?= $req ?></label>
                  <input type="text" class="form-control<?= $err('contact_name') ?>" id="contact_name"
                    name="contact_name" value="<?= esc($old('contact_name')) ?>" maxlength="150"
                    autocomplete="name" required>
                  <?php if (isset($errors['contact_name'])): ?>
                    <div class="invalid-feedback d-block"><?= esc($errors['contact_name']) ?></div>
                  <?php endif; ?>
                </div>

                <div class="col-md-6">
                  <label for="contact_phone" class="form-label fw-semibold">Contact number <?= $req ?></label>
                  <input type="tel" class="form-control<?= $err('contact_phone') ?>" id="contact_phone"
                    name="contact_phone" value="<?= esc($old('contact_phone')) ?>" maxlength="30"
                    autocomplete="tel" inputmode="tel" required>
                  <?php if (isset($errors['contact_phone'])): ?>
                    <div class="invalid-feedback d-block"><?= esc($errors['contact_phone']) ?></div>
                  <?php endif; ?>
                </div>

                <div class="col-md-6">
                  <label for="contact_email" class="form-label fw-semibold">Email address <?= $req ?></label>
                  <input type="email" class="form-control<?= $err('contact_email') ?>" id="contact_email"
                    name="contact_email" value="<?= esc($old('contact_email')) ?>" maxlength="255"
                    autocomplete="email" inputmode="email" required>
                  <div class="form-text">Your confirmation is sent here.</div>
                  <?php if (isset($errors['contact_email'])): ?>
                    <div class="invalid-feedback d-block"><?= esc($errors['contact_email']) ?></div>
                  <?php endif; ?>
                </div>

                <div class="col-md-6">
                  <label for="vehicle_reg" class="form-label fw-semibold">
                    Vehicle registration <span class="text-muted fw-normal">(optional)</span>
                  </label>
                  <input type="text" class="form-control<?= $err('vehicle_reg') ?>" id="vehicle_reg"
                    name="vehicle_reg" value="<?= esc($old('vehicle_reg')) ?>" maxlength="20">
                  <div class="form-text">Helps us clear the car park by <?= esc($config->carParkClear) ?>.</div>
                </div>
              </div>
            </div>

            <!-- 3. Documents -->
            <div class="mela-section">
              <div class="mela-section-head">
                <span class="mela-step-num">3</span>
                <h3>Documents</h3>
              </div>

              <div class="mela-upload<?= $old('is_food_stall') || $old('category') === 'food' ? ' is-required' : '' ?>"
                id="uploadZone">
                <label for="documents" class="form-label fw-semibold">
                  <i class="bi bi-paperclip me-1"></i>
                  Food Hygiene Certificate or other documents
                  <span id="uploadRequiredFlag" class="mela-req<?= $old('is_food_stall') || $old('category') === 'food' ? '' : ' d-none' ?>">*</span>
                </label>
                <input type="file" class="form-control<?= $err('documents') ?>" id="documents"
                  name="documents[]" multiple
                  accept=".pdf,.jpg,.jpeg,.png,.heic,.webp,application/pdf,image/*"
                  aria-describedby="documents_help">
                <div class="form-text mb-0" id="documents_help">
                  Up to <?= esc($config->maxDocuments) ?> files,
                  max <?= esc(round($config->maxDocumentSizeKb / 1024)) ?>MB each. PDF or photo.
                  <strong id="uploadRequiredNote"
                    class="<?= $old('is_food_stall') || $old('category') === 'food' ? '' : 'd-none' ?>">
                    Required because you have marked this as a food stall.
                  </strong>
                </div>
                <?php if (isset($errors['documents'])): ?>
                  <div class="invalid-feedback d-block"><?= esc($errors['documents']) ?></div>
                <?php endif; ?>
              </div>
            </div>

            <!-- 4. Confirm -->
            <div class="mela-section">
              <div class="mela-section-head">
                <span class="mela-step-num">4</span>
                <h3>Confirm and submit</h3>
              </div>

              <div class="mela-confirm form-check">
                <input class="form-check-input<?= $err('confirmed_payment') ?>" type="checkbox"
                  value="1" id="confirmed_payment" name="confirmed_payment" required>
                <label class="form-check-label" for="confirmed_payment">
                  I confirm the &pound;<?= esc($config->fee) ?> stall fee has been paid to the
                  LCNL account <?= $req ?>
                </label>
                <?php if (isset($errors['confirmed_payment'])): ?>
                  <div class="invalid-feedback d-block"><?= esc($errors['confirmed_payment']) ?></div>
                <?php endif; ?>
              </div>

              <div class="mela-confirm form-check">
                <input class="form-check-input<?= $err('agreed_terms') ?>" type="checkbox"
                  value="1" id="agreed_terms" name="agreed_terms" required>
                <label class="form-check-label" for="agreed_terms">
                  I agree to all the terms and details listed above <?= $req ?>
                  <span class="d-block text-muted small">
                    Including that the &pound;<?= esc($config->fee) ?> fee is payable in full
                    even if you are later unable to attend, and that you may be asked to sign
                    a further disclaimer on the day.
                  </span>
                </label>
                <?php if (isset($errors['agreed_terms'])): ?>
                  <div class="invalid-feedback d-block"><?= esc($errors['agreed_terms']) ?></div>
                <?php endif; ?>
              </div>

              <div class="mb-4">
                <label for="comments" class="form-label fw-semibold">
                  Anything else we should know? <span class="text-muted fw-normal">(optional)</span>
                </label>
                <textarea class="form-control" id="comments" name="comments" rows="3"
                  maxlength="2000"><?= esc($old('comments')) ?></textarea>
              </div>

              <button type="submit" class="btn btn-brand btn-lg w-100">
                <i class="bi bi-send-fill me-1"></i> Submit stall booking
              </button>

              <p class="text-muted small mt-3 mb-0">
                We use these details only to organise the Mela and to pass on customer
                enquiries about your stall. Uploaded documents are visible only to the LCNL
                organising team. See our <a href="<?= base_url('privacy') ?>">privacy policy</a>.
              </p>
            </div>
          </form>
        </div>

        <div class="mt-4"><?= $this->include('mela/_contacts') ?></div>

      <?php endif; ?>

    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var category   = document.getElementById('category');
    var otherWrap  = document.getElementById('categoryOtherWrap');
    var otherInput = document.getElementById('category_other');
    var foodTick   = document.getElementById('is_food_stall');
    var company    = document.getElementById('company_name');
    var refEl      = document.getElementById('paymentRef');
    var errors     = document.getElementById('formErrors');
    var zone       = document.getElementById('uploadZone');
    var reqFlag    = document.getElementById('uploadRequiredFlag');
    var reqNote    = document.getElementById('uploadRequiredNote');

    // Send keyboard and screen reader users straight to the problem.
    if (errors) errors.focus();

    function isFood() {
      return (foodTick && foodTick.checked) || (category && category.value === 'food');
    }

    // "Other" reveals its free-text box; food selection highlights the upload.
    function sync() {
      if (category && otherWrap) {
        var isOther = category.value === 'other';
        otherWrap.classList.toggle('d-none', !isOther);
        if (otherInput) otherInput.required = isOther;
      }
      if (foodTick && category && category.value === 'food') foodTick.checked = true;

      var food = isFood();
      if (zone) zone.classList.toggle('is-required', food);
      if (reqFlag) reqFlag.classList.toggle('d-none', !food);
      if (reqNote) reqNote.classList.toggle('d-none', !food);
    }

    // Keep the payment reference in step with the company name, so the transfer
    // reference matches what shows on the bank statement.
    function syncReference() {
      if (!company || !refEl) return;
      var name = company.value.trim().replace(/\s+/g, ' ');
      refEl.textContent = <?= json_encode($config->paymentReferencePrefix, JSON_HEX_TAG) ?>
        + ' – ' + (name !== '' ? name : 'Your Company Name');
    }

    if (category) category.addEventListener('change', sync);
    if (foodTick) foodTick.addEventListener('change', sync);
    if (company)  company.addEventListener('input', syncReference);

    // Copy buttons on the bank details — most people are on a phone.
    document.querySelectorAll('.mela-copy').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var target = document.querySelector(btn.getAttribute('data-copy'));
        if (!target) return;
        var text = target.textContent.trim();
        var done = function () {
          var was = btn.textContent;
          btn.textContent = 'Copied';
          setTimeout(function () { btn.textContent = was; }, 1500);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(text).then(done, function () {});
        } else {
          var t = document.createElement('textarea');
          t.value = text;
          document.body.appendChild(t);
          t.select();
          try { document.execCommand('copy'); done(); } catch (e) {}
          document.body.removeChild(t);
        }
      });
    });

    sync();
    syncReference();
  });
</script>

<?= $this->endSection() ?>
