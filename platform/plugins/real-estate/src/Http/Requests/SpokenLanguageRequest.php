<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;

class SpokenLanguageRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:120',
            'order' => 'nullable|integer',
            'status' => 'required',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => trans('plugins/real-estate::spoken-language.messages.request.name_required'),
        ];
    }
}
