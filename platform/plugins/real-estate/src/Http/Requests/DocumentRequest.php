<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;

class DocumentRequest extends Request
{
    public function rules()
    {
        return [
            'name' => 'required',
            'type' => 'required'
        ];
    }
}