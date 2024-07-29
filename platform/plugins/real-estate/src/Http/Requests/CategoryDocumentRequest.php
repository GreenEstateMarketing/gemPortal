<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;

class CategoryDocumentRequest extends Request
{
    public function rules()
    {
        return [
            'category_id' => 'required',
            'document_id' => 'required'
        ];
    }
}