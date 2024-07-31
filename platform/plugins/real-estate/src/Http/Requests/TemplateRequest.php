<?php

namespace Botble\RealEstate\Http\Requests;

class TemplateRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'detail' => 'required',
            'type' => 'required',
            'status' => 'required',
            'category_id' => 'required'
        ];
    }
}