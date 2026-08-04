<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/** @var \Config\MelaStalls $config */
$errors = session('errors') ?? [];
$old    = static fn(string $k, $default = '') => old($k, $default);

$err = static function (string $field) use ($errors): string {
    return isset($errors[$field]) ? ' is-invalid' : '';
};
?>

<!-- Hero -->
<section class="hero-lcnl-watermark hero-overlay-ruby d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">Stall Holder Booking</h1>
    <p class="lead fs-5 mb-0"><?= esc($config->eventName) ?></p>
  </div>
</section>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-9">

      <?php if ($msg = session('error')): ?>
        <div class="alert alert-warning" role="alert"><?= esc($msg) ?></div>
      <?php endif; ?>

      <?php if (! empty($errors)): ?>
        <div class="alert alert-danger" role="alert" tabindex="-1" id="formErrors">
          <h2 class="h6 fw-bold mb-2">Please check the following</h2>
          <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
              <li><?= esc($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <!-- ============================= Briefing ============================= -->
      <div class="lcnl-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">

          <p>
            Thank you for your interest in booking a stall at the
            <strong><?= esc($config->eventName) ?></strong>, taking place on
            <strong><?= esc($config->eventLabel()) ?></strong> at:
          </p>

          <address class="mb-3">
            <?php foreach ($config->venueLines() as $i => $line): ?>
              <?= $i === 0 ? '<strong>' . esc($line) . '</strong>' : esc($line) ?><br>
            <?php endforeach; ?>
          </address>

          <p>
            The event runs from <strong><?= esc($config->eventTimes) ?></strong> and is open to
            people of all ages. Throughout the day there will be a variety of activities,
            including bingo, karaoke, sports, children&rsquo;s rides and much more.
          </p>

          <h2 class="h5 fw-bold text-brand mt-4">Stall booking</h2>
          <p>
            Bookings close at <strong>midnight on <?= esc($config->closingLabel()) ?></strong>.
            The cost of a stall is <strong>&pound;<?= esc($config->fee) ?></strong>.
          </p>
          <p>
            Each stall holder is allocated an area of approximately
            <strong><?= esc($config->stallSize) ?></strong>. Please note that
            <strong>no equipment is provided</strong>. If you require a table, gazebo, chairs,
            electricity or anything else, you must bring it yourself.
          </p>
          <p>
            Stalls are allocated by LCNL based on when you booked and where we feel the stall
            fits best &mdash; food stalls are kept together, clothing near each other, and so on.
          </p>

          <h2 class="h5 fw-bold text-brand mt-4">Set-up</h2>
          <p>
            You will have access to the venue from <strong><?= esc($config->setUpFrom) ?></strong>.
            One vehicle may be brought into the main car park during set-up, but it
            <strong>must be moved by <?= esc($config->carParkClear) ?></strong> to the designated
            grass parking area &mdash; the main car park by the hall must stay clear for the
            safety of visitors. Please plan your arrival and unloading accordingly.
          </p>

          <h2 class="h5 fw-bold text-brand mt-4">Insurance and responsibility</h2>
          <p>
            LCNL accepts <strong>no responsibility</strong> for the products or services offered
            at your stall. It is your responsibility to hold any insurance, licences or
            certifications required to operate it.
          </p>
          <p>
            Should we receive enquiries, complaints or requests relating to your stall, we will
            pass on the contact details you supply below so customers can contact you directly.
            LCNL will not investigate or deal with complaints on your behalf.
          </p>

          <h2 class="h5 fw-bold text-brand mt-4">Payment</h2>
          <p class="mb-2">
            The stall fee must be paid <strong>in advance</strong>, by bank transfer:
          </p>

          <div class="p-3 rounded border bg-light mb-3">
            <dl class="row mb-0">
              <dt class="col-sm-4">Account name</dt>
              <dd class="col-sm-8"><?= esc($config->bank['accountName']) ?></dd>
              <dt class="col-sm-4">Account number</dt>
              <dd class="col-sm-8"><?= esc($config->bank['accountNumber']) ?></dd>
              <dt class="col-sm-4">Sort code</dt>
              <dd class="col-sm-8"><?= esc($config->bank['sortCode']) ?></dd>
              <dt class="col-sm-4">Payment reference</dt>
              <dd class="col-sm-8">
                <span id="paymentRef" class="fw-bold text-brand">
                  <?= esc($config->paymentReference($old('company_name'))) ?>
                </span>
                <span class="d-block text-muted small">
                  Please use exactly this reference so we can match your payment.
                </span>
              </dd>
            </dl>
          </div>

          <p class="mb-0">
            <strong>Please pay before completing this form.</strong> Without payment the stall
            is not booked.
          </p>

          <h2 class="h5 fw-bold text-brand mt-4">Food stalls</h2>
          <p>
            If you are operating a food stall you must upload a copy of your current
            <strong>Food Hygiene Certificate</strong> below, and bring a copy with you on the
            day as you may be asked to present it.
          </p>

          <h2 class="h5 fw-bold text-brand mt-4">Disclaimer</h2>
          <p class="mb-0">
            On the day you may be asked to sign an additional disclaimer. By submitting this
            form you confirm you have read and accepted the conditions above, and you agree to
            pay the &pound;<?= esc($config->fee) ?> stall fee in full even if you are
            subsequently unable to attend.
          </p>
        </div>
      </div>

      <!-- ============================= Form ============================= -->
      <?php if (! $isOpen): ?>

        <div class="lcnl-card border-0 shadow-sm">
          <div class="card-body p-4 text-center">
            <i class="bi bi-clock-history text-brand" style="font-size:2.5rem;"></i>
            <h2 class="h4 fw-bold mt-3">
              <?= $isFull ? 'All stalls have been allocated' : 'Stall bookings are closed' ?>
            </h2>
            <p class="text-muted mb-4"><?= esc($closedMessage) ?></p>
            <?= $this->include('mela/_contacts') ?>
          </div>
        </div>

      <?php else: ?>

        <div class="lcnl-card border-0 shadow-sm">
          <div class="card-body p-4">
            <h2 class="h4 fw-bold text-brand mb-3">Booking form</h2>
            <p class="text-muted">
              Fields marked <span aria-hidden="true">*</span>
              <span class="visually-hidden">(required)</span> are required.
            </p>

            <form method="post" action="<?= esc(current_url()) ?>" enctype="multipart/form-data" novalidate>
              <?= csrf_field() ?>

              <!-- Honeypot: real people never see or fill this. -->
              <div class="d-none" aria-hidden="true">
                <label for="website">Leave this field blank</label>
                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
              </div>

              <div class="mb-3">
                <label for="company_name" class="form-label fw-semibold">
                  Company or stall name <span aria-hidden="true">*</span>
                </label>
                <input type="text" class="form-control<?= $err('company_name') ?>" id="company_name"
                  name="company_name" value="<?= esc($old('company_name')) ?>" maxlength="200" required
                  <?= isset($errors['company_name']) ? 'aria-describedby="company_name_err" aria-invalid="true"' : '' ?>>
                <div class="form-text">If you are trading as an individual, please use your own name.</div>
                <?php if (isset($errors['company_name'])): ?>
                  <div class="invalid-feedback d-block" id="company_name_err"><?= esc($errors['company_name']) ?></div>
                <?php endif; ?>
              </div>

              <div class="mb-3">
                <label for="category" class="form-label fw-semibold">
                  Type of stall <span aria-hidden="true">*</span>
                </label>
                <select class="form-select<?= $err('category') ?>" id="category" name="category" required
                  <?= isset($errors['category']) ? 'aria-describedby="category_err" aria-invalid="true"' : '' ?>>
                  <option value="">Please choose&hellip;</option>
                  <?php foreach ($config->categories as $value => $label): ?>
                    <option value="<?= esc($value) ?>" <?= $old('category') === $value ? 'selected' : '' ?>>
                      <?= esc($label) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <div class="form-text">This helps us group similar stalls together when allocating pitches.</div>
                <?php if (isset($errors['category'])): ?>
                  <div class="invalid-feedback d-block" id="category_err"><?= esc($errors['category']) ?></div>
                <?php endif; ?>
              </div>

              <div class="mb-3<?= $old('category') === 'other' ? '' : ' d-none' ?>" id="categoryOtherWrap">
                <label for="category_other" class="form-label fw-semibold">
                  Please specify the type of stall <span aria-hidden="true">*</span>
                </label>
                <input type="text" class="form-control<?= $err('category_other') ?>" id="category_other"
                  name="category_other" value="<?= esc($old('category_other')) ?>" maxlength="200">
                <?php if (isset($errors['category_other'])): ?>
                  <div class="invalid-feedback d-block"><?= esc($errors['category_other']) ?></div>
                <?php endif; ?>
              </div>

              <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" value="1" id="is_food_stall" name="is_food_stall"
                  <?= $old('is_food_stall') ? 'checked' : '' ?>>
                <label class="form-check-label fw-semibold" for="is_food_stall">
                  This stall sells food or drink
                </label>
                <div class="form-text">
                  Tick this if any food or drink is sold or given away. A current Food Hygiene
                  Certificate must then be uploaded below.
                </div>
              </div>

              <div class="mb-3">
                <label for="items_description" class="form-label fw-semibold">
                  Brief details of items being sold or exhibited <span aria-hidden="true">*</span>
                </label>
                <textarea class="form-control<?= $err('items_description') ?>" id="items_description"
                  name="items_description" rows="3" maxlength="2000" required><?= esc($old('items_description')) ?></textarea>
                <?php if (isset($errors['items_description'])): ?>
                  <div class="invalid-feedback d-block"><?= esc($errors['items_description']) ?></div>
                <?php endif; ?>
              </div>

              <hr class="my-4">
              <h3 class="h6 fw-bold text-brand">Contact details</h3>
              <p class="text-muted small">
                These are the details we pass on if a customer needs to contact you about your stall.
              </p>

              <div class="row g-3">
                <div class="col-md-6">
                  <label for="contact_name" class="form-label fw-semibold">
                    Stall holder contact name <span aria-hidden="true">*</span>
                  </label>
                  <input type="text" class="form-control<?= $err('contact_name') ?>" id="contact_name"
                    name="contact_name" value="<?= esc($old('contact_name')) ?>" maxlength="150"
                    autocomplete="name" required>
                  <?php if (isset($errors['contact_name'])): ?>
                    <div class="invalid-feedback d-block"><?= esc($errors['contact_name']) ?></div>
                  <?php endif; ?>
                </div>

                <div class="col-md-6">
                  <label for="contact_phone" class="form-label fw-semibold">
                    Contact number <span aria-hidden="true">*</span>
                  </label>
                  <input type="tel" class="form-control<?= $err('contact_phone') ?>" id="contact_phone"
                    name="contact_phone" value="<?= esc($old('contact_phone')) ?>" maxlength="30"
                    autocomplete="tel" inputmode="tel" required>
                  <?php if (isset($errors['contact_phone'])): ?>
                    <div class="invalid-feedback d-block"><?= esc($errors['contact_phone']) ?></div>
                  <?php endif; ?>
                </div>

                <div class="col-md-6">
                  <label for="contact_email" class="form-label fw-semibold">
                    Email address <span aria-hidden="true">*</span>
                  </label>
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
                  <div class="form-text">Helps us clear the main car park by <?= esc($config->carParkClear) ?>.</div>
                </div>
              </div>

              <hr class="my-4">
              <h3 class="h6 fw-bold text-brand">Documents</h3>

              <div class="mb-3">
                <label for="documents" class="form-label fw-semibold">
                  Food Hygiene Certificate or other documents
                </label>
                <input type="file" class="form-control<?= $err('documents') ?>" id="documents"
                  name="documents[]" multiple
                  accept=".pdf,.jpg,.jpeg,.png,.heic,.webp,application/pdf,image/*"
                  aria-describedby="documents_help">
                <div class="form-text" id="documents_help">
                  Up to <?= esc($config->maxDocuments) ?> files, max
                  <?= esc(round($config->maxDocumentSizeKb / 1024)) ?>MB each.
                  PDF or photo. <strong>Required for food stalls.</strong>
                  You can photograph the certificate with your phone.
                </div>
                <?php if (isset($errors['documents'])): ?>
                  <div class="invalid-feedback d-block"><?= esc($errors['documents']) ?></div>
                <?php endif; ?>
              </div>

              <hr class="my-4">
              <h3 class="h6 fw-bold text-brand">Confirmations</h3>

              <div class="mb-3 form-check">
                <input class="form-check-input<?= $err('confirmed_payment') ?>" type="checkbox" value="1"
                  id="confirmed_payment" name="confirmed_payment" required>
                <label class="form-check-label" for="confirmed_payment">
                  I confirm the &pound;<?= esc($config->fee) ?> stall fee has been paid to the
                  LCNL account <span aria-hidden="true">*</span>
                </label>
                <?php if (isset($errors['confirmed_payment'])): ?>
                  <div class="invalid-feedback d-block"><?= esc($errors['confirmed_payment']) ?></div>
                <?php endif; ?>
              </div>

              <div class="mb-4 form-check">
                <input class="form-check-input<?= $err('agreed_terms') ?>" type="checkbox" value="1"
                  id="agreed_terms" name="agreed_terms" required>
                <label class="form-check-label" for="agreed_terms">
                  I agree to all the terms and details listed above <span aria-hidden="true">*</span>
                </label>
                <?php if (isset($errors['agreed_terms'])): ?>
                  <div class="invalid-feedback d-block"><?= esc($errors['agreed_terms']) ?></div>
                <?php endif; ?>
              </div>

              <div class="mb-4">
                <label for="comments" class="form-label fw-semibold">
                  Any comments <span class="text-muted fw-normal">(optional)</span>
                </label>
                <textarea class="form-control" id="comments" name="comments" rows="3"
                  maxlength="2000"><?= esc($old('comments')) ?></textarea>
              </div>

              <p class="text-muted small">
                We use these details only to organise the Mela and to pass on customer enquiries
                about your stall. Documents you upload are visible only to the LCNL organising
                team. See our <a href="<?= base_url('privacy') ?>">privacy policy</a>.
              </p>

              <button type="submit" class="btn btn-brand btn-lg w-100">
                <i class="bi bi-send me-1"></i> Submit stall booking
              </button>
            </form>
          </div>
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

    // Send keyboard and screen reader users straight to the problem.
    if (errors) errors.focus();

    // "Other" reveals its free-text box and makes it required.
    function syncCategory() {
      if (!category || !otherWrap) return;
      var isOther = category.value === 'other';
      otherWrap.classList.toggle('d-none', !isOther);
      if (otherInput) otherInput.required = isOther;

      // Choosing the food category implies a food stall.
      if (foodTick && category.value === 'food') foodTick.checked = true;
    }

    // Keep the displayed payment reference in step with the company name, so
    // the reference on the transfer matches what the bank statement shows.
    function syncReference() {
      if (!company || !refEl) return;
      var name = company.value.trim().replace(/\s+/g, ' ');
      refEl.textContent = <?= json_encode($config->paymentReferencePrefix, JSON_HEX_TAG) ?>
        + ' – ' + (name !== '' ? name : 'Your Company Name');
    }

    if (category) category.addEventListener('change', syncCategory);
    if (company) company.addEventListener('input', syncReference);

    syncCategory();
    syncReference();
  });
</script>

<?= $this->endSection() ?>
