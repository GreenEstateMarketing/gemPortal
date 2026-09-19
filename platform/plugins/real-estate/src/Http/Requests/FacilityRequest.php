<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\RealEstate\Enums\GooglePlaceTypeEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class FacilityRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'                  => 'required',
            'status'                => Rule::in(BaseStatusEnum::values()),
            'google_place_type'     => ['nullable', Rule::in(array_merge([''], GooglePlaceTypeEnum::toArray()))],
            'google_place_keyword'  => 'nullable|string|max:120',
            'google_place_radius'   => 'nullable|integer|min:100|max:50000',
        ];
    }
}
