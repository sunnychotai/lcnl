<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var \Config\Connexions $config
 * @var bool  $isOpen
 * @var bool  $isFull
 * @var array $placesLeft ['girl' => int|null, 'boy' => int|null]; null is no limit
 */
$errors = session('errors') ?? [];
$old    = static fn(string $k, $default = '') => old($k, $default);
$err    = static fn(string $field): string => isset($errors[$field]) ? ' is-invalid' : '';
$req    = '<span class="mela-req" aria-hidden="true">*</span>';

$fieldError = static function (string $field) use ($errors): string {
  return isset($errors[$field])
    ? '<div class="invalid-feedback d-block">' . esc($errors[$field]) . '</div>'
    : '';
};

$price = $config->ticketPrice;
$max   = $config->maxPerGender;
$flier = static fn(int $w): string => base_url($config->flier . '-' . $w . '.webp');

// Never offer more tickets than there are places left.
$maxFor = [];
foreach (['girl', 'boy'] as $g) {
  $maxFor[$g] = $placesLeft[$g] === null ? $max : min($max, $placesLeft[$g]);
}

$attendeeFields = [
  'first_name' => ['label' => 'First name', 'type' => 'text', 'max' => 100, 'auto' => 'off', 'col' => 'col-sm-6'],
  'last_name'  => ['label' => 'Surname', 'type' => 'text', 'max' => 100, 'auto' => 'off', 'col' => 'col-sm-6'],
  'email'      => ['label' => 'Email address', 'type' => 'email', 'max' => 255, 'auto' => 'off', 'col' => 'col-md-7'],
  'phone'      => ['label' => 'Mobile number', 'type' => 'tel', 'max' => 30, 'auto' => 'off', 'col' => 'col-md-5'],
];
?>

<?php ob_start(); ?>
<div class="mela-hero-pills justify-content-start">
  <span class="pill"><i class="bi bi-calendar-event"></i> <?= esc($config->eventLabel()) ?></span>
  <span class="pill"><i class="bi bi-clock"></i> <?= esc($config->times) ?></span>
  <span class="pill is-accent"><i class="bi bi-ticket-perforated"></i> &pound;<?= esc($price) ?> per person</span>
</div>
<?php $headerExtra = ob_get_clean(); ?>
<?= view('partials/page_header', [
  'overlay'    => 'rosewood',
  'eyebrow'    => 'LCNL Connexions & YLS',
  'heading'    => 'Singles Mix & Mingle',
  'subheading' => 'It’s that unexpected meeting that changes your life…',
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
            <img src="<?= $flier(480) ?>" srcset="<?= $flier(480) ?> 480w, <?= $flier(854) ?> 854w"
              sizes="(min-width: 768px) 40vw, 100vw" width="480" height="720" decoding="async"
              alt="Connexions Singles Mix &amp; Mingle flier: <?= esc($config->eventLabel(), 'attr') ?>, <?= esc($config->times, 'attr') ?>, at <?= esc($config->venueLine(), 'attr') ?>. &pound;<?= esc($price, 'attr') ?> per person including a welcome drink and nibbles. Ages 25 to 40. Registration from <?= esc($config->registrationFrom, 'attr') ?>.">
          </a>
          <p class="small text-muted text-center mt-2 mb-0">
            <i class="bi bi-arrows-fullscreen me-1"></i>Tap the flier to see it full size
          </p>
        </div>

        <div class="col-md-7">
          <h2 class="h3 fw-bold text-brand mb-3">An evening to meet new people</h2>
          <p>
            Join LCNL Connexions and the Young Lohana Society for a relaxed, welcoming evening of new connections and
            friendly conversation. The Connexions team will be on hand to make introductions.
          </p>
          <p class="cnx-eligibility">
            <i class="bi bi-people-fill me-1" aria-hidden="true"></i>
            This event is exclusively for <strong><?= esc($config->eligibility) ?></strong>.
          </p>

          <div class="mela-facts chopda-facts">
            <div class="mela-fact">
              <i class="bi bi-calendar-event"></i>
              <span class="label">Date</span>
              <span class="value"><?= esc($config->eventLabel()) ?></span>
            </div>
            <div class="mela-fact">
              <i class="bi bi-clock"></i>
              <span class="label">Time</span>
              <span class="value"><?= esc($config->times) ?></span>
              <span class="note">Registration from <?= esc($config->registrationFrom) ?></span>
            </div>
            <div class="mela-fact">
              <i class="bi bi-geo-alt"></i>
              <span class="label">Venue</span>
              <a class="value" href="https://www.google.com/maps/search/?api=1&amp;query=<?= esc(rawurlencode($config->venueLine()), 'attr') ?>"
                target="_blank" rel="noopener"><?= esc($config->venueLine()) ?></a>
            </div>
            <div class="mela-fact">
              <i class="bi bi-ticket-perforated"></i>
              <span class="label">Tickets</span>
              <span class="value">&pound;<?= esc($price) ?> per person</span>
              <span class="note">Includes a welcome drink and nibbles. Other drinks can be bought at the venue.</span>
            </div>
          </div>

          <?php if ($isOpen): ?>
            <div class="mela-deadline mb-2">
              <i class="bi bi-hourglass-split"></i>
              <span>Places are limited. Online booking closes <strong><?= esc($config->closingLabel()) ?></strong>.</span>
              <a href="#bookForm" class="btn btn-brand btn-sm ms-auto flex-shrink-0">
                Book now <i class="bi bi-arrow-down ms-1"></i>
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- ======================= Before you book ======================= -->
      <div class="xmas-important mb-4" role="note">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div>
          <strong>Tickets are non-refundable and non-exchangeable.</strong>
          Every ticket holder will be emailed a booking confirmation, which they must
          <strong>show on arrival</strong>. Entry may be refused if the details don&rsquo;t match.
        </div>
      </div>

      <!-- ======================= Form ======================= -->
      <?php if (! $isOpen): ?>

        <div class="mela-form-card mt-4">
          <div class="card-body p-4 p-md-5 text-center mela-closed">
            <i class="bi bi-ticket-perforated"></i>
            <h2 class="h4 fw-bold mt-3"><?= $isFull ? 'Fully booked' : ($config->isOpen() ? 'Booking opens soon' : 'Online booking has closed') ?></h2>
            <p class="text-muted mb-0"><?= esc($closedMessage) ?></p>
          </div>
        </div>

      <?php else: ?>

        <div class="mela-form-card mt-4" id="bookForm">
          <div class="mela-form-head">
            <h2 class="h4"><i class="bi bi-ticket-perforated me-2"></i>Book your tickets</h2>
            <p>Fields marked <span class="mela-req">*</span> are required.</p>
          </div>

          <form method="post" action="<?= esc(site_url($config->slug)) ?>" novalidate id="connexionsForm">
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
                <h3>Your details</h3>
              </div>

              <div class="row g-3">
                <div class="col-sm-6">
                  <label for="first_name" class="form-label fw-semibold">First name <?= $req ?></label>
                  <input type="text" class="form-control<?= $err('first_name') ?>" id="first_name" name="first_name"
                    value="<?= esc($old('first_name')) ?>" maxlength="100" autocomplete="given-name" required>
                  <?= $fieldError('first_name') ?>
                </div>

                <div class="col-sm-6">
                  <label for="last_name" class="form-label fw-semibold">Surname <?= $req ?></label>
                  <input type="text" class="form-control<?= $err('last_name') ?>" id="last_name" name="last_name"
                    value="<?= esc($old('last_name')) ?>" maxlength="100" autocomplete="family-name" required>
                  <?= $fieldError('last_name') ?>
                </div>

                <div class="col-md-7">
                  <label for="email" class="form-label fw-semibold">Email address <?= $req ?></label>
                  <input type="email" class="form-control<?= $err('email') ?>" id="email" name="email"
                    value="<?= esc($old('email')) ?>" maxlength="255" autocomplete="email" inputmode="email" required>
                  <div class="form-text">Your booking confirmation is sent here.</div>
                  <?= $fieldError('email') ?>
                </div>

                <div class="col-md-5">
                  <label for="phone" class="form-label fw-semibold">Mobile number <?= $req ?></label>
                  <input type="tel" class="form-control<?= $err('phone') ?>" id="phone" name="phone"
                    value="<?= esc($old('phone')) ?>" maxlength="30" autocomplete="tel" inputmode="tel" required>
                  <?= $fieldError('phone') ?>
                </div>

                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="is_member" name="is_member"
                      aria-describedby="member_help" <?= $old('is_member') ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="is_member">I am an LCNL member</label>
                  </div>
                  <div class="form-text" id="member_help">
                    You don&rsquo;t have to be a member to buy tickets or attend the event.
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Tickets and attendees -->
            <div class="mela-section">
              <div class="mela-section-head">
                <span class="mela-step-num">2</span>
                <h3>Tickets and attendees</h3>
              </div>

              <div class="row g-3">
                <?php foreach (['girl', 'boy'] as $g): ?>
                  <?php $label = $config->genders[$g]['one']; ?>
                  <div class="col-sm-6">
                    <label for="<?= $g ?>_tickets" class="form-label fw-semibold"><?= esc($label) ?> tickets</label>
                    <select class="form-select cnx-count<?= $err($g . '_tickets') . $err('tickets') ?>" id="<?= $g ?>_tickets"
                      name="<?= $g ?>_tickets" data-gender="<?= $g ?>" aria-describedby="<?= $g ?>_price"
                      <?= $maxFor[$g] === 0 ? 'disabled' : '' ?>>
                      <?php for ($n = 0; $n <= $maxFor[$g]; $n++): ?>
                        <option value="<?= $n ?>" <?= (int) $old($g . '_tickets', 0) === $n ? 'selected' : '' ?>><?= $n ?></option>
                      <?php endfor; ?>
                    </select>
                    <?php if ($maxFor[$g] === 0): ?>
                      <input type="hidden" name="<?= $g ?>_tickets" value="0">
                    <?php endif; ?>
                    <div class="form-text xmas-unit" id="<?= $g ?>_price">
                      <?php if ($maxFor[$g] === 0): ?>
                        <strong class="text-danger"><?= esc($label) ?> tickets are sold out.</strong>
                      <?php else: ?>
                        <strong>&pound;<?= esc($price) ?> each</strong>, up to <?= $max ?> per booking
                      <?php endif; ?>
                    </div>
                    <?= $fieldError($g . '_tickets') ?>
                  </div>
                <?php endforeach; ?>
              </div>
              <?= $fieldError('tickets') ?>

              <p class="small text-muted mt-3 mb-3 cnx-js-only" hidden>
                <i class="bi bi-info-circle me-1"></i>
                Choose your tickets and a box will open for each person. Coming yourself? Use
                <strong>&ldquo;Use my details&rdquo;</strong> to fill in your own box.
              </p>
              <p class="small text-muted mt-3 mb-3 cnx-no-js">
                <i class="bi bi-info-circle me-1"></i>
                Fill in one box for each ticket you have chosen, starting with Girl 1 and Boy 1. Any other boxes are ignored.
              </p>

              <div id="attendeeBoxes">
                <?php foreach (['girl', 'boy'] as $g): ?>
                  <?php for ($i = 1; $i <= $max; $i++): ?>
                    <?php $who = $config->genders[$g]['one'] . ' ' . $i; ?>
                    <fieldset class="cnx-attendee cnx-attendee-<?= $g ?>" data-gender="<?= $g ?>" data-pos="<?= $i ?>"
                      aria-labelledby="att_<?= $g ?>_<?= $i ?>_legend">
                      <div class="cnx-attendee-head">
                        <legend id="att_<?= $g ?>_<?= $i ?>_legend" class="cnx-attendee-title">
                          <i class="bi <?= $g === 'girl' ? 'bi-person-heart' : 'bi-person-fill' ?> me-1" aria-hidden="true"></i>
                          <?= esc($who) ?> attendee details
                        </legend>
                        <button type="button" class="btn btn-sm btn-outline-secondary cnx-use-mine cnx-js-only" hidden>
                          <i class="bi bi-person-check me-1" aria-hidden="true"></i>Use my details
                        </button>
                      </div>
                      <div class="row g-3">
                        <?php foreach ($attendeeFields as $f => $spec): ?>
                          <?php
                          $key  = 'attendees.' . $g . '.' . $i . '.' . $f;
                          $id   = 'att_' . $g . '_' . $i . '_' . $f;
                          ?>
                          <div class="<?= $spec['col'] ?>">
                            <label for="<?= $id ?>" class="form-label fw-semibold"><?= esc($spec['label']) ?> <?= $req ?></label>
                            <input type="<?= $spec['type'] ?>" class="form-control<?= $err($key) ?>" id="<?= $id ?>"
                              name="attendees[<?= $g ?>][<?= $i ?>][<?= $f ?>]" data-field="<?= $f ?>"
                              value="<?= esc($old($key)) ?>" maxlength="<?= $spec['max'] ?>" autocomplete="<?= $spec['auto'] ?>"
                              <?= $f === 'email' ? 'inputmode="email"' : ($f === 'phone' ? 'inputmode="tel"' : '') ?>>
                            <?= $fieldError($key) ?>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    </fieldset>
                  <?php endfor; ?>
                <?php endforeach; ?>
              </div>
            </div>

            <!-- 3. Confirm and pay -->
            <div class="mela-section">
              <div class="mela-section-head">
                <span class="mela-step-num">3</span>
                <h3>Check and pay</h3>
              </div>

              <div class="mela-confirm form-check mb-3">
                <input class="form-check-input<?= $err('eligible') ?>" type="checkbox" value="1"
                  id="eligible" name="eligible" <?= $old('eligible') ? 'checked' : '' ?> required>
                <label class="form-check-label fw-semibold" for="eligible">
                  I confirm that everyone I am booking tickets for is a Gujarati single aged 25&ndash;40 <?= $req ?>
                  <span class="d-block text-muted small fw-normal">
                    This event is only open to <?= esc($config->eligibility) ?>.
                  </span>
                </label>
                <?= $fieldError('eligible') ?>
              </div>

              <div class="xmas-important mb-3" role="note">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>
                  <strong>All information needs to be included. If any details are wrong, you may be refused admission
                  and no refunds will be given.</strong> So please make sure the details you enter are correct.
                </div>
              </div>

              <div class="xmas-summary" aria-live="polite">
                <h4 class="xmas-summary-title">Your order</h4>
                <div id="summaryLines">
                  <p class="text-muted small mb-0">Tickets are &pound;<?= esc($price) ?> each. Your total is shown on the secure payment page.</p>
                </div>
              </div>

              <div class="mela-confirm form-check mt-3 mb-3">
                <input class="form-check-input<?= $err('privacy_consent') ?>" type="checkbox" value="1"
                  id="privacy_consent" name="privacy_consent" <?= $old('privacy_consent') ? 'checked' : '' ?> required>
                <label class="form-check-label" for="privacy_consent">
                  I agree that LCNL may hold these details to manage this booking and to email each attendee their
                  confirmation <?= $req ?>
                  <span class="d-block text-muted small">
                    See our <a href="<?= base_url('privacy') ?>">privacy policy</a>.
                  </span>
                </label>
                <?= $fieldError('privacy_consent') ?>
              </div>

              <p class="small mb-3">
                <i class="bi bi-lock-fill me-1"></i>
                You&rsquo;ll pay by card on Stripe&rsquo;s secure payment page. Your places are held for
                <?= esc($config->holdMinutes) ?> minutes while you pay. Tickets are non-refundable.
              </p>

              <button type="submit" class="btn btn-brand btn-lg w-100" id="confirmBtn">
                <i class="bi bi-credit-card me-1"></i> <span id="confirmLabel">Continue to payment</span>
              </button>
            </div>
          </form>
        </div>

      <?php endif; ?>

      <div class="p-3 rounded border bg-light mt-4">
        <h2 class="h6 fw-bold text-brand mb-2">Questions?</h2>
        <p class="mb-0 small">
          Please contact
          <?php foreach ($config->contacts as $i => $c): ?>
            <?= $i > 0 ? ' or ' : '' ?><?= esc($c['name']) ?> on
            <a href="tel:<?= esc(str_replace(' ', '', $c['phone']), 'attr') ?>"><?= esc($c['phone']) ?></a>
          <?php endforeach; ?>,
          or email <a href="mailto:<?= esc($config->contactEmail, 'attr') ?>"><?= esc($config->contactEmail) ?></a>.
        </p>
      </div>

    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var errors = document.getElementById('formErrors');
    if (errors) errors.focus();

    var form = document.getElementById('connexionsForm');
    if (!form) return; // booking closed

    var price  = <?= json_encode($price) ?>;
    var counts = form.querySelectorAll('.cnx-count');
    var boxes  = form.querySelectorAll('.cnx-attendee');
    var lines  = document.getElementById('summaryLines');
    var label  = document.getElementById('confirmLabel');

    form.querySelectorAll('.cnx-js-only').forEach(function (el) { el.hidden = false; });
    form.querySelectorAll('.cnx-no-js').forEach(function (el) { el.hidden = true; });

    function count(gender) {
      var sel = document.getElementById(gender + '_tickets');
      return sel && !sel.disabled ? (parseInt(sel.value, 10) || 0) : 0;
    }
    function money(n) { return '£' + n.toLocaleString('en-GB'); }
    function plural(n, one, many) { return n + ' ' + (n === 1 ? one : many); }

    function update() {
      var n = { girl: count('girl'), boy: count('boy') };

      // One box per ticket. Hidden boxes are disabled so they aren't sent.
      boxes.forEach(function (box) {
        var on = parseInt(box.dataset.pos, 10) <= n[box.dataset.gender];
        box.hidden = !on;
        box.querySelectorAll('input').forEach(function (input) {
          input.disabled = !on;
          input.required = on;
        });
      });

      var total = (n.girl + n.boy) * price;
      var rows  = '';
      if (n.girl > 0) rows += '<div class="xmas-summary-row"><span>' + plural(n.girl, 'girl ticket', 'girl tickets') +
        ' &times; ' + money(price) + '</span><span>' + money(n.girl * price) + '</span></div>';
      if (n.boy > 0) rows += '<div class="xmas-summary-row"><span>' + plural(n.boy, 'boy ticket', 'boy tickets') +
        ' &times; ' + money(price) + '</span><span>' + money(n.boy * price) + '</span></div>';

      if (total === 0) {
        lines.innerHTML = '<p class="text-muted small mb-0">Choose your girl and/or boy tickets above.</p>';
        label.textContent = 'Continue to payment';
      } else {
        lines.innerHTML = rows + '<div class="xmas-summary-total"><span>Total to pay<small>' +
          plural(n.girl + n.boy, 'person', 'people') + '</small></span><span>' + money(total) + '</span></div>';
        label.textContent = 'Continue to payment – ' + money(total);
      }
    }

    // Copy the booker's own details into an attendee box.
    form.querySelectorAll('.cnx-use-mine').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var box = btn.closest('.cnx-attendee');
        ['first_name', 'last_name', 'email', 'phone'].forEach(function (f) {
          var from = document.getElementById(f);
          var to   = box.querySelector('[data-field="' + f + '"]');
          if (from && to) to.value = from.value;
        });
        var first = box.querySelector('[data-field="first_name"]');
        if (first && !first.value) first.focus();
      });
    });

    counts.forEach(function (sel) { sel.addEventListener('change', update); });
    update();
  });
</script>

<?= $this->endSection() ?>
