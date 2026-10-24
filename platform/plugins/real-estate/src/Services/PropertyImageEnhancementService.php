<?php

namespace Botble\RealEstate\Services;

use Botble\Media\Models\MediaFile;
use File;
use Image;
use Imagick;
use Log;
use RvMedia;

/**
 * Runs CPU-only mild sharpening on a property/project image, then stamps a
 * centered, more visible watermark on the main file and every generated
 * thumbnail size - RvMedia::generateThumbnails() only watermarks the main
 * file, bottom-right, which is why thumbnails (what buyers see in listing
 * grids) never carried a watermark at all. This runs after that method,
 * re-opening the files it already produced rather than duplicating
 * thumbnail generation.
 *
 * Deliberately does NOT do any auto color/white-balance correction: an
 * earlier version applied a gray-world white balance (scaling each color
 * channel so the image's average color is neutral gray), which assumes a
 * photo's content averages out to gray. Real estate exteriors routinely
 * don't - they're dominated by green grass/trees and blue sky - so that
 * assumption was false for most uploads and the "correction" was instead
 * pushing a uniform yellow/green color cast onto every photo.
 *
 * Before touching the main file, the pristine upload is copied to a sibling
 * originals/ folder and that copy's existence doubles as a guard against
 * re-running the filters on an already-processed file (e.g. a retried job),
 * which would otherwise compound degradation on every run.
 */
class PropertyImageEnhancementService
{
    protected const UNSHARP_SIGMA = 0.5;

    protected const UNSHARP_AMOUNT = 0.3;

    protected const UNSHARP_THRESHOLD = 0.03;

    protected const MAX_WATERMARK_WIDTH_RATIO = 0.5;

    protected const MAX_WATERMARK_HEIGHT_RATIO = 0.5;

    // Caps the served file's longer edge for page-load performance - large
    // camera/phone photos (often 4000px+) get downscaled, while the
    // already-small generated thumbnail sizes in the same loop are
    // naturally left alone since they're already under this.
    protected const MAX_DIMENSION = 2000;

    // Recompression quality used when re-saving every size - keeps file size
    // down for performance while staying visually close to the original.
    protected const SAVE_QUALITY = 82;

    public function process(MediaFile $file): bool
    {
        if (!$file->canGenerateThumbnails()) {
            return false;
        }

        $mainPath = RvMedia::getRealPath($file->url);

        if (File::exists($this->originalBackupPath($mainPath))) {
            // Already processed in a previous run (e.g. a retried/re-dispatched
            // job) - never re-run filters on an already-filtered file.
            return true;
        }

        foreach ($this->paths($file) as $path) {
            if (!File::exists($path)) {
                continue;
            }

            if ($path === $mainPath) {
                $this->backupOriginal($mainPath);
            }

            $image = Image::make($path);

            // Must happen before enhance()/applyCenteredWatermark(), both of
            // which rely on the image's final, correctly-oriented width/height.
            try {
                $image->orientate();
            } catch (\Throwable $e) {
                Log::error('PropertyImageEnhancementService: orientate failed for ' . $path . ': ' . $e->getMessage());
            }

            if (extension_loaded('imagick') && $image->getCore() instanceof Imagick) {
                // Neutralize any stale orientation tag so nothing re-rotates
                // an image orientate() already physically rotated - Intervention's
                // Imagick JPEG encoder never strips metadata on save().
                $image->getCore()->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
            }

            if (extension_loaded('imagick') && $file->mime_type !== 'image/gif') {
                $this->enhance($image->getCore());
            }

            $this->resizeIfOversized($image);

            $this->applyCenteredWatermark($image);

            $image->save($path, static::SAVE_QUALITY);
        }

        return true;
    }

    protected function paths(MediaFile $file): array
    {
        $paths = [RvMedia::getRealPath($file->url)];

        foreach (RvMedia::getSizes() as $size) {
            $paths[] = RvMedia::getRealPath(
                File::dirname($file->url) . '/' . File::name($file->url) . '-' . $size . '.' . File::extension($file->url)
            );
        }

        return $paths;
    }

    protected function originalBackupPath(string $path): string
    {
        return File::dirname($path) . '/originals/' . File::basename($path);
    }

    protected function backupOriginal(string $path): void
    {
        $backupPath = $this->originalBackupPath($path);

        if (File::exists($backupPath)) {
            return;
        }

        File::ensureDirectoryExists(File::dirname($backupPath));
        File::copy($path, $backupPath);
    }

    protected function resizeIfOversized($image): void
    {
        if ($image->width() <= static::MAX_DIMENSION && $image->height() <= static::MAX_DIMENSION) {
            return;
        }

        if ($image->width() >= $image->height()) {
            $image->resize(static::MAX_DIMENSION, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        } else {
            $image->resize(null, static::MAX_DIMENSION, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
    }

    protected function enhance(Imagick $imagick): void
    {
        $imagick->unsharpMaskImage(0, static::UNSHARP_SIGMA, static::UNSHARP_AMOUNT, static::UNSHARP_THRESHOLD);
    }

    protected function applyCenteredWatermark($image): void
    {
        if (!setting('media_watermark_enabled', config('core.media.media.watermark.enabled'))) {
            return;
        }

        $source = RvMedia::getRealPath(setting('media_watermark_source', config('core.media.media.watermark.source')));

        if (!$source || !File::exists($source)) {
            return;
        }

        // Property/project images get a more visible watermark than the sitewide
        // default (avatars/blog/theme) - twice the configured size, not the size itself,
        // so it stays proportionally bigger even if the sitewide setting is later changed.
        $sizePercent = (float) setting('media_watermark_size', config('core.media.media.watermark.size')) * 2;

        if ($sizePercent <= 0) {
            return;
        }

        $maxWidth = $image->width() * static::MAX_WATERMARK_WIDTH_RATIO;
        $maxHeight = $image->height() * static::MAX_WATERMARK_HEIGHT_RATIO;

        $targetWidth = min(round($image->width() * ($sizePercent / 100), 2), $maxWidth);

        $watermark = Image::make($source);

        $watermark->resize($targetWidth, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        // The aspect-ratio resize above only bounds width - a tall/narrow
        // watermark source could still end up taller than the photo, so clamp
        // height too, regardless of how the watermark asset itself is shaped.
        if ($watermark->height() > $maxHeight) {
            $watermark->resize(null, $maxHeight, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        $watermark->opacity(setting('watermark_opacity', config('core.media.media.watermark.opacity')));

        $image->insert($watermark, 'center', 0, 0);
    }
}
