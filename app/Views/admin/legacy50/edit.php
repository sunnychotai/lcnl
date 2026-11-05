<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/** @var \Config\Legacy50 $config */
$errors = session('errors') ?? [];
$hasOld = old('first_name') !== null;

// Values come back from a failed save first, then from the pledge.
$val = static fn(string $field): string => (string) old($field, $pledge[$field] ?? '');
$err = static fn(string $field): string => isset($errors[$field]) ? ' is-invalid' : '';
$fieldError = static fn(string $field): string => isset($errors[$field])
    ? '<div class="invalid-feedback d-block">' . esc($errors[$field]) . '</div>' : '';

$giftAidOn = $hasOld ? (bool) old('gift_aid') : (bool) $pledge['gift_aid'];
$timing    = $val('payment_timing');
$title     = $val('title');
?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      <a href="<?= base_url('admin/legacy50') ?>" class="small text-decoration-none">
        <i class="bi bi-arrow-left"></i> All pledges
      </a>
      <h1 class="h3 fw-bold mt-2 mb-1">Pledge <?= esc($pledge['pledge_ref']) ?></h1>
      <p class="text-muted small mb-3">
        Pledged <?= esc(date('j M Y H:i', strtotime($pledge['created_at']))) ?>
        &middot; &pound;<?= esc(number_format((float) $pledge['amount'])) ?>
        &middot; Status: <strong><?= esc($pledge['status']) ?></strong>
        <?php if ($pledge['payment_received']): ?>
          (received <?= esc(date('j M Y', strtotime($pledge['payment_received_at']))) ?>
          by <?= esc($pledge['payment_marked_by']) ?>)
        <?php endif; ?>
      </p>

      <?php if (! empty($errors)): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="<?= base_url('admin/legacy50/update/' . $pledge['id']) ?>" novalidate>
        <?= csrf_field() ?>

        <!-- Donor -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <h2 class="h5 fw-bold mb-3">Donor</h2>
            <div class="row g-3">
              <div class="col-sm-3">
                <label for="title" class="form-label fw-semibold">Title</label>
                <select class="form-select<?= $err('title') ?>" id="title" name="title">
                  <option value="">&mdash;</option>
                  <?php foreach ($config->titles as $t): ?>
                    <option value="<?= esc($t, 'attr') ?>" <?= $title === $t ? 'selected' : '' ?>><?= esc($t) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-sm-4">
                <label for="first_name" class="form-label fw-semibold">First name</label>
                <input type="text" class="form-control<?= $err('first_name') ?>" id="first_name" name="first_name"
                  value="<?= esc($val('first_name')) ?>" maxlength="100" required>
                <?= $fieldError('first_name') ?>
              </div>
              <div class="col-sm-5">
                <label for="last_name" class="form-label fw-semibold">Last name</label>
                <input type="text" class="form-control<?= $err('last_name') ?>" id="last_name" name="last_name"
                  value="<?= esc($val('last_name')) ?>" maxlength="100" required>
                <?= $fieldError('last_name') ?>
              </div>
              <div class="col-sm-7">
                <label for="email" class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control<?= $err('email') ?>" id="email" name="email"
                  value="<?= esc($val('email')) ?>" maxlength="255" required>
                <?= $fieldError('email') ?>
              </div>
              <div class="col-sm-5">
                <label for="phone" class="form-label fw-semibold">Mobile</label>
                <input type="tel" class="form-control<?= $err('phone') ?>" id="phone" name="phone"
                  value="<?= esc($val('phone')) ?>" maxlength="30" required>
                <?= $fieldError('phone') ?>
              </div>
            </div>
            <div class="form-text mt-2">Changing the email does not re-send any emails.</div>
          </div>
        </div>

        <!-- Plaque and payment -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <h2 class="h5 fw-bold mb-3">Plaque and payment</h2>
            <div class="mb-3">
              <label for="plaque_name" class="form-label fw-semibold">Plaque name</label>
              <input type="text" class="form-control<?= $err('plaque_name') ?>" id="plaque_name" name="plaque_name"
                value="<?= esc($val('plaque_name')) ?>"
                maxlength="<?= esc($config->plaqueMaxLength, 'attr') ?>" required>
              <?= $fieldError('plaque_name') ?>
              <div class="form-text">Donor submitted: &ldquo;<?= esc($pledge['plaque_name_submitted']) ?>&rdquo;</div>
            </div>

            <div class="row g-3">
              <div class="col-sm-6">
                <label for="payment_reference" class="form-label fw-semibold">Payment reference</label>
                <input type="text" class="form-control text-uppercase<?= $err('payment_reference') ?>" id="payment_reference"
                  name="payment_reference" value="<?= esc($val('payment_reference')) ?>"
                  maxlength="<?= esc($config->paymentReferenceMaxLength, 'attr') ?>" required>
                <?= $fieldError('payment_reference') ?>
                <div class="form-text">What to look for on the LCF statement.</div>
              </div>
              <div class="col-sm-6">
                <span class="form-label fw-semibold d-block">At the time of pledging</span>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="payment_timing" id="timing_paid" value="paid"
                    <?= $timing === 'paid' ? 'checked' : '' ?>>
                  <label class="form-check-label" for="timing_paid">Said they had already paid</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="payment_timing" id="timing_will_pay" value="will_pay"
                    <?= $timing === 'will_pay' ? 'checked' : '' ?>>
                  <label class="form-check-label" for="timing_will_pay">Said they would pay soon</label>
                </div>
                <?= $fieldError('payment_timing') ?>
              </div>
            </div>
            <div class="form-text mt-2">To record the money arriving, use <strong>Mark paid</strong> on the pledge list.</div>
          </div>
        </div>

        <!-- Gift Aid -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <h2 class="h5 fw-bold mb-3">Gift Aid</h2>

            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" role="switch" id="gift_aid" name="gift_aid" value="1"
                <?= $giftAidOn ? 'checked' : '' ?>>
              <label class="form-check-label fw-semibold" for="gift_aid">Claim Gift Aid on this pledge</label>
            </div>

            <?php if ($pledge['gift_aid']): ?>
              <p class="small text-muted mb-3">
                Declared <?= esc(date('j M Y H:i', strtotime($pledge['gift_aid_declared_at']))) ?>
                (wording <?= esc($pledge['gift_aid_version']) ?>).
                Turning it off removes the declaration and the home address.
              </p>
            <?php else: ?>
              <div id="giftAidConfirm" class="alert alert-warning small mb-3<?= $giftAidOn ? '' : ' d-none' ?>">
                <p class="mb-2">
                  Only turn this on if the donor has made the declaration below to you (by phone, email or in person).
                  It will be recorded as declared today, under your name in the notes.
                </p>
                <ul class="mb-2">
                  <?php foreach ($config->giftAidStatements as $s): ?><li><?= esc($s) ?></li><?php endforeach; ?>
                </ul>
                <div class="form-check mb-0">
                  <input class="form-check-input<?= $err('gift_aid_confirmed') ?>" type="checkbox" value="1"
                    id="gift_aid_confirmed" name="gift_aid_confirmed" <?= old('gift_aid_confirmed') ? 'checked' : '' ?>>
                  <label class="form-check-label fw-semibold" for="gift_aid_confirmed">
                    The donor has confirmed this declaration
                  </label>
                </div>
                <?= $fieldError('gift_aid_confirmed') ?>
              </div>
            <?php endif; ?>

            <div id="giftAidAddress" class="row g-3<?= $giftAidOn ? '' : ' d-none' ?>">
              <div class="col-sm-4">
                <label for="house_name_number" class="form-label fw-semibold">House name or number</label>
                <input type="text" class="form-control<?= $err('house_name_number') ?>" id="house_name_number"
                  name="house_name_number" value="<?= esc($val('house_name_number')) ?>" maxlength="100">
                <?= $fieldError('house_name_number') ?>
              </div>
              <div class="col-sm-8">
                <label for="street" class="form-label fw-semibold">Street</label>
                <input type="text" class="form-control<?= $err('street') ?>" id="street" name="street"
                  value="<?= esc($val('street')) ?>" maxlength="150">
                <?= $fieldError('street') ?>
              </div>
              <div class="col-sm-8">
                <label for="town" class="form-label fw-semibold">Town or city</label>
                <input type="text" class="form-control<?= $err('town') ?>" id="town" name="town"
                  value="<?= esc($val('town')) ?>" maxlength="100">
                <?= $fieldError('town') ?>
              </div>
              <div class="col-sm-4">
                <label for="postcode" class="form-label fw-semibold">Postcode</label>
                <input type="text" class="form-control text-uppercase<?= $err('postcode') ?>" id="postcode" name="postcode"
                  value="<?= esc($val('postcode')) ?>" maxlength="10">
                <?= $fieldError('postcode') ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Comments and notes -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <div class="mb-3">
              <label for="comments" class="form-label fw-semibold">Donor comments</label>
              <textarea class="form-control<?= $err('comments') ?>" id="comments" name="comments" rows="3"
                maxlength="2000"><?= esc($val('comments')) ?></textarea>
              <?= $fieldError('comments') ?>
            </div>
            <div>
              <label for="admin_notes" class="form-label fw-semibold">Admin notes</label>
              <textarea class="form-control<?= $err('admin_notes') ?>" id="admin_notes" name="admin_notes" rows="5"
                maxlength="5000"><?= esc($val('admin_notes')) ?></textarea>
              <?= $fieldError('admin_notes') ?>
              <div class="form-text">
                Internal only &mdash; never shown to the donor. Each save adds a dated line saying who changed what.
              </div>
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-brand">Save changes</button>
        <a href="<?= base_url('admin/legacy50') ?>" class="btn btn-outline-secondary">Cancel</a>
      </form>

    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var toggle  = document.getElementById('gift_aid');
    var address = document.getElementById('giftAidAddress');
    var confirm = document.getElementById('giftAidConfirm');

    toggle.addEventListener('change', function () {
      address.classList.toggle('d-none', !toggle.checked);
      if (confirm) confirm.classList.toggle('d-none', !toggle.checked);
    });
  });
</script>

<?= $this->endSection() ?>
