<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('partials/page_header', [
  'overlay'    => 'steel',
  'heading'    => 'Access Denied',
  'subheading' => $message,
  'extra'      => '<a href="' . base_url('/') . '" class="btn btn-light rounded-pill px-4">Return home</a>',
]) ?>

<?= $this->endSection() ?>