<?php

namespace Botble\RealEstate\Jobs;

use Botble\Media\Repositories\Interfaces\MediaFileInterface;
use Botble\RealEstate\Services\PropertyImageEnhancementService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class EnhancePropertyImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    public $mediaFileId;

    public function __construct(int $mediaFileId)
    {
        $this->mediaFileId = $mediaFileId;
    }

    public function handle(MediaFileInterface $fileRepository, PropertyImageEnhancementService $enhancer)
    {
        try {
            $file = $fileRepository->findById($this->mediaFileId);

            if (!$file) {
                return;
            }

            $enhancer->process($file);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
