<?php
/**
 * "Pay by card" button for the pending-payment and reminder emails. Renders
 * nothing while card payments are not on offer.
 *
 * @var array $registration
 * @var \Config\ChopdaPujan $config
 */
$payUrl = $config->payUrl($registration);
?>
<?php if ($payUrl): ?>
<p style="text-align:center; margin:24px 0;">
    <a href="<?= esc($payUrl) ?>"
        style="background-color:#7a1d3c; color:#ffffff; padding:12px 24px; font-weight:bold;
              text-decoration:none; border-radius:4px; display:inline-block;">
        Pay &pound;<?= esc(number_format((float) $registration['amount'])) ?> by card
    </a>
    <br><span style="color:#666; font-size:13px;">Secure payment by Stripe &ndash; your place is confirmed straight away.</span>
</p>
<p style="color:#666; font-size:12px; text-align:center; word-break:break-all;">
    If the button does not work, open this link: <?= esc($payUrl) ?>
</p>
<?php endif; ?>
