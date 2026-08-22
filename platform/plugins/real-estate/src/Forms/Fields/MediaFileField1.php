<?php

namespace Botble\RealEstate\Forms\Fields;

use Illuminate\Support\Arr;
use Kris\LaravelFormBuilder\Fields\FormField;
class MediaFileField1 extends FormField
{

    /**
     * @return string
     */
    protected function getTemplate()
    {
       // return 'core/base::forms.fields.media-file';
        return 'plugins/real-estate::forms.fields.media-field1';
    }

    /**
     * @param array $options
     * @param bool $showLabel
     * @param bool $showField
     * @param bool $showError
     * @return string
     */

}

