<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- Hero Banner -->
<div class="position-relative w-100" style="height: 300px; overflow: hidden;">
    <img src="<?= base_url('assets/img/hero/events.png') ?>" 
         alt="Bereavement Support Information" 
         class="img-fluid w-100 h-100" 
         style="object-fit: cover; object-position: center;">

    <!-- Semi-transparent dark layer -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.4);"></div>

</div>

<div class="container py-5">
  <h2>Events</h2>
  <p>Upcoming events will be listed here.</p>
</div>
<?= $this->endSection() ?>
