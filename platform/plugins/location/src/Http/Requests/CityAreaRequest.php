<?php

namespace Botble\Location\Http\Requests;

use Botble\Support\Http\Requests\Request;

class CityAreaRequest extends Request
{
    public function rules()
    {
        return [
            'city_area_name' => 'required',
            'city_id' => 'required',
        ];
    }
}