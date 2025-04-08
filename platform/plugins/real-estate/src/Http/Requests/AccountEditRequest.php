<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;
use Botble\RealEstate\Http\Requests\Rules\ImageDimension;

class AccountEditRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'first_name' => 'required|max:120|min:2',
            'last_name'  => 'required|max:120|min:2',
            'username'   => 'required|max:60|min:2|unique:re_accounts,username,' . $this->route('account'),
            'email'      => 'required|max:60|min:6|email|unique:re_accounts,email,' . $this->route('account'),
            'image_path' => [new ImageDimension(500, 500)],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'city_area_id' => ['required', 'array'],
            'city_area_id.*' => 'string|max:256'
        ];

        if ($this->input('is_change_password') == 1) {
            $rules['password'] = 'required|min:6|confirmed';
        }

        return $rules;
    }
}
