<?php
/**
 * LCNL bank details with copy buttons — most people book on a phone.
 *
 * @var \Config\ChristmasMehfil $config
 * @var array $booking
 */
$rows = [
  'mehfilAccName' => ['Account name', $config->bank['accountName']],
  'mehfilSort'    => ['Sort code', $config->bank['sortCode']],
  'mehfilAccNo'   => ['Account number', $config->bank['accountNumber']],
  'mehfilAmount'  => ['Amount', '£' . number_format((float) $booking['amount'])],
  'mehfilRef'     => ['Your reference', $booking['booking_ref']],
];
?>
<div class="mela-pay mb-3">
  <div class="mela-pay-head">
    <span><i class="bi bi-bank me-1"></i> Bank transfer to LCNL</span>
    <span class="fee">&pound;<?= esc(number_format((float) $booking['amount'])) ?></span>
  </div>
  <div class="mela-pay-body">
    <?php foreach ($rows as $id => [$label, $value]): ?>
      <div class="mela-pay-row">
        <span class="k"><?= esc($label) ?></span>
        <span class="d-flex align-items-center gap-2">
          <span class="v<?= $id === 'mehfilRef' ? ' text-brand' : '' ?>" id="<?= $id ?>"><?= esc($value) ?></span>
          <button type="button" class="mela-copy" data-copy="#<?= $id ?>">Copy</button>
        </span>
      </div>
    <?php endforeach; ?>
    <div class="xmas-ref-note">
      <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
      <span>
        Please make sure you use your reference <strong class="text-nowrap"><?= esc($booking['booking_ref']) ?></strong>
        when you make the payment, so we can match it to your booking.
      </span>
    </div>
  </div>
</div>

<script>
  document.querySelectorAll('.mela-copy').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var target = document.querySelector(btn.getAttribute('data-copy'));
      if (!target || !navigator.clipboard) return;
      // Copy the amount as a bare number so it pastes into a banking app.
      var text = target.textContent.trim().replace(/^£/, '');
      navigator.clipboard.writeText(text).then(function () {
        var was = btn.textContent;
        btn.textContent = 'Copied';
        setTimeout(function () { btn.textContent = was; }, 1500);
      }, function () {});
    });
  });
</script>
