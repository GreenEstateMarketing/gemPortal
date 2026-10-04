<?php

namespace Botble\RealEstate\Listeners;

use Botble\Media\Events\MediaFileUploaded;
use Botble\RealEstate\Jobs\EnhancePropertyImageJob;
use Exception;
use Illuminate\Support\Str;

/**
 * MediaFileUploaded fires for every upload sitewide (avatars, blog, theme
 * logos included), since property/project images share the same generic
 * Media Library endpoint as everything else. The Referer header is the
 * only available signal to scope enhancement + the centered watermark to
 * property/project images without touching any other upload type - it
 * covers all four upload paths (member wizard, agent wizard, admin
 * property wizard, admin project form) with one check.
 */
class EnhancePropertyImageListener
{
    protected $refererNeedles = [
        'member/properties',
        'account/properties',
        'real-estate/properties',
        'real-estate/projects',
    ];

    public function handle(MediaFileUploaded $event)
    {
        try {
            if (!$event->file->canGenerateThumbnails()) {
                return;
            }

            $referer = request()->headers->get('referer');

            if (!$referer || !Str::contains($referer, $this->refererNeedles)) {
                return;
            }

            EnhancePropertyImageJob::dispatch($event->file->id);
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
