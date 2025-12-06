<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title ?? 'LCNL') ?></title>
  <meta name="description" content="<?= esc($metaDescription ?? 'Lohana Community North London – Bringing people together since 1976. Learn more about our events, membership, and community initiatives.') ?>">
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/lcnl-core.css">
  <link rel="stylesheet" href="/assets/css/lcnl-header-nav.css">
  <link rel="stylesheet" href="/assets/css/lcnl-hero.css">
  <link rel="stylesheet" href="/assets/css/lcnl-components.css">
  <link rel="stylesheet" href="/assets/css/lcnl-pages.css">
  <link rel="stylesheet" href="/assets/css/lcnl-auth.css"> <!-- only on login/register -->
  <link rel="stylesheet" href="/assets/css/lcnl-utilities.css"> <!-- optional global helpers -->
</head>

<body>
  <?= $this->include('layout/_header') ?>
  <?= $this->include('layout/_navbar') ?>

  <main class="flex-shrink-0 mt-0">
    <?= $this->renderSection('content') ?>
  </main>

  <?= $this->include('layout/_footer') ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>