<?php

namespace Botble\Location\Http\Requests;

use Botble\Support\Http\Requests\Request;

class CityAreaRequest extends Request
{
    public function rules()
    {
        return [
            'city_area_name' => 'required|unique:city_area,city_area_name,NULL,id,city_id,' . $this->input('city_id'),
            'city_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'city_area_name.unique' => 'City area already exists in the selected city.'
        ];
    }
}