<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class PropertyRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'              => 'required|min:5|max:120',
            'number_bedroom'    => 'numeric|min:0|max:10000|nullable',
            'number_bathroom'   => 'numeric|min:0|max:10000|nullable',
            'number_floor'      => 'numeric|min:0|max:10000|nullable',
            'square'        =>'required|min:0|max:99999999',
            'price'           => 'required|min:0|max:999999999999999',
            'city_id'           => 'required|not_in:0',
            'city_area_id'      => 'required|not_in:0',
            'location'          => 'required|string',
            'images'            => ['required'],
            'status'            => Rule::in(PropertyStatusEnum::values()),
            'moderation_status' => Rule::in(ModerationStatusEnum::values()),

        ];
    }
    public function messages()
    {
        return [
            'city_id.not_in' => 'Choose city from list',
            'city_area_id.not_in'  => 'Choose city area from list',
        ];
    }

}
