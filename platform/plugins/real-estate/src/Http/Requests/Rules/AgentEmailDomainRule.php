<?php

namespace Botble\RealEstate\Http\Requests\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class AgentEmailDomainRule implements Rule
{
    protected const REQUIRED_DOMAIN = 'gemlisting.co';

    public function passes($attribute, $value)
    {
        return Str::endsWith(Str::lower((string) $value), '@' . self::REQUIRED_DOMAIN);
    }

    public function message()
    {
        return 'The :attribute must be a @' . self::REQUIRED_DOMAIN . ' email address.';
    }
}
