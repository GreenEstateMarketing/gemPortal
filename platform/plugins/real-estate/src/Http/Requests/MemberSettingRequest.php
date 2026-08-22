<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;

class MemberSettingRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            'full_name' => 'required|max:120',
            'mobile_no'      => 'required:max:20|sometimes|regex:/^([0-9\s\-\+\(\)]*)$/'

        ];
    }
}
