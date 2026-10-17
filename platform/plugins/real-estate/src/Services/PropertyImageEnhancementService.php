<?php

namespace Botble\RealEstate\Services;

use Botble\Media\Models\MediaFile;
use File;
use Image;
use Imagick;
use Log;
use RvMedia;

/**
 * Runs CPU-only auto white balance and mild sharpening on a property/project
 * image, then stamps a centered, more visible watermark on the main file and
 * every generated thumbnail size - RvMedia::generateThumbnails() only
 * watermarks the main file, bottom-right, which is why thumbnails (what
 * buyers see in listing grids) never carried a watermark at all. This runs
 * after that method, re-opening the files it already produced rather than
 * duplicating thumbnail generation.
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

            $this->applyCenteredWatermark($image);

            $image->save($path);
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

    protected function enhance(Imagick $imagick): void
    {
        $this->whiteBalance($imagick);

        $imagick->unsharpMaskImage(0, static::UNSHARP_SIGMA, static::UNSHARP_AMOUNT, static::UNSHARP_THRESHOLD);
    }

    protected function whiteBalance(Imagick $imagick): void
    {
        $channels = [Imagick::CHANNEL_RED, Imagick::CHANNEL_GREEN, Imagick::CHANNEL_BLUE];

        $means = [];
        foreach ($channels as $channel) {
            $stats = $imagick->getImageChannelStatistics()[$channel] ?? null;
            $means[$channel] = $stats['mean'] ?? null;
        }

        $means = array_filter($means, fn ($mean) => $mean !== null && $mean > 0);

        if (count($means) !== count($channels)) {
            return;
        }

        $gray = array_sum($means) / count($means);

        foreach ($means as $channel => $mean) {
            $imagick->evaluateImage(Imagick::EVALUATE_MULTIPLY, $gray / $mean, $channel);
        }
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
