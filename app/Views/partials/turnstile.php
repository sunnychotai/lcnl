<?php
/**
 * Cloudflare Turnstile widget for a public form. Renders nothing until the
 * keys are set in .env (Config\Turnstile). Check the result in the
 * controller with (new \App\Libraries\Turnstile())->verify(...).
 */
$turnstile = new \App\Libraries\Turnstile();
?>
<?php if ($turnstile->enabled()): ?>
    <div class="cf-turnstile mb-3" data-sitekey="<?= esc($turnstile->siteKey(), 'attr') ?>" data-theme="light"
        data-size="flexible"></div>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<?php endif; ?>
