<?php

namespace Botble\RealEstate\Http\Requests;

use Botble\Support\Http\Requests\Request;

class StoreAgentApplicationRequest extends Request
{
    /**
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:60',
            'phone' => ['required', 'regex:/^\+[0-9]{11}$/'],
            'education_level' => 'required|string|max:60',
            'years_of_experience' => 'required|integer|min:0|max:60',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'integer|exists:re_categories,id',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ];

        if (setting('enable_captcha') && is_plugin_active('captcha')) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        return $rules;
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => trans('plugins/real-estate::agent-application.form.name.required'),
            'email.required' => trans('plugins/real-estate::agent-application.form.email.required'),
            'email.email' => trans('plugins/real-estate::agent-application.form.email.email'),
            'phone.required' => trans('plugins/real-estate::agent-application.form.phone.required'),
            'phone.regex' => trans('plugins/real-estate::agent-application.form.phone.regex'),
            'education_level.required' => trans('plugins/real-estate::agent-application.form.education_level.required'),
            'years_of_experience.required' => trans('plugins/real-estate::agent-application.form.years_of_experience.required'),
            'category_ids.required' => trans('plugins/real-estate::agent-application.form.category_ids.required'),
            'resume.required' => trans('plugins/real-estate::agent-application.form.resume.required'),
            'resume.mimes' => trans('plugins/real-estate::agent-application.form.resume.mimes'),
            'resume.max' => trans('plugins/real-estate::agent-application.form.resume.max'),
            'g-recaptcha-response.required' => trans('plugins/real-estate::agent-application.form.g-recaptcha-response.required'),
            'g-recaptcha-response.captcha' => trans('plugins/real-estate::agent-application.form.g-recaptcha-response.captcha'),
        ];
    }
}
