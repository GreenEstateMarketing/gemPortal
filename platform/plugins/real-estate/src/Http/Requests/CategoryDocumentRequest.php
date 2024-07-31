<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CategoryDocumentRequest extends Request
{
    public function rules()
    {
        return [
            'category_id' => 'required',
            'document_id' => [
                'required',
                'integer',
                Rule::unique('category_documents')->where(function ($query) {
                    return $query->where('category_id', $this->category_id);
                }),
            ],
        ];
    }

    public function messages()
    {
        return [
            'document_id.unique' => 'Such a record already exists.',
        ];
    }
}