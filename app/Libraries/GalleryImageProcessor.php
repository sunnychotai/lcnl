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
            $variants[$name] = $this->encode($source, $spec['width'], $spec['height'], $spec['quality']);
        }

        return $variants;
    }

    /**
     * Scale to fit inside the box, never enlarging, flattened onto white so
     * transparent PNGs do not turn black.
     *
     * @return array{body: string, width: int, height: int}
     */
    private function encode(GdImage $source, int $maxWidth, int $maxHeight, int $quality): array
    {
        $sw    = imagesx($source);
        $sh    = imagesy($source);
        $scale = min(1, $maxWidth / $sw, $maxHeight / $sh);
        $w     = max(1, (int) round($sw * $scale));
        $h     = max(1, (int) round($sh * $scale));

        $canvas = imagecreatetruecolor($w, $h);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $w, $h, $sw, $sh);

        ob_start();
        imagewebp($canvas, null, $quality);

        return ['body' => (string) ob_get_clean(), 'width' => $w, 'height' => $h];
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
