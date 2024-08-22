<?php

namespace Botble\RealEstate\Http\Requests\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\File;

class ImageDimension implements Rule
{
    protected $width;
    protected $height;

    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function passes($attribute, $value)
    {
        $filePath = public_path('storage/' . $value);

        if (File::exists($filePath)) {
            $image = getimagesize($filePath);
            $imageWidth = $image[0];
            $imageHeight = $image[1];

            return $imageWidth <= $this->width && $imageHeight <= $this->height;
        }

        return false;
    }

    public function message()
    {
        return "The :attribute must be an image with dimensions of max {$this->width}x{$this->height} pixels.";
    }
}
