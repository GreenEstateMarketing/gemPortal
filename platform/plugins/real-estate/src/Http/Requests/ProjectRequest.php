<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\RealEstate\Enums\ProjectStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ProjectRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'         => 'required|min:5|max:120',
            'description'  => 'max:1000',
            'content'      => 'required',
            'city_id'      => 'required|not_in:0',
            'city_area_id' => 'required|not_in:0',
            'location'     => 'required|string',
            'images'       => ['required'],
            'number_block' => 'numeric|min:0|max:10000|nullable',
            'number_floor' => 'numeric|min:0|max:10000|nullable',
            'number_flat'  => 'numeric|min:0|max:10000|nullable',
            'price_from'   => 'numeric|min:0|required',
            'price_to'     => 'numeric|min:0|required',
            'status'       => Rule::in(ProjectStatusEnum::values()),
        ];
    }
}
