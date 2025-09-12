<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<script>
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".hero-lcnl-watermark").forEach(container => {
    const count = 20; // number of scattered logos per section
    const placed = []; // keep track of positions

    for (let i = 0; i < count; i++) {
      let tries = 0, placedOk = false, x, y, size;

      while (!placedOk && tries < 50) { // max attempts
        size = 50 + Math.random() * 150; // px
        x = Math.random() * 100; // %
        y = Math.random() * 100; // %

        // Convert % to pixels relative to container
        const rect = container.getBoundingClientRect();
        const px = (x / 100) * rect.width;
        const py = (y / 100) * rect.height;

        // Check overlap with already placed logos
        let overlap = false;
        for (const p of placed) {
          const dx = px - p.x;
          const dy = py - p.y;
          const dist = Math.sqrt(dx * dx + dy * dy);
          if (dist < (size/2 + p.size/2) * 0.9) { // 0.9 = tolerance
            overlap = true;
            break;
          }
        }

        if (!overlap) {
          placed.push({ x: px, y: py, size });
          placedOk = true;
        }
        tries++;
      }

      if (placedOk) {
        const img = document.createElement("img");
        img.src = "/assets/patterns/lcnl-watermark.svg";
        img.className = "random-logo";
        img.style.width = size + "px";
        img.style.top = y + "%";
        img.style.left = x + "%";
        img.style.transform =
          `translate(-50%, -50%) rotate(${Math.random() * 360}deg)`;
        container.appendChild(img);
      }
    }
  });
});
</script>

<?php 
// Array of overlay samples (label => class)
$overlays = [
  ['Grey Overlay', 'hero-overlay-grey'],
  ['Red Overlay', 'hero-overlay-red'],
  ['Blue Overlay', 'hero-overlay-blue'],
  ['Green Overlay', 'hero-overlay-green'],
  ['Orange Overlay', 'hero-overlay-orange'],
  ['White Overlay', 'hero-overlay-white'],
  ['Royal Blue Overlay', 'hero-overlay-royalblue'],
  ['Emerald Green Overlay', 'hero-overlay-emerald'],
  ['Dark Red Overlay', 'hero-overlay-darkred'],
  ['Midnight Purple Overlay', 'hero-overlay-midnight'],
  ['Charcoal Grey Overlay', 'hero-overlay-charcoal'],
  ['Teal Mist Overlay', 'hero-overlay-teal'],
  ['Burgundy Overlay', 'hero-overlay-burgundy'],
  ['Slate Blue Overlay', 'hero-overlay-slateblue'],
  ['Forest Green Overlay', 'hero-overlay-forest'],
  ['Bronze Overlay', 'hero-overlay-bronze'],
  ['Sapphire Overlay', 'hero-overlay-sapphire'],
  ['Ruby Overlay', 'hero-overlay-ruby'],
  ['Amethyst Overlay', 'hero-overlay-amethyst'],
  ['Onyx Overlay', 'hero-overlay-onyx'],
  ['Obsidian Overlay', 'hero-overlay-obsidian'],
  ['Gold Overlay', 'hero-overlay-gold'],
  ['Platinum Overlay', 'hero-overlay-platinum'],
  ['Deep Ocean Overlay', 'hero-overlay-ocean'],
  ['Storm Grey Overlay', 'hero-overlay-storm'],
  ['Wine Overlay', 'hero-overlay-wine'],
  ['Velvet Overlay', 'hero-overlay-velvet'],
  ['Jade Overlay', 'hero-overlay-jade'],
  ['Copper Overlay', 'hero-overlay-copper'],
  ['Steel Overlay', 'hero-overlay-steel'],
  ['Smoke Overlay', 'hero-overlay-smoke'],
  ['Midnight Teal Overlay', 'hero-overlay-midnight-teal'],
  ['Desert Sand Overlay', 'hero-overlay-sand'],
  ['Indigo Overlay', 'hero-overlay-indigo'],
  ['Peacock Overlay', 'hero-overlay-peacock'],
  ['Vignette Overlay', 'hero-overlay-vignette'],
    ['Crimson Sunset Overlay', 'hero-overlay-crimsonsunset'],
  ['Arctic Blue Overlay', 'hero-overlay-arctic'],
  ['Moss Green Overlay', 'hero-overlay-moss'],
  ['Amber Glow Overlay', 'hero-overlay-amber'],
  ['Night Sky Overlay', 'hero-overlay-nightsky'],
  ['Turquoise Overlay', 'hero-overlay-turquoise'],
  ['Rosewood Overlay', 'hero-overlay-rosewood'],
  ['Pearl White Overlay', 'hero-overlay-pearl'],
  ['Cobalt Blue Overlay', 'hero-overlay-cobalt'],
  ['Garnet Overlay', 'hero-overlay-garnet'],
  ['Emerald Mist Overlay', 'hero-overlay-emeraldmist'],
  ['Silver Frost Overlay', 'hero-overlay-silverfrost'],
  ['Magenta Overlay', 'hero-overlay-magenta'],
  ['Chocolate Brown Overlay', 'hero-overlay-chocolate'],
  ['Slate Storm Overlay', 'hero-overlay-slatestorm'],
  ['Lavender Overlay', 'hero-overlay-lavender'],
  ['Obsidian Red Overlay', 'hero-overlay-obsidianred'],
  ['Teal Ocean Overlay', 'hero-overlay-tealocean'],
  ['Royal Gold Overlay', 'hero-overlay-royalgold'],
  ['Midnight Indigo Overlay', 'hero-overlay-midnightindigo'],

];
?>

<div class="container py-5">
  <h1 class="fw-bold mb-4 text-center">Overlay Samples Gallery</h1>
  
  <div class="row g-4">
    <?php $i = 1; foreach ($overlays as [$label, $class]) : ?>
      <div class="col-md-6 col-lg-4">
        <section class="hero-lcnl-watermark <?= $class ?> d-flex align-items-center justify-content-center rounded shadow-sm" style="min-height:200px;">
          <div class="text-center">
            <h4 class="fw-bold text-white mb-2"><?= $label ?></h4>
            <span class="badge bg-white text-dark"><?= $i ?> — <?= $class ?></span>
          </div>
        </section>
      </div>
    <?php $i++; endforeach; ?>
  </div>
</div>

<?= $this->endSection() ?>


