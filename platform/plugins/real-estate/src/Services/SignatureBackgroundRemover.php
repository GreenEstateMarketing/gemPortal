<?php

namespace Botble\RealEstate\Services;

use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * An uploaded signature PNG is usually a photo of ink on paper, not a
 * transparent canvas drawing, so it arrives with a background that would
 * otherwise sit as an opaque box in the generated contract PDF (see
 * pdf-template.blade.php).
 *
 * A single global brightness cutoff doesn't work on real phone photos:
 * uneven lighting/shadow across the paper means the "background" luminance
 * can vary anywhere from near-white to as dark as the ink itself. Instead,
 * each pixel is compared against a *local* background estimate - a heavily
 * blurred copy of the image, which keeps the slow-varying paper shading but
 * washes out the thin ink strokes. A pixel much darker than its own local
 * background is ink; a pixel close to its local background is paper, no
 * matter how dark or bright that local lighting happens to be.
 *
 * This is a heuristic for the common case (ink strokes on a roughly
 * uniform paper, however lit), not a general-purpose background remover.
 */
class SignatureBackgroundRemover
{
    protected const MAX_DIMENSION = 1600;

    // How much darker than its local background a pixel must be to count
    // as ink, on a 0-255 luminance scale.
    protected const NOISE_FLOOR = 14;

    protected const INK_DELTA = 50;

    // If at least this fraction of pixels are already non-opaque, the
    // source was already exported with a transparent background (e.g. a
    // signature drawn/exported from another app) - there's no flat paper
    // background left to detect, and running the heuristic on it would
    // compare real ink against a bogus "local background" built by
    // blending its own transparent holes onto black, erasing the ink.
    protected const ALREADY_TRANSPARENT_THRESHOLD = 0.03;

    public static function process(string $pngBytes): string
    {
        try {
            return self::stripBackground($pngBytes);
        } catch (Throwable $exception) {
            Log::warning('SignatureBackgroundRemover failed, keeping original signature bytes: ' . $exception->getMessage());

            return $pngBytes;
        }
    }

    protected static function stripBackground(string $pngBytes): string
    {
        $image = @imagecreatefromstring($pngBytes);

        if (! $image) {
            return $pngBytes;
        }

        if (! imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }

        $image = self::constrainSize($image);
        $width = imagesx($image);
        $height = imagesy($image);

        if (self::hasSignificantExistingTransparency($image, $width, $height)) {
            imagedestroy($image);

            return $pngBytes;
        }

        $background = self::estimateLocalBackground($image, $width, $height);
        $mask = self::buildAlphaMask($image, $background, $width, $height);
        imagedestroy($background);

        imagealphablending($image, false);
        imagesavealpha($image, true);

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgba = imagecolorat($image, $x, $y);

                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;
                $gdAlpha = ($rgba >> 24) & 0x7F; // GD: 0 = opaque, 127 = fully transparent
                $existingAlpha255 = 255 - (int) round($gdAlpha * 255 / 127);

                $maskAlpha255 = imagecolorat($mask, $x, $y) & 0xFF;

                $newAlpha255 = min($existingAlpha255, $maskAlpha255);
                $newGdAlpha = 127 - (int) round($newAlpha255 * 127 / 255);

                $color = imagecolorallocatealpha($image, $r, $g, $b, $newGdAlpha);
                imagesetpixel($image, $x, $y, $color);
            }
        }

        imagedestroy($mask);

        ob_start();
        imagepng($image);
        $bytes = ob_get_clean();

        imagedestroy($image);

        return $bytes !== false ? $bytes : $pngBytes;
    }

    protected static function hasSignificantExistingTransparency($image, int $width, int $height): bool
    {
        $total = $width * $height;
        $sampleStep = max(1, (int) floor(sqrt($total / 10000))); // cap sampled pixels around ~10k
        $transparent = 0;
        $sampled = 0;

        for ($y = 0; $y < $height; $y += $sampleStep) {
            for ($x = 0; $x < $width; $x += $sampleStep) {
                $gdAlpha = (imagecolorat($image, $x, $y) >> 24) & 0x7F;
                if ($gdAlpha > 10) {
                    $transparent++;
                }
                $sampled++;
            }
        }

        return $sampled > 0 && ($transparent / $sampled) >= self::ALREADY_TRANSPARENT_THRESHOLD;
    }

    /**
     * Decides, pixel by pixel, how opaque each pixel should end up (0 =
     * fully transparent background, 255 = fully opaque ink), then smooths
     * that decision itself. Real photos carry paper grain and sensor noise,
     * so classifying raw pixels directly produces a speckled, jagged result
     * - individual noisy pixels flip across the threshold independently of
     * their neighbors. Denoising the input before classifying, and blurring
     * the resulting mask afterwards, keeps strokes smooth and solid instead
     * of grainy.
     *
     * @param resource|\GdImage $image
     * @param resource|\GdImage $background grayscale local-background estimate
     * @return resource|\GdImage a grayscale image whose per-pixel value is the alpha (0-255) to apply
     */
    protected static function buildAlphaMask($image, $background, int $width, int $height)
    {
        $denoised = imagecreatetruecolor($width, $height);
        imagealphablending($denoised, false);
        imagesavealpha($denoised, true);
        imagecopy($denoised, $image, 0, 0, 0, 0, $width, $height);
        imagefilter($denoised, IMG_FILTER_GAUSSIAN_BLUR);

        $mask = imagecreatetruecolor($width, $height);

        $noiseFloor = self::NOISE_FLOOR;
        $inkDelta = self::INK_DELTA;
        $range = $inkDelta - $noiseFloor;

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgba = imagecolorat($denoised, $x, $y);
                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;

                $pixelLuminance = ($r + $g + $b) / 3;
                $localBackground = imagecolorat($background, $x, $y) & 0xFF;

                $darkness = $localBackground - $pixelLuminance;

                if ($darkness <= $noiseFloor) {
                    $alpha255 = 0;
                } elseif ($darkness >= $inkDelta) {
                    $alpha255 = 255;
                } else {
                    $alpha255 = (int) round((($darkness - $noiseFloor) / $range) * 255);
                }

                $gray = imagecolorallocate($mask, $alpha255, $alpha255, $alpha255);
                imagesetpixel($mask, $x, $y, $gray);
            }
        }

        imagedestroy($denoised);

        // Smooth the mask itself to round off jagged edges and close up any
        // remaining single-pixel speckle holes.
        imagefilter($mask, IMG_FILTER_GAUSSIAN_BLUR);

        return $mask;
    }

    /**
     * Builds a smooth estimate of the paper's own shading/lighting by
     * shrinking the image down drastically (which erases thin ink strokes,
     * since they occupy only a few source pixels per destination pixel)
     * and resampling back up to full size. The result tracks gradients and
     * shadows across the paper without the signature itself.
     *
     * @param resource|\GdImage $image
     * @return resource|\GdImage a grayscale image the same size as $image
     */
    protected static function estimateLocalBackground($image, int $width, int $height)
    {
        $thumbWidth = max(6, (int) round($width / 25));
        $thumbHeight = max(6, (int) round($height / 25));

        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

        $blurred = imagecreatetruecolor($width, $height);
        imagealphablending($blurred, false);
        imagesavealpha($blurred, true);
        imagecopyresampled($blurred, $thumb, 0, 0, 0, 0, $width, $height, $thumbWidth, $thumbHeight);
        imagedestroy($thumb);

        // Flatten to grayscale so the caller can read a single luminance
        // byte back out per pixel.
        imagefilter($blurred, IMG_FILTER_GRAYSCALE);

        return $blurred;
    }

    /**
     * @param resource|\GdImage $image
     * @return resource|\GdImage
     */
    protected static function constrainSize($image)
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $max = self::MAX_DIMENSION;

        if ($width <= $max && $height <= $max) {
            return $image;
        }

        $ratio = min($max / $width, $max / $height);
        $newWidth = max(1, (int) round($width * $ratio));
        $newHeight = max(1, (int) round($height * $ratio));

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);

        return $resized;
    }
}
