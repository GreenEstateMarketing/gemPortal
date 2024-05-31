<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Http\Requests\PropertyRequest as BaseRequest;
use Illuminate\Validation\Rule;

class PropertyRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'            => 'required|min:5|max:120',
          //  'description'     => 'max:350',
           // 'content'         => 'required',
            'number_bedroom'  => 'numeric|min:0|max:10000|nullable',
            'number_bathroom' => 'numeric|min:0|max:10000|nullable',
            'number_floor'    => 'numeric|min:0|max:10000|nullable',
            'square'        =>'required|min:0|max:99999999',
            'price'           => 'required|min:0|max:999999999999999',
            'images'            => ['required'],
            /*'document1'       =>'required', 
            'document2'       =>'required',
            'document3'       =>'required',*/
            'status'          => Rule::in(PropertyStatusEnum::values()),
        ];
    }
   /* public function messages()
    {
        return [
            'document1.required' => 'Completion Letter is required',
            'document2.required' => 'Allotment Letter is required',
            'document3.required' => 'Possession Letter is required',
        ];
    }*/
}
