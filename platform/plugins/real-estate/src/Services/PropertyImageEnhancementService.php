<?php

namespace Botble\RealEstate\Services;

use Botble\Media\Models\MediaFile;
use File;
use Image;
use Imagick;
use RvMedia;

/**
 * Runs CPU-only auto brightness/contrast, white balance, noise reduction and
 * sharpening on a property/project image, then stamps a centered, more
 * visible watermark on the main file and every generated thumbnail size -
 * RvMedia::generateThumbnails() only watermarks the main file, bottom-right,
 * which is why thumbnails (what buyers see in listing grids) never carried
 * a watermark at all. This runs after that method, re-opening the files it
 * already produced rather than duplicating thumbnail generation.
 */
class PropertyImageEnhancementService
{
    public function process(MediaFile $file): bool
    {
        if (!$file->canGenerateThumbnails()) {
            return false;
        }

        foreach ($this->paths($file) as $path) {
            if (!File::exists($path)) {
                continue;
            }

            $image = Image::make($path);

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

    protected function enhance(Imagick $imagick): void
    {
        $this->whiteBalance($imagick);

        $imagick->normalizeImage();
        $imagick->despeckleImage();
        $imagick->unsharpMaskImage(0, 1.0, 1.0, 0.05);
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

        $watermark = Image::make($source);

        // Property/project images get a more visible watermark than the sitewide
        // default (avatars/blog/theme) - twice the configured size, not the size itself,
        // so it stays proportionally bigger even if the sitewide setting is later changed.
        $sizePercent = setting('media_watermark_size', config('core.media.media.watermark.size')) * 2;
        $size = round($image->width() * ($sizePercent / 100), 2);

        $watermark->resize($size, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $watermark->opacity(setting('watermark_opacity', config('core.media.media.watermark.opacity')));

        $image->insert($watermark, 'center', 0, 0);
    }
}
