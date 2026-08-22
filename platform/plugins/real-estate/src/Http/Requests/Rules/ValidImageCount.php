<?php
namespace Botble\RealEstate\Http\Requests\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidImageCount implements Rule
{
    protected $min;
    protected $max;

    public function __construct($min = 1, $max = 20)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function passes($attribute, $value)
    {
        if(!is_array($value)) {
            $value = explode(',', $value);
        }

        $count = count($value);

        return $count >= $this->min && $count <= $this->max;
    }

    public function message()
    {
        return "Please upload no more than 20 images.";
    }
}
