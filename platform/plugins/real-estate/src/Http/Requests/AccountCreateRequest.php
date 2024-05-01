<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;

class AccountCreateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|min:3|max:120|regex:/[a-zA-Z]{3,}(?: [a-zA-Z]+){0,2}$/',
            'last_name'  => 'required|min:3|max:120|regex:/[a-zA-Z]{3,}(?: [a-zA-Z]+){0,2}$/',
            'username'   => 'required|max:60|min:2|unique:re_accounts,username',
            'email'      => 'required|max:60|min:6|email|unique:re_accounts',
            'phone'   => 'min:11|numeric|regex:/[0][\d]{3}[\d]{7}$/',
            'password'   => 'required|min:6|confirmed',
        ];
    }
}
