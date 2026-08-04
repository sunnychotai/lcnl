<?= '<?xml version="1.0" encoding="UTF-8"?>' . "\n" ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<?php
/**
 * Static public routes. Event detail pages are appended by Sitemap::index.
 * Keep in step with the public routes in Config\Routes — anything reachable
 * and indexable belongs here. Auth-only and transactional pages do not.
 */
$pages = [
    ''                    => ['weekly',  '1.0'],
    'events'              => ['daily',   '0.9'],
    'membership'          => ['monthly', '0.8'],
    'membership/register' => ['monthly', '0.7'],
    'aboutus'             => ['monthly', '0.7'],
    'committee'           => ['monthly', '0.7'],
    'lcf'                 => ['monthly', '0.7'],
    'mahila'              => ['monthly', '0.7'],
    'yls'                 => ['monthly', '0.7'],
    'youth'               => ['monthly', '0.7'],
    'bereavement'         => ['monthly', '0.7'],
    'dlc-hire'            => ['monthly', '0.7'],
    'tabletennis'         => ['monthly', '0.6'],
    'faqs'                => ['monthly', '0.6'],
    'gallery'             => ['monthly', '0.5'],
    'contact'             => ['yearly',  '0.5'],
    'privacy'             => ['yearly',  '0.3'],
];
?>
<?php foreach ($pages as $path => [$changefreq, $priority]): ?>
  <url>
    <loc><?= $path === '' ? base_url() : base_url($path) ?></loc>
    <changefreq><?= $changefreq ?></changefreq>
    <priority><?= $priority ?></priority>
  </url>
<?php endforeach; ?>
