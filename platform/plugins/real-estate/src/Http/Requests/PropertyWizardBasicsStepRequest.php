<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class PropertyWizardBasicsStepRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|min:5|max:120',
            'type' => ['required', Rule::in(['sale', 'rent'])],
            'category_id' => 'required|not_in:0',
            'project_id' => 'nullable',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0|max:999999999999999',
            'currency_id' => 'nullable',
            'price_unit' => 'nullable|string|max:120',
            'square' => 'required|numeric|min:0|max:99999999',
            'area_units' => ['required', Rule::in(['ft2', 'm2', 'marla', 'yards', 'kanal'])],
            'number_bedroom' => 'nullable|numeric|min:0|max:10000',
            'number_bathroom' => 'nullable|numeric|min:0|max:10000',
            'number_floor' => 'nullable|numeric|min:0|max:10000',
            'built_in' => 'nullable|string|max:191',
        ];
    }
}
