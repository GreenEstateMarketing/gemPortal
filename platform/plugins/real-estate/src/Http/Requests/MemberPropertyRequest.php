<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\RealEstate\Enums\PropertyStatusEnum;
use Illuminate\Validation\Rule;

class MemberPropertyRequest extends PropertyRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:5|max:120',
            'number_bedroom' => 'numeric|min:0|max:10000|nullable',
            'number_bathroom' => 'numeric|min:0|max:10000|nullable',
            'number_floor' => 'numeric|min:0|max:10000|nullable',
            'square' => 'required|min:0|max:99999999',
            'price' => 'required|min:0|max:999999999999999',
            'status' => Rule::in(PropertyStatusEnum::values()),
        ];
    }
}
