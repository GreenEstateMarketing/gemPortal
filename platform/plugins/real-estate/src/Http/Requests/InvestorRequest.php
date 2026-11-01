<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\RealEstate\Http\Requests\Rules\MinImageDimension;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class InvestorRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'        => 'required',
            'description' => 'nullable|max:1000',
            'image'       => ['nullable', new MinImageDimension(1200, 600)],
            'status'      => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
