<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class TemplateRequest extends Request
{
    public function rules()
    {
        return [
            'name' => 'required',
            'detail' => 'required',
            'type' => 'required',
            'status' => 'required',
            'category_id' => [
                'required',
                Rule::unique('description_template')->ignore($this->route('template')),
            ],
        ];
    }

    public function messages()
    {
        return [
            'category_id.unique' => 'Record exists for this category.',
        ];
    }
}