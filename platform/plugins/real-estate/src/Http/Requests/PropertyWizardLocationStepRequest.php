<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;

class PropertyWizardLocationStepRequest extends Request
{
    public function rules(): array
    {
        return [
            'country_id' => 'required|not_in:0',
            'state_id' => 'required|not_in:0',
            'city_id' => 'required|not_in:0',
            'city_area_id' => 'required|not_in:0',
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'features' => 'nullable|array',
            'features.*' => 'integer',
            'facilities' => 'nullable|array',
            'facilities.*.id' => 'nullable|integer',
            'facilities.*.distance' => 'nullable|string|max:191',
        ];
    }

    public function messages(): array
    {
        return [
            'city_id.not_in' => 'Choose city from list',
            'city_area_id.not_in' => 'Choose city area from list',
            'state_id.required' => 'Please select a state.',
            'state_id.not_in' => 'Please select a state.',
        ];
    }
}
