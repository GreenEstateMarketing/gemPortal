<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;

class MemberEditRequest extends Request
{
    public function rules()
    {
        return [
            'full_name' => 'required|max:120|min:2',
            'email' => 'required|max:60|min:6|email|unique:members,email,' . $this->route('member'),
            'mobile_no' => 'required|min:11|numeric|regex:^[0][\d]{3}[\d]{7}$^',
            'credits' => 'numeric'
        ];
    }
}