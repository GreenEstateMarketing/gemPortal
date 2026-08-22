<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;
use Botble\RealEstate\Http\Requests\Rules\ImageDimension;

class AccountCreateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'first_name' => 'required|min:3|max:120|regex:/[a-zA-Z]{3,}(?: [a-zA-Z]+){0,2}$/',
            'last_name' => 'required|min:3|max:120|regex:/[a-zA-Z]{3,}(?: [a-zA-Z]+){0,2}$/',
            'username' => 'required|max:60|min:2|unique:re_accounts,username',
            'email' => 'required|max:60|min:6|email|unique:re_accounts',
            'phone' => ['required', 'regex:/^\+?[1-9][0-9]{7,14}$/'],
            'password' => 'required|min:6|confirmed',
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'city_area_id' => ['required', 'array'],
            'city_area_id.*' => 'string|max:256'
        ];

       

        if ($this->hasFile('image_path')) {
            $rules['image_path'] = [new ImageDimension(500, 500)];
        }

        return $rules;
    }


    public function messages()
    {
        return [
            'phone.regex' => 'The phone number format is invalid. It must be a valid international number, e.g., +1234567890.',
        ];
    }

    
}
