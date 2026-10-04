<?php

namespace Botble\Media\Events;

use Botble\Base\Events\Event;
use Botble\Media\Models\MediaFile;
use Illuminate\Queue\SerializesModels;

class MediaFileUploaded extends Event
{
    use SerializesModels;

    /**
     * @var MediaFile
     */
    public $file;

    public function __construct(MediaFile $file)
    {
        $this->file = $file;
    }
}
