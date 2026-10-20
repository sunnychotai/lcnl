<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('partials/page_header', [
  'heading'    => 'Executive Committee',
  'subheading' => 'The elected committee that runs LCNL, 2025–27',
  'tone'       => 'brand',
]) ?>

<div class="container py-4 py-md-5">
  <div class="cmt-page">

    <?= view('committees/_more', ['current' => 'committee']) ?>

    <figure class="cmt-group-photo">
      <img src="<?= base_url('assets/img/committee/lcnl-ec-large.jpg') ?>"
        alt="Group photo of the LCNL Executive Committee 2025–27"
        width="1920" height="616" decoding="async" fetchpriority="high">
      <figcaption>LCNL Executive Committee 2025–27</figcaption>
    </figure>

    <?php
    $sections = [
      'officers'        => ['Officers', 'The President and the officers who run LCNL day to day.'],
      'members'         => ['Committee members', null],
      'representatives' => ['Representatives', 'Members who represent the bodies that work alongside LCNL.'],
    ];
    ?>

    <?php foreach ($sections as $key => [$title, $intro]): ?>
      <?php if (empty($groups[$key])) continue; ?>
      <section class="cmt-section" aria-labelledby="cmt-<?= $key ?>">
        <h2 id="cmt-<?= $key ?>" class="cmt-section-title"><?= esc($title) ?></h2>
        <?php if ($intro): ?>
          <p class="cmt-section-intro"><?= esc($intro) ?></p>
        <?php endif; ?>
        <?= view('committees/_people', ['people' => $groups[$key]]) ?>
      </section>
    <?php endforeach; ?>
  </div>
</div>

<?= $this->endSection() ?>
