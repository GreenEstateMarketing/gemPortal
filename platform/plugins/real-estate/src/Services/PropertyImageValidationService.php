<?php

namespace Botble\RealEstate\Services;

use Illuminate\Http\UploadedFile;

/**
 * Validates a single property photo before it's handed to RvMedia::handleUpload().
 * Deliberately scoped to the property wizard's photo gallery uploader only - the
 * sibling document-slot uploader, avatars, and every other upload path in the app
 * are untouched and keep their own (looser) rules.
 */
class PropertyImageValidationService
{
    public const MIN_WIDTH = 1024;

    public const MIN_HEIGHT = 768;

    public const MAX_FILE_SIZE_BYTES = 5 * 1024 * 1024;

    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    public const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    // Reject only extreme slivers (e.g. a panorama or a UI screenshot) - anything
    // within a 3:1 ratio either way still passes, so normal portrait/landscape/
    // square phone photos are never rejected on shape alone.
    public const MAX_ASPECT_RATIO = 3.0;

    /**
     * Returns null if the file is acceptable, or a user-facing error message if not.
     */
    public function validate(UploadedFile $file): ?string
    {
        if (! $file->isValid()) {
            // The web server's own upload_max_filesize/post_max_size cut this
            // upload off before PHP could save it to a temp file - getMimeType()/
            // getSize()/getRealPath() below would throw or return garbage on a
            // file that doesn't actually exist on disk, so this has to be caught
            // here first rather than falling through to those checks.
            if (in_array($file->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
                return 'This image is too large. Please upload a photo smaller than 5MB.';
            }

            return 'This image could not be uploaded. Please try again.';
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();

        if (
            ! in_array($extension, self::ALLOWED_EXTENSIONS, true)
            || ! in_array($mimeType, self::ALLOWED_MIME_TYPES, true)
        ) {
            return 'Please upload a photo in JPG, PNG, or WEBP format.';
        }

        if ($file->getSize() > self::MAX_FILE_SIZE_BYTES) {
            return 'This image is too large. Please upload a photo smaller than 5MB.';
        }

        $dimensions = @getimagesize($file->getRealPath());

        if ($dimensions === false) {
            return 'This image appears to be corrupted or unsupported. Please upload a different photo.';
        }

        [$width, $height] = $dimensions;

        if ($width < self::MIN_WIDTH || $height < self::MIN_HEIGHT) {
            return sprintf(
                'This image resolution is too low. Please upload an image with a minimum resolution of %d × %d pixels.',
                self::MIN_WIDTH,
                self::MIN_HEIGHT
            );
        }

        $ratio = $width / $height;

        if ($ratio > self::MAX_ASPECT_RATIO || $ratio < (1 / self::MAX_ASPECT_RATIO)) {
            return "This image has an unusual shape and can't be used. Please upload a normal photo of the property.";
        }

        return null;
    }
}
