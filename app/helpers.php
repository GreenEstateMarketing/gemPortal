<?php

if (!function_exists('current_site_direction')) {
    /**
     * Text direction ("ltr"/"rtl") for the active front-end locale. Only
     * "en"/"ur" are supported today, so this is a plain two-way check
     * rather than a lookup against Botble's locale table (which keys
     * locales like "en_US", not the plain "en"/"ur" app()->getLocale()
     * actually returns).
     */
    function current_site_direction(): string
    {
        return app()->getLocale() === 'ur' ? 'rtl' : 'ltr';
    }
}
