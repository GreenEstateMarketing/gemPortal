<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;
use Illuminate\Support\Arr;
use Route;
use Illuminate\Validation\Rule;

class CurrencyRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $currencyId = $this->route('currency');

        $rules = [
            'title'  => 'required|string',
            'symbol' => [
                'required',
                'string',
                'max:30',
                Rule::unique('re_currencies')->ignore($currencyId),
            ],
            'order'  => 'integer|min:0',
        ];

        return $rules;
    }
}