<?php
/**
 * "LCNL in the media" home spotlight: the latest press coverage.
 *
 * Renders one column of the home spotlight row (see home.php). To feature a
 * new article, render the page to WebP (480/800/1600 wide plus a 1600 JPEG)
 * under assets/img/media/, drop the PDF in assets/documents/media/ and
 * update the array below.
 */
$article = [
  'publication' => 'Voice of Day',
  'date'        => '2026-10-21',
  'page'        => 18,
  'headline'    => 'Golden Jubilee Navratri unites thousands of Garba lovers in London',
  'quote'       => 'It became a celebration dedicated to keeping Gujarati culture alive, connecting the '
    . 'younger generation with its roots, and strengthening the bonds of affection and unity.',
  'image'       => 'assets/img/media/voice-of-day-2026-10-21',
  'pdf'         => 'assets/documents/media/voice-of-day-2026-10-21.pdf',
];

$mediaImg  = static fn(int $w): string => base_url($article['image'] . '-' . $w . '.webp');
$mediaDate = (new DateTimeImmutable($article['date']))->format('l j F Y');
?>
<div class="col-lg">
  <div class="media-spotlight h-100">
    <div class="row g-0 h-100">

      <div class="col-sm-5 media-spotlight-page">
        <a href="<?= $mediaImg(1600) ?>" target="_blank" rel="noopener" tabindex="-1" aria-hidden="true">
          <img src="<?= $mediaImg(480) ?>" srcset="<?= $mediaImg(480) ?> 480w, <?= $mediaImg(800) ?> 800w"
            sizes="(min-width: 992px) 20vw, (min-width: 576px) 40vw, 100vw"
            width="480" height="700" loading="lazy" decoding="async" alt="">
        </a>
      </div>

      <div class="col-sm-7">
        <div class="media-spotlight-body">
          <span class="media-spotlight-eyebrow"><i class="bi bi-newspaper me-1"></i>LCNL in the media</span>
          <h2 class="media-spotlight-title"><?= esc($article['headline']) ?></h2>
          <p class="media-spotlight-source">
            <?= esc($article['publication']) ?> &middot; <?= esc($mediaDate) ?> &middot; page <?= esc($article['page']) ?>
          </p>

          <blockquote class="media-spotlight-quote">
            <p class="mb-0">&ldquo;<?= esc($article['quote']) ?>&rdquo;</p>
          </blockquote>

          <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
            <a href="<?= $mediaImg(1600) ?>" target="_blank" rel="noopener" class="btn btn-brand rounded-pill px-4">
              <i class="bi bi-book me-2"></i>Read the article
            </a>
            <a href="<?= base_url($article['pdf']) ?>" target="_blank" rel="noopener" class="btn btn-outline-brand rounded-pill px-3">
              <i class="bi bi-file-earmark-pdf me-1"></i>PDF
              <?php if (is_file(FCPATH . $article['pdf'])): ?>
                <span class="small">(<?= esc(round(filesize(FCPATH . $article['pdf']) / 1048576, 1)) ?>MB)</span>
              <?php endif; ?>
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
