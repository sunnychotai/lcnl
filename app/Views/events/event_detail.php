<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$requiresReg = !empty($event['requires_registration']);
$regOpen     = !empty($event['registration_open']);

$maxRegs      = (int) ($event['max_registrations']    ?? 0);
$maxHeads     = (int) ($event['max_headcount']         ?? 0);
$currentRegs  = (int) ($event['current_registrations'] ?? 0);
$currentHeads = (int) ($event['current_headcount']     ?? 0);

$regPercent  = ($maxRegs  > 0) ? min(100, round(($currentRegs  / $maxRegs)  * 100)) : null;
$headPercent = ($maxHeads > 0) ? min(100, round(($currentHeads / $maxHeads) * 100)) : null;
$regsLeft    = ($maxRegs  > 0) ? max(0, $maxRegs  - $currentRegs)  : null;
$headsLeft   = ($maxHeads > 0) ? max(0, $maxHeads - $currentHeads) : null;

$isFull = !empty($event['is_full']);

$tf = !empty($event['time_from']) ? date('H:i', strtotime($event['time_from'])) : '';
$tt = !empty($event['time_to'])   ? date('H:i', strtotime($event['time_to']))   : '';
$timeStr = $tf . ($tt ? '–' . $tt : '');

$img = !empty($event['image']) ? $event['image'] : '';
if (!$img || !is_file(FCPATH . $img)) {
    $img = 'assets/img/lcnl-placeholder-320.png';
}
$modalId = 'eventImageModal';

/**
 * Convert plain-text event content to formatted HTML.
 * - Lines starting with "- " or "* " become <ul><li> lists.
 * - A single short line (≤35 chars, no dash, no trailing . or ,) becomes a bold sub-heading.
 * - Everything else becomes a <p>.
 */
if (!function_exists('fmtEventText')) {
    function fmtEventText(string $raw): string
    {
        if (trim($raw) === '') return '';

        $out      = '';
        $listType = null; // 'ul' | 'ol'
        $acc      = [];

        $inlineFmt = function (string $s): string {
            return preg_replace('/\*\*(.+?)\*\*/u', '<strong>$1</strong>', esc($s));
        };

        $flush = function () use (&$acc, &$listType, &$out, $inlineFmt) {
            if ($listType) { $out .= "</$listType>"; $listType = null; }
            if (empty($acc)) return;
            $ll = array_values(array_filter(array_map('trim', $acc)));
            if (empty($ll)) { $acc = []; return; }
            $isHeader = count($ll) === 1
                && mb_strlen($ll[0]) <= 35
                && !preg_match('/[-–]/u', $ll[0])
                && !preg_match('/[.,]$/u', $ll[0]);
            $out .= $isHeader
                ? '<p class="fw-semibold text-dark mb-1 mt-3">' . $inlineFmt($ll[0]) . '</p>'
                : '<p class="mb-1">' . implode('<br>', array_map($inlineFmt, $ll)) . '</p>';
            $acc = [];
        };

        foreach (explode("\n", $raw) as $line) {
            $t = trim($line);
            if ($t === '') { $flush(); continue; }

            // Markdown headings: ## Heading
            if (preg_match('/^#{1,3}\s+(.+)$/u', $t, $m)) {
                $flush();
                $out .= '<p class="fw-semibold text-dark mb-1 mt-3">' . $inlineFmt(trim($m[1])) . '</p>';
                continue;
            }

            // Unordered list: - item or * item
            if (preg_match('/^[-*]\s+(.+)$/u', $t, $m)) {
                if (!empty($acc)) $flush();
                if ($listType !== 'ul') {
                    if ($listType) $out .= "</$listType>";
                    $out .= '<ul class="mb-2 ps-3">';
                    $listType = 'ul';
                }
                $out .= '<li>' . $inlineFmt(trim($m[1])) . '</li>';
                continue;
            }

            // Ordered list: 1. item
            if (preg_match('/^\d+\.\s+(.+)$/u', $t, $m)) {
                if (!empty($acc)) $flush();
                if ($listType !== 'ol') {
                    if ($listType) $out .= "</$listType>";
                    $out .= '<ol class="mb-2 ps-3">';
                    $listType = 'ol';
                }
                $out .= '<li>' . $inlineFmt(trim($m[1])) . '</li>';
                continue;
            }

            // Regular text
            if ($listType) { $out .= "</$listType>"; $listType = null; }
            $acc[] = $t;
        }
        $flush();
        return $out;
    }
}
?>

<!-- Hero Banner -->
<section class="hero-lcnl-watermark hero-overlay-moss d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">

    <h1 class="fw-bold display-6 mb-2">
      <i class="bi bi-calendar-event me-2"></i>
      <?= esc($event['title'] ?? 'Event') ?>
    </h1>

    <div class="d-flex justify-content-center flex-wrap gap-2 mt-2">

      <?php if (!empty($event['event_date'])): ?>
        <span class="badge badge-glass">
          <i class="bi bi-calendar-event me-1"></i>
          <?= date('D j M Y', strtotime($event['event_date'])) ?>
          <?php if ($timeStr): ?>
            &middot; <i class="bi bi-clock ms-1 me-1"></i><?= $timeStr ?>
          <?php endif; ?>
        </span>
      <?php endif; ?>

      <?php if (!empty($event['location'])): ?>
        <span class="badge badge-brand">
          <i class="bi bi-geo-alt me-1"></i><?= esc($event['location']) ?>
        </span>
      <?php endif; ?>

      <?php if (!empty($event['is_sold_out'])): ?>
        <span class="badge bg-danger fs-6 px-3 py-2">
          <i class="bi bi-x-circle-fill me-1"></i>SOLD OUT
        </span>
      <?php endif; ?>

      <?php if ($requiresReg): ?>
        <span class="badge <?= $isFull ? 'bg-danger' : ($regOpen ? 'bg-success' : 'bg-secondary') ?>">
          <i class="bi <?= $isFull ? 'bi-x-circle' : 'bi-check-circle' ?> me-1"></i>
          <?= $isFull ? 'Fully Booked' : ($regOpen ? 'Registration Open' : 'Registration Closed') ?>
        </span>
      <?php endif; ?>

    </div>

  </div>
</section>

<div class="container py-4">

  <!-- Description + Image -->
  <div class="lcnl-card border-0 shadow-sm mb-3 overflow-hidden">
    <div class="card-body p-4">
      <div class="row g-4 align-items-start">

        <div class="col">
          <h5 class="fw-semibold mb-3 text-brand">
            <i class="bi bi-info-circle me-2"></i>About this event
          </h5>
          <div class="event-description text-secondary">
            <?= !empty($event['description'])
              ? fmtEventText($event['description'])
              : '<p class="text-muted fst-italic">More details coming soon.</p>' ?>
          </div>
        </div>

        <div class="col-auto">
          <a href="#" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>"
            class="d-block position-relative event-image-wrapper" style="width: 160px;">
            <img src="<?= base_url($img) ?>" class="img-fluid rounded-3 shadow-sm w-100">
            <div class="position-absolute top-0 end-0 m-1">
              <span class="badge bg-dark bg-opacity-75" style="font-size: 0.65rem;">
                <i class="bi bi-zoom-in me-1"></i>Enlarge
              </span>
            </div>
            <?php if (!empty($event['is_sold_out'])): ?>
              <div class="sold-out-banner rounded-3">
                <i class="bi bi-x-circle-fill me-1"></i>SOLD OUT
              </div>
            <?php endif; ?>
          </a>
        </div>

      </div>
    </div>
  </div>

  <!-- Terms · Ticket · Contact -->
  <?php $hasTerms = !empty($event['eventterms']); ?>
  <div class="row g-3 mb-3">

    <?php if ($hasTerms): ?>
    <div class="col-lg-4">
      <div class="lcnl-card border-0 shadow-sm h-100">
        <div class="card-body p-4">
          <h5 class="fw-semibold mb-3 text-brand">
            <i class="bi bi-file-earmark-text me-2"></i>Event Terms
          </h5>
          <div class="text-secondary small">
            <?= fmtEventText($event['eventterms']) ?>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <?php $colW = $hasTerms ? 'col-lg-4' : 'col-lg-6'; ?>

    <div class="<?= $colW ?>">
      <div class="lcnl-card border-0 shadow-sm h-100">
        <div class="card-body p-4">
          <h5 class="fw-semibold mb-3 text-brand">
            <i class="bi bi-ticket-perforated me-2"></i>Ticket Information
          </h5>
          <div class="text-secondary small">
            <?= !empty($event['ticketinfo'])
              ? fmtEventText($event['ticketinfo'])
              : '<p class="text-muted fst-italic mb-0">Ticket details will be announced.</p>' ?>
          </div>
          <?php if (!empty($event['purchase_ticket_url'])): ?>
            <div class="mt-3">
              <a href="<?= esc($event['purchase_ticket_url']) ?>" target="_blank" rel="noopener noreferrer"
                class="btn btn-brand px-4">
                <i class="bi bi-ticket-perforated-fill me-2"></i>Purchase Tickets
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="<?= $colW ?>">
      <div class="lcnl-card border-0 shadow-sm h-100">
        <div class="card-body p-4">
          <h5 class="fw-semibold mb-3 text-brand">
            <i class="bi bi-telephone-inbound me-2"></i>Contact
          </h5>
          <div class="text-secondary small">
            <?= !empty($event['contactinfo'])
              ? fmtEventText($event['contactinfo'])
              : '<p class="text-muted fst-italic mb-0">For queries email <a href="mailto:info@lcnl.org" class="text-brand fw-semibold text-decoration-none">info@lcnl.org</a>.</p>' ?>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- Registration Panel -->
  <?php if ($requiresReg): ?>
    <div class="lcnl-card shadow-sm border-0 mb-4 overflow-hidden">
      <div class="card-header bg-brand text-white py-3 border-0">
        <h5 class="mb-0 fw-semibold">
          <i class="bi bi-pencil-square me-2"></i>Event Registration
        </h5>
      </div>
      <div class="card-body p-4">

        <?php if (!$regOpen): ?>
          <div class="d-flex align-items-center gap-3 text-secondary">
            <i class="bi bi-lock-fill fs-4 flex-shrink-0"></i>
            <span>Registration is currently closed for this event.</span>
          </div>

        <?php elseif ($isFull): ?>
          <div class="d-flex align-items-center gap-3 text-danger">
            <i class="bi bi-x-octagon-fill fs-4 flex-shrink-0"></i>
            <span class="fw-semibold">This event is now fully booked.</span>
          </div>

        <?php else: ?>

          <div class="row g-3 align-items-center">

            <?php if ($maxRegs > 0 || $maxHeads > 0): ?>
            <div class="col-md-6">
              <?php if ($maxRegs > 0): ?>
                <div class="<?= $maxHeads > 0 ? 'mb-3' : '' ?>">
                  <div class="d-flex justify-content-between mb-1 small">
                    <span class="text-muted">Registrations</span>
                    <span class="fw-semibold"><?= $currentRegs ?> / <?= $maxRegs ?></span>
                  </div>
                  <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-brand" role="progressbar"
                      style="width: <?= $regPercent ?>%"
                      aria-valuenow="<?= $regPercent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  <?php if ($regsLeft !== null): ?>
                    <div class="small text-muted mt-1"><?= $regsLeft ?> registration<?= $regsLeft !== 1 ? 's' : '' ?> remaining</div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

              <?php if ($maxHeads > 0): ?>
                <div>
                  <div class="d-flex justify-content-between mb-1 small">
                    <span class="text-muted">Total Headcount</span>
                    <span class="fw-semibold"><?= $currentHeads ?> / <?= $maxHeads ?></span>
                  </div>
                  <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-brand" role="progressbar"
                      style="width: <?= $headPercent ?>%"
                      aria-valuenow="<?= $headPercent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  <?php if ($headsLeft !== null): ?>
                    <div class="small text-muted mt-1"><?= $headsLeft ?> seat<?= $headsLeft !== 1 ? 's' : '' ?> remaining</div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="<?= ($maxRegs > 0 || $maxHeads > 0) ? 'col-md-6 text-md-end' : 'col-12' ?> d-flex flex-wrap gap-2 justify-content-md-end">
              <a href="<?= site_url('events/register/' . ($event['slug'] ?? '')) ?>"
                class="btn btn-brand btn-lg px-5">
                <i class="bi bi-pencil-square me-2"></i>Register Now
              </a>
              <?php if (!empty($event['purchase_ticket_url'])): ?>
                <a href="<?= esc($event['purchase_ticket_url']) ?>" target="_blank" rel="noopener noreferrer"
                  class="btn btn-outline-brand btn-lg px-4">
                  <i class="bi bi-ticket-perforated-fill me-2"></i>Purchase Tickets
                </a>
              <?php endif; ?>
            </div>

          </div>

        <?php endif; ?>

      </div>
    </div>
  <?php endif; ?>

  <div>
    <a href="<?= base_url('events') ?>" class="btn btn-outline-secondary px-4">
      <i class="bi bi-arrow-left me-2"></i>Back to Events
    </a>
  </div>

</div>

<!-- Image Modal -->
<div class="modal fade" id="<?= $modalId ?>" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-body p-0">
        <img src="<?= base_url($img) ?>" class="img-fluid w-100">
      </div>
      <div class="modal-footer justify-content-between py-3">
        <span class="text-muted"><?= esc($event['title']) ?></span>
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
          <i class="bi bi-x-lg me-2"></i>Close
        </button>
      </div>
    </div>
  </div>
</div>

<style>
  .event-image-wrapper img { transition: transform 0.3s ease; }
  .event-image-wrapper:hover img { transform: scale(1.02); }
  .event-description p { margin-bottom: 0.75rem; }
  .sold-out-banner {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    padding: 6px 0;
    background: rgba(185, 28, 28, 0.92);
    color: #fff;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-align: center;
    text-transform: uppercase;
    z-index: 20;
  }
</style>

<?= $this->endSection() ?>
