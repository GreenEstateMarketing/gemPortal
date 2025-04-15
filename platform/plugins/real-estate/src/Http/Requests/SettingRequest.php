<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;

class SettingRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username'   => 'required|max:60|min:2|unique:re_accounts,username,' . auth('account')->user()->getAuthIdentifier(),
            'first_name' => 'required|max:120',
            'last_name'  => 'required|max:120',
            'phone' => ['required', 'regex:/^\+?[1-9][0-9]{7,14}$/'],
            'dob'        => 'max:20|sometimes',
        ];
    }

    public function messages()
    {
        return [
            'phone.regex' => 'The phone number format is invalid. It must be a valid international number, e.g., +1234567890.',
        ];
    }
}
