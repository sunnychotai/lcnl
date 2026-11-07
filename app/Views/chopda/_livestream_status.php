<?php
/**
 * "The live stream will start soon" notice plus the auto-refresh, for any
 * page that shows the Chopda Pujan player.
 *
 * Until ChopdaPujan::$livestreamStart it tells people to keep refreshing; it
 * deliberately doesn't say what time the stream starts. The
 * page reloads itself at each of $livestreamRefreshAt, so anyone who leaves it
 * open picks up the stream (or a newly swapped link) without doing anything.
 */
$chopda    = config('ChopdaPujan');
$refreshes = $chopda->livestreamRefreshTimes();
?>
<?php if (! $chopda->livestreamStarted()): ?>
  <div class="chopda-live-notice" role="status">
    <i class="bi bi-broadcast" aria-hidden="true"></i>
    <div>
      <strong>The live stream will start soon.</strong>
      <span class="d-block">Keep refreshing this page.</span>
    </div>
  </div>
<?php endif; ?>

<?php if ($refreshes !== []): ?>
  <p class="chopda-live-refresh" data-chopda-refresh="<?= esc(json_encode(array_keys($refreshes)), 'attr') ?>">
    <i class="bi bi-arrow-clockwise me-1" aria-hidden="true"></i>
    This page will refresh automatically when the stream starts. If it doesn&rsquo;t, please refresh it yourself.
  </p>
  <script>
    (function () {
      var el = document.querySelector('[data-chopda-refresh]');
      if (!el || window.chopdaRefreshArmed) return;
      window.chopdaRefreshArmed = true;

      // Polled rather than one long setTimeout, so a phone or laptop that
      // slept through the time still reloads as soon as it wakes.
      var loadedAt = Date.now();
      var due = JSON.parse(el.getAttribute('data-chopda-refresh'))
        .filter(function (t) { return t > loadedAt; });

      if (!due.length) return;

      setInterval(function () {
        if (Date.now() >= due[0]) {
          window.location.reload();
        }
      }, 15000);
    })();
  </script>
<?php endif; ?>
