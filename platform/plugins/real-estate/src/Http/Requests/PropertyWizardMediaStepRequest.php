<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\RealEstate\Http\Requests\Rules\ValidImageCount;
use Botble\Support\Http\Requests\Request;

class PropertyWizardMediaStepRequest extends Request
{
    public function rules(): array
    {
        return [
            'images' => ['required', 'array', new ValidImageCount(1, 20)],
            'documents' => 'nullable|array',
            'auto_renew' => 'nullable|boolean',
            'never_expired' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'moderation_status' => 'nullable|string',
        ];
    }
}
