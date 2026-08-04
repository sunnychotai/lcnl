<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php
  $pageTitle = trim((string) ($title ?? '')) !== ''
    ? $title . ' | LCNL'
    : 'Lohana Community North London';
  // Meta content must be a single line — collapse any newlines/runs of whitespace.
  $pageDescription = trim(preg_replace('/\s+/u', ' ', (string) ($metaDescription
    ?? 'Lohana Community North London – Bringing people together since 1976. Learn more about our events, membership, and community initiatives.')));
  // Default share card is 1200x630 — the ratio WhatsApp, Facebook and LinkedIn
  // crop to. The square logo used previously was cropped badly or demoted to a
  // thumbnail. Pages that set $ogImage (an event poster, say) override it.
  $hasCustomImage = !empty($ogImage);
  $pageImage = $hasCustomImage
    ? (str_starts_with($ogImage, 'http') ? $ogImage : base_url(ltrim($ogImage, '/')))
    : base_url('assets/img/og-default.png');
  $pageImageAlt = trim((string) ($ogImageAlt ?? '')) !== ''
    ? $ogImageAlt
    : 'Lohana Community North London — we move forward together, since 1976';
  ?>
  <title><?= esc($pageTitle) ?></title>
  <meta name="description" content="<?= esc($pageDescription) ?>">
  <link rel="canonical" href="<?= esc(current_url()) ?>">
  <?php if (! empty($noindex)): ?>
    <!-- Unlisted page: kept out of search results and link previews. -->
    <meta name="robots" content="noindex, nofollow, noarchive">
  <?php endif; ?>

  <!-- Open Graph (WhatsApp, Facebook, LinkedIn) -->
  <meta property="og:site_name" content="Lohana Community North London">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="en_GB">
  <meta property="og:title" content="<?= esc($pageTitle) ?>">
  <meta property="og:description" content="<?= esc($pageDescription) ?>">
  <meta property="og:url" content="<?= esc(current_url()) ?>">
  <meta property="og:image" content="<?= esc($pageImage) ?>">
  <meta property="og:image:alt" content="<?= esc($pageImageAlt) ?>">
  <?php if (!$hasCustomImage): ?>
    <!-- Declared only for the default card, whose dimensions are known. Stating
         them lets a crawler lay out the preview before fetching the image. -->
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
  <?php endif; ?>

  <!-- Twitter/X -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= esc($pageTitle) ?>">
  <meta name="twitter:description" content="<?= esc($pageDescription) ?>">
  <meta name="twitter:image" content="<?= esc($pageImage) ?>">
  <meta name="twitter:image:alt" content="<?= esc($pageImageAlt) ?>">

  <?php
  // Structured data. JSON_HEX_TAG escapes '<' so a stray "</script>" inside any
  // value cannot break out of the block.
  $ldFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG;

  $organisationLd = [
    '@context'      => 'https://schema.org',
    '@type'         => 'NGO',
    '@id'           => base_url() . '#organisation',
    'name'          => 'Lohana Community North London',
    'alternateName' => 'LCNL',
    'url'           => base_url(),
    'logo'          => base_url('assets/img/lcnl-logo.png'),
    'image'         => base_url('assets/img/og-default.png'),
    'foundingDate'  => '1976',
    'slogan'        => 'We move forward together',
    'description'   => 'Lohana Community North London is a voluntary community organisation '
      . 'founded in 1976, serving Lohana families across North London and Middlesex through '
      . 'religious, cultural, educational, sporting and bereavement services.',
    'areaServed'    => 'North London and Middlesex, United Kingdom',
    'sameAs'        => [
      'https://www.facebook.com/groups/lcnlmahajan/',
      'https://www.instagram.com/lcnlmahajan/',
      'https://vimeo.com/lcnl',
      'https://www.youtube.com/@lcnlmahajan',
    ],
  ];
  ?>
  <script type="application/ld+json"><?= json_encode($organisationLd, $ldFlags) ?></script>
  <?php if (!empty($jsonLd)): ?>
    <script type="application/ld+json"><?= json_encode($jsonLd, $ldFlags) ?></script>
  <?php endif; ?>

  <!-- Favicons -->
  <link rel="icon" href="<?= base_url('assets/icons/favicon.svg') ?>" type="image/svg+xml">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/icons/favicon-32x32.png') ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/icons/favicon-16x16.png') ?>">
  <link rel="icon" href="<?= base_url('assets/icons/favicon.ico') ?>" sizes="any">

  <!-- Apple Touch Icon (iOS home screen) -->
  <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/icons/apple-touch-icon.png') ?>">

  <!-- Safari pinned tab (macOS Safari) -->
  <link rel="mask-icon" href="<?= base_url('assets/icons/safari-pinned-tab.svg') ?>" color="#7a1d3c">

  <!-- Web manifest (Android/Chrome PWAs) -->
  <link rel="manifest" href="<?= base_url('site.webmanifest') ?>">

  <!-- Windows tile + theme color -->
  <meta name="msapplication-TileColor" content="#7a1d3c">
  <meta name="theme-color" content="#7a1d3c">

  <!-- CSS -->
  <?php
  /**
   * Cache-busting for local assets.
   *
   * The server sends `Cache-Control: public, max-age=604800` for /assets while
   * the HTML is `no-store`. A returning visitor therefore gets new markup
   * immediately but keeps the old stylesheet for up to a week, with no
   * revalidation — so a CSS fix appears not to have deployed. Appending the
   * file's modification time gives every revision its own URL, which the cache
   * treats as a new resource.
   */
  $assetUrl = static function (string $path): string {
    $path = ltrim($path, '/');
    $file = FCPATH . $path;

    return base_url($path) . (is_file($file) ? '?v=' . filemtime($file) : '');
  };
  ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <?php foreach ([
    'lcnl-core',
    'lcnl-header-nav',
    'lcnl-hero',
    'lcnl-components',
    'lcnl-pages',
    'lcnl-auth',       // only on login/register
    'lcnl-utilities',  // optional global helpers
  ] as $sheet): ?>
    <link rel="stylesheet" href="<?= esc($assetUrl("assets/css/{$sheet}.css")) ?>">
  <?php endforeach; ?>
</head>

<body>
  <a class="skip-link" href="#main">Skip to main content</a>

  <?= $this->include('layout/_header') ?>
  <?= $this->include('layout/_navbar') ?>

  <main id="main" class="flex-shrink-0 mt-0" tabindex="-1">
    <?= $this->renderSection('content') ?>
  </main>

  <?= $this->include('layout/_footer') ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>