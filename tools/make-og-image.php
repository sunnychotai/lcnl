<?php
/**
 * Regenerates the default Open Graph card at
 * public/assets/img/og-default.png (1200x630).
 *
 * Run from the repo root:  php tools/make-og-image.php
 *
 * Kept in the repo so the card can be rebuilt if the logo, the motto or the
 * founding wording ever change. Requires the GD extension with FreeType and
 * the DejaVu fonts.
 *
 * WhatsApp, Facebook and LinkedIn all crop to roughly 1.91:1. The previous
 * default was the 1086x1086 square logo, which those platforms either crop
 * badly or demote to a small thumbnail.
 */

$W = 1200;
$H = 630;

$im = imagecreatetruecolor($W, $H);
imageantialias($im, true);

$brand   = [0x7a, 0x1d, 0x3c];
$brandLo = [0x5e, 0x16, 0x30];
$gold    = imagecolorallocate($im, 0xd4, 0xaf, 0x37);
$white   = imagecolorallocate($im, 0xff, 0xff, 0xff);
$cream   = imagecolorallocate($im, 0xf5, 0xef, 0xf1);

// Vertical brand gradient
for ($y = 0; $y < $H; $y++) {
    $t = $y / ($H - 1);
    $c = imagecolorallocate(
        $im,
        (int) round($brand[0] + ($brandLo[0] - $brand[0]) * $t),
        (int) round($brand[1] + ($brandLo[1] - $brand[1]) * $t),
        (int) round($brand[2] + ($brandLo[2] - $brand[2]) * $t)
    );
    imageline($im, 0, $y, $W, $y, $c);
}

// Gold rule along the bottom
imagefilledrectangle($im, 0, $H - 12, $W, $H, $gold);

// Logo, left third
$root     = dirname(__DIR__);
$logoPath = $root . '/public/assets/img/lcnl-logo.png';

$logoBox = 300;
$logoX   = 90;
$logoY   = (int) (($H - $logoBox) / 2) - 6;

if (is_file($logoPath)) {
    $logo = imagecreatefrompng($logoPath);
    // White disc behind the logo so the maroon does not show through its
    // transparent corners and the crest keeps its contrast.
    imagefilledellipse($im, $logoX + $logoBox / 2, $logoY + $logoBox / 2, $logoBox + 26, $logoBox + 26, $white);
    imagealphablending($im, true);
    imagecopyresampled(
        $im, $logo,
        $logoX, $logoY, 0, 0,
        $logoBox, $logoBox,
        imagesx($logo), imagesy($logo)
    );
    imagedestroy($logo);
}

$fontBold = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
$fontReg  = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';

$textX     = $logoX + $logoBox + 70;
$rightPad  = 70;
$maxTextW  = $W - $textX - $rightPad;

/** Largest point size at which $text still fits $maxW. */
$fitSize = static function (string $text, string $font, int $maxW, int $start, int $min = 12): int {
    for ($pt = $start; $pt >= $min; $pt--) {
        $bb = imagettfbbox($pt, 0, $font, $text);
        if (max($bb[2], $bb[4]) - min($bb[0], $bb[6]) <= $maxW) {
            return $pt;
        }
    }
    return $min;
};

$line1 = 'Lohana Community';
$line2 = 'North London';
$motto = '"We Move Forward Together"';
$sub   = 'Bringing people together since 1976';

// One size for both title lines so they stay optically consistent
$titlePt = min(
    $fitSize($line1, $fontBold, $maxTextW, 52),
    $fitSize($line2, $fontBold, $maxTextW, 52)
);
$mottoPt = $fitSize($motto, $fontBold, $maxTextW, 25);
$subPt   = $fitSize($sub, $fontReg, $maxTextW, 20);

imagettftext($im, $titlePt, 0, $textX, 268, $white, $fontBold, $line1);
imagettftext($im, $titlePt, 0, $textX, 268 + (int) round($titlePt * 1.34), $white, $fontBold, $line2);

// Gold divider
imagefilledrectangle($im, $textX, 372, $textX + 96, 378, $gold);

imagettftext($im, $mottoPt, 0, $textX, 432, $gold, $fontBold, $motto);
imagettftext($im, $subPt, 0, $textX, 476, $cream, $fontReg, $sub);

printf("  title %dpt, motto %dpt, sub %dpt (max width %dpx)\n", $titlePt, $mottoPt, $subPt, $maxTextW);

$out = $root . '/public/assets/img/og-default.png';
imagepng($im, $out, 9);
imagedestroy($im);

$size = getimagesize($out);
printf("wrote %s  %dx%d  %d KB\n", $out, $size[0], $size[1], round(filesize($out) / 1024));
