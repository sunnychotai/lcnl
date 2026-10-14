<?php

namespace App\Libraries;

use Config\Gallery;
use GdImage;
use InvalidArgumentException;

/**
 * Turns one uploaded photo into the WebP variants the gallery serves.
 *
 * Decoding and re-encoding with GD is also the content check: whatever was
 * uploaded, only freshly encoded pixels reach R2, so no metadata (GPS, camera
 * serials) and no smuggled payloads survive.
 */
class GalleryImageProcessor
{
    private const ALLOWED_TYPES = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP];

    /** The scaled, faded logo, cached by size for the life of the request. */
    private array $logos = [];

    public function __construct(private Gallery $config)
    {
    }

    /**
     * @return array<string, array{body: string, width: int, height: int}> keyed by variant name
     *
     * @throws InvalidArgumentException when the file is not a usable photo
     */
    public function process(string $path): array
    {
        $info = @getimagesize($path);

        if ($info === false || ! in_array($info[2], self::ALLOWED_TYPES, true)) {
            throw new InvalidArgumentException('Not a JPEG, PNG or WebP image.');
        }

        [$width, $height] = $info;

        if ($width < 1 || $height < 1 || $width * $height > $this->config->maxSourcePixels) {
            throw new InvalidArgumentException('Image dimensions are not supported.');
        }

        $source = @imagecreatefromstring((string) file_get_contents($path));

        if (! $source instanceof GdImage) {
            throw new InvalidArgumentException('The image could not be read — it may be damaged.');
        }

        if ($info[2] === IMAGETYPE_JPEG) {
            $source = $this->applyOrientation($source, $path);
        }

        $variants = [];
        foreach ($this->config->variants as $name => $spec) {
            $image = $this->resize($source, $spec['width'], $spec['height']);

            if ($this->config->watermark && in_array($name, $this->config->watermarkVariants, true)) {
                $this->watermark($image);
            }

            $variants[$name] = [
                'body'   => $this->webp($image, $spec['quality']),
                'width'  => imagesx($image),
                'height' => imagesy($image),
            ];
        }

        return $variants;
    }

    /**
     * Watermark an already-stored WebP variant, for photos uploaded before
     * watermarking was turned on (the originals are not kept).
     */
    public function watermarkWebp(string $webp, int $quality): string
    {
        $image = @imagecreatefromstring($webp);

        if (! $image instanceof GdImage) {
            throw new InvalidArgumentException('The stored image could not be read.');
        }

        $this->watermark($image);

        return $this->webp($image, $quality);
    }

    /**
     * Stamp the logo in the top-right corner. If the logo file is missing the
     * photo goes out unmarked rather than the upload failing.
     */
    public function watermark(GdImage $image): void
    {
        $width  = imagesx($image);
        $height = imagesy($image);
        $size   = max(48, (int) round(min($width, $height) * $this->config->watermarkSize));
        $logo   = $this->logo($size);

        if ($logo === null) {
            return;
        }

        $margin = (int) round($size * 0.2);

        imagealphablending($image, true);
        imagecopy($image, $logo, $width - $size - $margin, $margin, 0, 0, $size, $size);
    }

    private function logo(int $size): ?GdImage
    {
        if (array_key_exists($size, $this->logos)) {
            return $this->logos[$size];
        }

        $path   = FCPATH . ltrim($this->config->watermarkImage, '/');
        $source = is_file($path) ? @imagecreatefrompng($path) : false;

        if (! $source instanceof GdImage) {
            log_message('error', 'Gallery: watermark image {path} could not be read; photos are going out unmarked.', ['path' => $path]);

            return $this->logos[$size] = null;
        }

        $logo = imagecreatetruecolor($size, $size);
        imagealphablending($logo, false);
        imagesavealpha($logo, true);
        imagefill($logo, 0, 0, imagecolorallocatealpha($logo, 0, 0, 0, 127));
        imagecopyresampled($logo, $source, 0, 0, 0, 0, $size, $size, imagesx($source), imagesy($source));

        // Fade it: GD has no opacity setting for images with an alpha channel,
        // so scale each pixel's own opacity.
        $opacity = max(0.0, min(1.0, $this->config->watermarkOpacity));
        if ($opacity < 1.0) {
            for ($x = 0; $x < $size; $x++) {
                for ($y = 0; $y < $size; $y++) {
                    $rgba  = imagecolorat($logo, $x, $y);
                    $alpha = 127 - (int) round((127 - (($rgba >> 24) & 0x7F)) * $opacity);
                    imagesetpixel($logo, $x, $y, ($alpha << 24) | ($rgba & 0xFFFFFF));
                }
            }
        }

        return $this->logos[$size] = $logo;
    }

    /**
     * Scale to fit inside the box, never enlarging, flattened onto white so
     * transparent PNGs do not turn black.
     */
    private function resize(GdImage $source, int $maxWidth, int $maxHeight): GdImage
    {
        $sw    = imagesx($source);
        $sh    = imagesy($source);
        $scale = min(1, $maxWidth / $sw, $maxHeight / $sh);
        $w     = max(1, (int) round($sw * $scale));
        $h     = max(1, (int) round($sh * $scale));

        $canvas = imagecreatetruecolor($w, $h);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $w, $h, $sw, $sh);

        return $canvas;
    }

    private function webp(GdImage $image, int $quality): string
    {
        ob_start();
        imagewebp($image, null, $quality);

        return (string) ob_get_clean();
    }

    /**
     * Photos straight off a camera or phone are often stored sideways with an
     * EXIF flag saying which way up they go. The browser normally rotates them
     * before upload; this covers anything that arrives unrotated.
     */
    private function applyOrientation(GdImage $image, string $path): GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $exif        = @exif_read_data($path);
        $orientation = (int) ($exif['Orientation'] ?? 1);

        if (in_array($orientation, [2, 4, 5, 7], true)) {
            imageflip($image, IMG_FLIP_HORIZONTAL);
        }

        $angle = match ($orientation) {
            3, 4    => 180,
            5, 6    => 270,
            7, 8    => 90,
            default => 0,
        };

        return $angle === 0 ? $image : (imagerotate($image, $angle, 0) ?: $image);
    }
}
