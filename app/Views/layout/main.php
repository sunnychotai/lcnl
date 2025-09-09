<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title ?? 'LCNL') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=<?= time() ?>') ?>">

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
