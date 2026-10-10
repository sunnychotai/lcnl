<?php
/**
 * LCF bank details with copy buttons — most donors are on a phone.
 *
 * @var \Config\Legacy50 $config
 * @var string $reference
 */
$rows = [
  'legacyAccName' => ['Account name', $config->bank['accountName']],
  'legacyAccType' => ['Account type', $config->bank['accountType']],
  'legacyAccNo'   => ['Account number', $config->bank['accountNumber']],
  'legacySort'    => ['Sort code', $config->bank['sortCode']],
  'legacyRef'     => ['Your reference', $reference],
];
?>
<div class="mela-pay mb-3">
  <div class="mela-pay-head">
    <span><i class="bi bi-bank me-1"></i> Bank transfer to LCF</span>
    <span class="fee">&pound;<?= esc($config->donation) ?></span>
  </div>
  <div class="mela-pay-body">
    <?php foreach ($rows as $id => [$label, $value]): ?>
      <div class="mela-pay-row">
        <span class="k"><?= esc($label) ?></span>
        <span class="d-flex align-items-center gap-2">
          <span class="v<?= $id === 'legacyRef' ? ' text-brand' : '' ?>" id="<?= $id ?>"><?= esc($value) ?></span>
          <?php if ($id !== 'legacyAccType'): ?>
            <button type="button" class="mela-copy" data-copy="#<?= $id ?>">Copy</button>
          <?php endif; ?>
        </span>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<details class="legacy-intl mb-3">
  <summary>Paying from outside the UK?</summary>
  <div class="mela-pay-body pt-0">
    <div class="mela-pay-row">
      <span class="k">IBAN</span>
      <span class="d-flex align-items-center gap-2">
        <span class="v" id="legacyIban"><?= esc($config->bank['iban']) ?></span>
        <button type="button" class="mela-copy" data-copy="#legacyIban">Copy</button>
      </span>
    </div>
    <div class="mela-pay-row">
      <span class="k">SWIFT BIC</span>
      <span class="d-flex align-items-center gap-2">
        <span class="v" id="legacyBic"><?= esc($config->bank['bic']) ?></span>
        <button type="button" class="mela-copy" data-copy="#legacyBic">Copy</button>
      </span>
    </div>
  </div>
</details>

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
