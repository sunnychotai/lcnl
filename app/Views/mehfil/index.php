<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var \Config\ChristmasMehfil $config
 * @var bool     $isOpen
 * @var bool     $isFull
 * @var int|null $seatsLeft null when there is no cap
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

$early   = $config->isEarlyBird();
$prices  = $config->currentPrices();
$size    = $config->tableSize;
$pound   = static fn(int|float $n): string => '&pound;' . number_format((float) $n);

// Never offer more than is left, so a table cannot be chosen once fewer than ten seats remain.
$maxInd    = $seatsLeft === null ? $config->maxIndividual : min($config->maxIndividual, $seatsLeft);
$maxTables = $seatsLeft === null ? $config->maxTables : min($config->maxTables, intdiv($seatsLeft, $size));

$flier = static fn(int $w): string => base_url($config->flier . '-' . $w . '.webp');

// The two price bands, in order. Before the cut-off the standard band is
// "coming"; after it the early bird band has "ended".
$bands = [
  'early' => [
    'title' => 'Early bird',
    'when'  => $early ? 'Book by ' . $config->earlyBirdLabel() : 'Ended ' . $config->earlyBirdLabel(),
    'state' => $early ? 'is-current' : 'is-ended',
  ],
  'standard' => [
    'title' => 'Standard',
    'when'  => 'From ' . $config->standardFromLabel(),
    'state' => $early ? 'is-coming' : 'is-current',
  ],
];
$otherBand = $config->prices[$early ? 'standard' : 'early'];
?>

<?php ob_start(); ?>
<div class="mela-hero-pills justify-content-start">
  <span class="pill"><i class="bi bi-calendar-event"></i> <?= esc($config->eventLabel()) ?></span>
  <span class="pill"><i class="bi bi-clock"></i> <?= esc($config->times) ?></span>
  <span class="pill is-accent"><i class="bi bi-ticket-perforated"></i>
    <?= $early ? 'Early bird ' : '' ?><?= $pound($prices['individual']) ?> per person</span>
</div>
<?php $headerExtra = ob_get_clean(); ?>
<?= view('partials/page_header', [
  'overlay'    => 'darkred',
  'eyebrow'    => 'Celebrating LCNL’s 50th Golden Jubilee',
  'heading'    => 'Christmas Mehfil 2026',
  'subheading' => 'Live music, dinner and festive fun with family and friends',
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
              sizes="(min-width: 768px) 40vw, 100vw" width="480" height="640" decoding="async"
              alt="Christmas Mehfil flier: <?= esc($config->eventLabel(), 'attr') ?>, <?= esc($config->times, 'attr') ?>, at <?= esc($config->venueLine(), 'attr') ?>. Early bird tickets until 5 December £270 for a table of 10 or £30 per person; from 6 December £300 a table or £35 per person. Vegetarian dinner by Desi Delite and drinks included, live band by RKB Entertainment, Christmas bingo, charity raffle, best dressed competitions.">
          </a>
          <p class="small text-muted text-center mt-2 mb-0">
            <i class="bi bi-arrows-fullscreen me-1"></i>Tap the flier to see it full size
          </p>
        </div>

        <div class="col-md-7">
          <h2 class="h3 fw-bold text-brand mb-3">Join us for the Christmas Mehfil</h2>
          <p>
            Join us for a fantastic evening of festive celebrations, live music, delicious food and plenty of
            Christmas cheer as we continue celebrating LCNL&rsquo;s <strong>50th Golden Jubilee</strong>!
          </p>
          <p>
            Get together with family and friends for an unforgettable Christmas Mehfil, featuring a live band from
            <strong>RKB Entertainment</strong> performing Christmas favourites, Bollywood classics and the latest hits.
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
            </div>
            <div class="mela-fact">
              <i class="bi bi-geo-alt"></i>
              <span class="label">Venue</span>
              <a class="value" href="https://www.google.com/maps/search/?api=1&amp;query=<?= esc(rawurlencode($config->venueLine()), 'attr') ?>"
                target="_blank" rel="noopener"><?= esc($config->venue['name']) ?>, <?= esc($config->venue['address1']) ?>, <?= esc($config->venue['address2']) ?> <?= esc($config->venue['postcode']) ?></a>
            </div>
            <div class="mela-fact">
              <i class="bi bi-ticket-perforated"></i>
              <span class="label">Tickets</span>
              <span class="value"><?= $pound($prices['individual']) ?> per person</span>
              <span class="value"><?= $pound($prices['table']) ?> for a table of <?= $size ?></span>
              <?php if ($early): ?>
                <span class="note">Early bird prices until <?= esc($config->earlyBirdLabel()) ?></span>
              <?php endif; ?>
            </div>
          </div>

          <?php if ($isOpen): ?>
            <div class="mela-deadline mb-2">
              <i class="bi bi-hourglass-split"></i>
              <span>
                <?php if ($early): ?>
                  Early bird prices end <strong><?= esc($config->earlyBirdLabel()) ?></strong>
                <?php else: ?>
                  Online booking closes <strong><?= esc($config->closingLabel()) ?></strong>
                <?php endif; ?>
              </span>
              <a href="#bookForm" class="btn btn-brand btn-sm ms-auto flex-shrink-0">
                Book tickets <i class="bi bi-arrow-down ms-1"></i>
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- ======================= What's in store ======================= -->
      <section class="xmas-panel mb-4" aria-labelledby="inStoreHeading">
        <h2 id="inStoreHeading" class="h4 fw-bold text-brand mb-3">
          <i class="bi bi-stars me-1"></i> What&rsquo;s in store?
        </h2>
        <ul class="xmas-instore">
          <li><i class="bi bi-mic-fill"></i><span>Live music by <strong>RKB Entertainment</strong></span></li>
          <li><i class="bi bi-egg-fried"></i><span>Delicious <strong>vegetarian dinner by Desi Delite</strong> and drinks included</span></li>
          <li><i class="bi bi-music-note-beamed"></i><span>Christmas songs, Bollywood classics and new hits</span></li>
          <li><i class="bi bi-grid-3x3-gap-fill"></i><span>Christmas Bingo</span></li>
          <li><i class="bi bi-ticket-detailed"></i><span>Charity Raffle</span></li>
          <li><i class="bi bi-trophy-fill"></i><span>Best Dressed Male and Female competitions</span></li>
        </ul>

        <div class="row g-3 mt-1">
          <div class="col-md-6">
            <div class="xmas-note h-100">
              <h3><i class="bi bi-snow me-1"></i> Dress code</h3>
              <p class="mb-0">
                Get into the festive spirit with your favourite <strong>Christmas outfit or Christmas jumper</strong>!
                There will be prizes for our best-dressed guests, so come dressed to impress!
              </p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="xmas-note h-100">
              <h3><i class="bi bi-gift me-1"></i> Charity raffle</h3>
              <p class="mb-2">We will be holding a charity raffle on the night with some fantastic prizes up for grabs!</p>
              <p class="mb-2">
                <?php foreach ($config->raffle as $i => $bundle): ?>
                  <?= $i > 0 ? ' &middot; ' : '' ?><strong><?= esc($bundle) ?></strong>
                <?php endforeach; ?>
              </p>
              <p class="mb-0 small"><i class="bi bi-cash-coin me-1"></i>Please remember to <strong>bring cash</strong> to buy your raffle tickets on the night.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- ======================= Prices ======================= -->
      <section class="mb-4" aria-labelledby="pricesHeading">
        <h2 id="pricesHeading" class="h4 fw-bold text-brand mb-3">
          <i class="bi bi-ticket-perforated me-1"></i> Ticket prices
        </h2>
        <div class="xmas-prices">
          <?php foreach ($bands as $key => $band): ?>
            <div class="xmas-band <?= $band['state'] ?>">
              <div class="xmas-band-head">
                <span class="xmas-band-title"><?= esc($band['title']) ?></span>
                <?php if ($band['state'] === 'is-current'): ?>
                  <span class="xmas-band-badge">Now</span>
                <?php endif; ?>
              </div>
              <p class="xmas-band-when"><?= esc($band['when']) ?></p>
              <ul>
                <li><span class="amt"><?= $pound($config->prices[$key]['individual']) ?></span> per person (individual ticket)</li>
                <li><span class="amt"><?= $pound($config->prices[$key]['table']) ?></span> for a table of <?= $size ?></li>
              </ul>
            </div>
          <?php endforeach; ?>
        </div>
        <p class="small text-muted mt-2 mb-0">
          <?php if ($early): ?>
            Book early to take advantage of our early bird prices and secure your place! You keep the price you book at,
            as long as you pay within <?= esc($config->payWithinHours) ?> hours.
          <?php else: ?>
            Early bird prices have now ended.
          <?php endif; ?>
        </p>
      </section>

      <!-- ======================= How to book ======================= -->
      <section class="xmas-panel mb-4" aria-labelledby="howHeading">
        <h2 id="howHeading" class="h4 fw-bold text-brand mb-3">
          <i class="bi bi-list-check me-1"></i> How to book your tickets
        </h2>
        <ol class="xmas-steps">
          <li>Complete the booking form below with your details and the tickets you would like.</li>
          <li>Once you submit the form, you will be shown LCNL&rsquo;s bank account details and your
            <strong>unique payment reference</strong> (for example <strong>XMAS-SUNCHO</strong>). We will email them to you too.</li>
          <li>Make your payment directly to the LCNL bank account, using your reference so we can match your payment
            to your booking.</li>
        </ol>
        <div class="xmas-important" role="note">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <div>
            <strong>Important: payment must be received within <?= esc($config->payWithinHours) ?> hours of booking.</strong>
            If it is not, your order will be cancelled and your tickets will be made available to others.
            Your booking is only confirmed once your payment has been received.
          </div>
        </div>
      </section>

      <!-- ======================= Form ======================= -->
      <?php if (! $isOpen): ?>

        <div class="mela-form-card mt-4">
          <div class="card-body p-4 p-md-5 text-center mela-closed">
            <i class="bi bi-ticket-perforated"></i>
            <h2 class="h4 fw-bold mt-3"><?= $isFull ? 'Sold out' : 'Online booking has closed' ?></h2>
            <p class="text-muted mb-0"><?= esc($closedMessage) ?></p>
          </div>
        </div>

      <?php else: ?>

        <div class="mela-form-card mt-4" id="bookForm">
          <div class="mela-form-head">
            <h2 class="h4"><i class="bi bi-ticket-perforated me-2"></i>Book your tickets</h2>
            <p>Fields marked <span class="mela-req">*</span> are required.</p>
          </div>

          <form method="post" action="<?= esc(site_url($config->slug)) ?>" novalidate id="mehfilForm">
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
                  <label for="last_name" class="form-label fw-semibold">Surname <?= $req ?></label>
                  <input type="text" class="form-control<?= $err('last_name') ?>" id="last_name" name="last_name"
                    value="<?= esc($old('last_name')) ?>" maxlength="100" autocomplete="family-name" required>
                  <?= $fieldError('last_name') ?>
                </div>

                <div class="col-md-7">
                  <label for="email" class="form-label fw-semibold">Email address <?= $req ?></label>
                  <input type="email" class="form-control<?= $err('email') ?>" id="email" name="email"
                    value="<?= esc($old('email')) ?>" maxlength="255" autocomplete="email" inputmode="email" required>
                  <div class="form-text">Your booking and payment details are sent here.</div>
                  <?= $fieldError('email') ?>
                </div>

                <div class="col-md-5">
                  <label for="phone" class="form-label fw-semibold">Mobile number <?= $req ?></label>
                  <input type="tel" class="form-control<?= $err('phone') ?>" id="phone" name="phone"
                    value="<?= esc($old('phone')) ?>" maxlength="30" autocomplete="tel" inputmode="tel" required>
                  <?= $fieldError('phone') ?>
                </div>

                <div class="col-sm-5 col-md-4">
                  <label for="postcode" class="form-label fw-semibold">Postcode <?= $req ?></label>
                  <input type="text" class="form-control text-uppercase<?= $err('postcode') ?>" id="postcode" name="postcode"
                    value="<?= esc($old('postcode')) ?>" maxlength="10" autocomplete="postal-code" required>
                  <?= $fieldError('postcode') ?>
                </div>

                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="is_member" name="is_member"
                      aria-describedby="member_help" <?= $old('is_member') ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="is_member">I am an LCNL member</label>
                  </div>
                  <div class="form-text" id="member_help">
                    You do not need to be a member to buy tickets. This just helps us know who is coming.
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Tickets -->
            <div class="mela-section">
              <div class="mela-section-head">
                <span class="mela-step-num">2</span>
                <h3>Your tickets</h3>
              </div>

              <div class="row g-3">
                <div class="col-sm-6">
                  <label for="individual_tickets" class="form-label fw-semibold">Individual tickets</label>
                  <select class="form-select<?= $err('individual_tickets') . $err('tickets') ?>" id="individual_tickets"
                    name="individual_tickets" aria-describedby="individual_price">
                    <?php for ($n = 0; $n <= $maxInd; $n++): ?>
                      <option value="<?= $n ?>" <?= (int) $old('individual_tickets', 0) === $n ? 'selected' : '' ?>><?= $n ?></option>
                    <?php endfor; ?>
                  </select>
                  <div class="form-text xmas-unit" id="individual_price">
                    <strong><?= $pound($prices['individual']) ?> each</strong>
                    <span class="xmas-unit-other"><?= $early ? 'then ' : 'was ' ?><?= $pound($otherBand['individual']) ?><?= $early ? ' from ' . esc($config->standardFromLabel()) : '' ?></span>
                  </div>
                  <?= $fieldError('individual_tickets') ?>
                </div>

                <div class="col-sm-6">
                  <label for="tables" class="form-label fw-semibold">Tables of <?= $size ?></label>
                  <select class="form-select<?= $err('tables') . $err('tickets') ?>" id="tables" name="tables"
                    aria-describedby="table_price" <?= $maxTables === 0 ? 'disabled' : '' ?>>
                    <?php for ($n = 0; $n <= $maxTables; $n++): ?>
                      <option value="<?= $n ?>" <?= (int) $old('tables', 0) === $n ? 'selected' : '' ?>><?= $n ?></option>
                    <?php endfor; ?>
                  </select>
                  <?php if ($maxTables === 0): ?>
                    <input type="hidden" name="tables" value="0">
                  <?php endif; ?>
                  <div class="form-text xmas-unit" id="table_price">
                    <?php if ($maxTables === 0): ?>
                      Tables are sold out; individual tickets are still available.
                    <?php else: ?>
                      <strong><?= $pound($prices['table']) ?> per table</strong>
                      <span class="xmas-unit-other"><?= $early ? 'then ' : 'was ' ?><?= $pound($otherBand['table']) ?><?= $early ? ' from ' . esc($config->standardFromLabel()) : '' ?></span>
                    <?php endif; ?>
                  </div>
                  <?= $fieldError('tables') ?>
                </div>
              </div>
              <?= $fieldError('tickets') ?>

              <p class="xmas-hint mt-3 mb-0" id="tableHint" hidden></p>
              <p class="small text-muted mt-3 mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Booking a table? Just let your guests know; you don&rsquo;t need to give us their names.
              </p>
            </div>

            <!-- 3. Confirm -->
            <div class="mela-section">
              <div class="mela-section-head">
                <span class="mela-step-num">3</span>
                <h3>Check and confirm</h3>
              </div>

              <div class="xmas-summary" aria-live="polite">
                <h4 class="xmas-summary-title">Your order</h4>
                <div id="summaryLines">
                  <p class="text-muted small mb-0">Your total will be shown on the next page.</p>
                </div>
              </div>

              <p class="small mt-3 mb-3">
                <i class="bi bi-bank me-1"></i>
                Next, we&rsquo;ll show you LCNL&rsquo;s bank details and your payment reference.
                <strong>Please pay within <?= esc($config->payWithinHours) ?> hours</strong> or your order will be cancelled.
              </p>

              <div class="mela-confirm form-check">
                <input class="form-check-input<?= $err('privacy_consent') ?>" type="checkbox" value="1"
                  id="privacy_consent" name="privacy_consent" <?= $old('privacy_consent') ? 'checked' : '' ?> required>
                <label class="form-check-label" for="privacy_consent">
                  I agree that LCNL may hold my details to manage my booking for the Christmas Mehfil <?= $req ?>
                  <span class="d-block text-muted small">
                    See our <a href="<?= base_url('privacy') ?>">privacy policy</a>.
                  </span>
                </label>
                <?= $fieldError('privacy_consent') ?>
              </div>

              <button type="submit" class="btn btn-brand btn-lg w-100 mt-2" id="confirmBtn">
                <i class="bi bi-check2-circle me-1"></i> <span id="confirmLabel">Confirm booking</span>
              </button>
            </div>
          </form>
        </div>

      <?php endif; ?>

      <p class="text-center fw-semibold text-brand mt-4">
        We look forward to welcoming you, your family and friends for a wonderful evening of music, laughter,
        festive fun and celebrations!
      </p>

      <div class="p-3 rounded border bg-light mt-3">
        <h2 class="h6 fw-bold text-brand mb-2">Questions?</h2>
        <p class="mb-0 small">
          Please email <a href="mailto:<?= esc($config->contactEmail, 'attr') ?>"><?= esc($config->contactEmail) ?></a>.
        </p>
      </div>

    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var errors = document.getElementById('formErrors');
    if (errors) errors.focus();

    var ind = document.getElementById('individual_tickets');
    var tab = document.getElementById('tables');
    if (!ind || !tab) return; // booking closed

    // Prices in force now. The server prices the booking itself; this is only a preview.
    var price = <?= json_encode(['individual' => $prices['individual'], 'table' => $prices['table'], 'size' => $size, 'early' => $early], JSON_HEX_TAG) ?>;
    var lines = document.getElementById('summaryLines');
    var label = document.getElementById('confirmLabel');
    var hint  = document.getElementById('tableHint');

    function money(n) { return '£' + n.toLocaleString('en-GB'); }
    function plural(n, one, many) { return n + ' ' + (n === 1 ? one : many); }
    function esc(s) { return String(s).replace(/[&<>"]/g, function (c) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }); }

    function update() {
      var i = parseInt(ind.value, 10) || 0;
      var t = parseInt(tab.value, 10) || 0;
      var total = i * price.individual + t * price.table;
      var people = i + t * price.size;
      var rows = '';

      if (i > 0) {
        rows += '<div class="xmas-summary-row"><span>' + esc(plural(i, 'individual ticket', 'individual tickets')) +
          ' &times; ' + money(price.individual) + '</span><span>' + money(i * price.individual) + '</span></div>';
      }
      if (t > 0) {
        rows += '<div class="xmas-summary-row"><span>' + esc(plural(t, 'table', 'tables')) + ' of ' + price.size +
          ' &times; ' + money(price.table) + '</span><span>' + money(t * price.table) + '</span></div>';
      }

      if (total === 0) {
        lines.innerHTML = '<p class="text-muted small mb-0">Choose your individual tickets and/or tables above.</p>';
        label.textContent = 'Confirm booking';
      } else {
        lines.innerHTML = rows +
          '<div class="xmas-summary-total"><span>Total to pay<small>' + esc(plural(people, 'person', 'people')) +
          (price.early ? ' &middot; early bird price' : '') + '</small></span><span>' + money(total) + '</span></div>';
        label.textContent = 'Confirm booking – ' + money(total);
      }

      // Individual tickets that cost as much as a table: point at the better deal.
      if (i > 0 && i * price.individual >= price.table && !tab.disabled) {
        hint.innerHTML = '<i class="bi bi-lightbulb-fill me-1"></i>' + esc(plural(i, 'individual ticket', 'individual tickets')) +
          ' cost ' + money(i * price.individual) + '; a table of ' + price.size + ' costs ' + money(price.table) + '.';
        hint.hidden = false;
      } else {
        hint.hidden = true;
      }
    }

    ind.addEventListener('change', update);
    tab.addEventListener('change', update);
    update();
  });
</script>

<?= $this->endSection() ?>
