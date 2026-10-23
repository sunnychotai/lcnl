<?php
/**
 * LCNL bank details with copy buttons — most people register on a phone.
 *
 * @var \Config\ChopdaPujan $config
 * @var string $reference
 */
$rows = [
  'chopdaAccName' => ['Account name', $config->bank['accountName']],
  'chopdaSort'    => ['Sort code', $config->bank['sortCode']],
  'chopdaAccNo'   => ['Account number', $config->bank['accountNumber']],
  'chopdaRef'     => ['Your reference', $reference],
];
?>
<div class="mela-pay mb-3">
  <div class="mela-pay-head">
    <span><i class="bi bi-bank me-1"></i> Bank transfer to LCNL</span>
    <span class="fee">&pound;<?= esc($config->fee) ?></span>
  </div>
  <div class="mela-pay-body">
    <?php foreach ($rows as $id => [$label, $value]): ?>
      <div class="mela-pay-row">
        <span class="k"><?= esc($label) ?></span>
        <span class="d-flex align-items-center gap-2">
          <span class="v<?= $id === 'chopdaRef' ? ' text-brand' : '' ?>" id="<?= $id ?>"><?= esc($value) ?></span>
          <button type="button" class="mela-copy" data-copy="#<?= $id ?>">Copy</button>
        </span>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
  document.querySelectorAll('.mela-copy').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var target = document.querySelector(btn.getAttribute('data-copy'));
      if (!target || !navigator.clipboard) return;
      navigator.clipboard.writeText(target.textContent.trim()).then(function () {
        var was = btn.textContent;
        btn.textContent = 'Copied';
        setTimeout(function () { btn.textContent = was; }, 1500);
      }, function () {});
    });
  });
</script>
