<?php

namespace Botble\Contact\Http\Requests;

use Botble\Support\Http\Requests\Request;

class ContactRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function rules()
    {
        if (setting('enable_captcha') && is_plugin_active('captcha')) {
            return [
                'name'                 => 'required',
                'email'                => 'required|email',
                'content'              => 'required',
                'g-recaptcha-response' => 'required|captcha',
                'phone' => ['required', 'regex:/^\+?[1-9][0-9]{7,14}$/'],
            ];
        }
        return [
            'name'    => 'required',
            'email'   => 'required|email',
            'content' => 'required',
            'phone' => ['required', 'regex:/^\+?[1-9][0-9]{7,14}$/'],
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'    => trans('plugins/contact::contact.form.name.required'),
            'email.required'   => trans('plugins/contact::contact.form.email.required'),
            'email.email'      => trans('plugins/contact::contact.form.email.email'),
            'content.required' => trans('plugins/contact::contact.form.content.required'),
            'phone.regex' => 'The phone number format is invalid. It must be a valid international number, e.g., +1234567890.',
        ];
    }
}
