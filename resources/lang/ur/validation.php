<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute کو قبول کیا جانا ضروری ہے۔',
    'active_url' => ':attribute ایک درست یو آر ایل نہیں ہے۔',
    'after' => ':attribute لازمی طور پر :date کے بعد کی تاریخ ہونی چاہیے۔',
    'after_or_equal' => ':attribute لازمی طور پر :date کے برابر یا اس کے بعد کی تاریخ ہونی چاہیے۔',
    'alpha' => ':attribute میں صرف حروف شامل ہو سکتے ہیں۔',
    'alpha_dash' => ':attribute میں صرف حروف، نمبرز، ڈیشز اور انڈر سکورز شامل ہو سکتے ہیں۔',
    'alpha_num' => ':attribute میں صرف حروف اور نمبرز شامل ہو سکتے ہیں۔',
    'array' => ':attribute لازمی طور پر ایک array ہونا چاہیے۔',
    'before' => ':attribute لازمی طور پر :date سے پہلے کی تاریخ ہونی چاہیے۔',
    'before_or_equal' => ':attribute لازمی طور پر :date کے برابر یا اس سے پہلے کی تاریخ ہونی چاہیے۔',
    'between' => [
        'numeric' => ':attribute لازمی طور پر :min اور :max کے درمیان ہونا چاہیے۔',
        'file' => ':attribute لازمی طور پر :min اور :max کلوبائٹس کے درمیان ہونا چاہیے۔',
        'string' => ':attribute لازمی طور پر :min اور :max حروف کے درمیان ہونا چاہیے۔',
        'array' => ':attribute میں لازمی طور پر :min اور :max آئٹمز کے درمیان ہونے چاہئیں۔',
    ],
    'boolean' => ':attribute فیلڈ لازمی طور پر true یا false ہونی چاہیے۔',
    'confirmed' => ':attribute کی تصدیق مماثل نہیں ہے۔',
    'date' => ':attribute ایک درست تاریخ نہیں ہے۔',
    'date_equals' => ':attribute لازمی طور پر :date کے برابر تاریخ ہونی چاہیے۔',
    'date_format' => ':attribute فارمیٹ :format سے مطابقت نہیں رکھتا۔',
    'different' => ':attribute اور :other مختلف ہونے چاہئیں۔',
    'digits' => ':attribute لازمی طور پر :digits ہندسوں پر مشتمل ہونا چاہیے۔',
    'digits_between' => ':attribute لازمی طور پر :min اور :max ہندسوں کے درمیان ہونا چاہیے۔',
    'dimensions' => ':attribute میں تصویر کی طول و عرض غلط ہیں۔',
    'distinct' => ':attribute فیلڈ میں ایک جیسی ویلیو موجود ہے۔',
    'email' => ':attribute لازمی طور پر ایک درست ای میل ایڈریس ہونا چاہیے۔',
    'ends_with' => ':attribute لازمی طور پر درج ذیل میں سے کسی ایک پر ختم ہونا چاہیے: :values۔',
    'exists' => 'منتخب کردہ :attribute غلط ہے۔',
    'file' => ':attribute لازمی طور پر ایک فائل ہونی چاہیے۔',
    'filled' => ':attribute فیلڈ میں لازمی طور پر ایک ویلیو ہونی چاہیے۔',
    'gt' => [
        'numeric' => ':attribute لازمی طور پر :value سے بڑا ہونا چاہیے۔',
        'file' => ':attribute لازمی طور پر :value کلوبائٹس سے بڑا ہونا چاہیے۔',
        'string' => ':attribute لازمی طور پر :value حروف سے بڑا ہونا چاہیے۔',
        'array' => ':attribute میں لازمی طور پر :value سے زیادہ آئٹمز ہونے چاہئیں۔',
    ],
    'gte' => [
        'numeric' => ':attribute لازمی طور پر :value کے برابر یا اس سے بڑا ہونا چاہیے۔',
        'file' => ':attribute لازمی طور پر :value کلوبائٹس کے برابر یا اس سے بڑا ہونا چاہیے۔',
        'string' => ':attribute لازمی طور پر :value حروف کے برابر یا اس سے بڑا ہونا چاہیے۔',
        'array' => ':attribute میں لازمی طور پر :value یا اس سے زیادہ آئٹمز ہونے چاہئیں۔',
    ],
    'image' => ':attribute لازمی طور پر ایک تصویر ہونی چاہیے۔',
    'in' => 'منتخب کردہ :attribute غلط ہے۔',
    'in_array' => ':attribute فیلڈ :other میں موجود نہیں ہے۔',
    'integer' => ':attribute لازمی طور پر ایک عدد صحیح ہونا چاہیے۔',
    'ip' => ':attribute لازمی طور پر ایک درست IP ایڈریس ہونا چاہیے۔',
    'ipv4' => ':attribute لازمی طور پر ایک درست IPv4 ایڈریس ہونا چاہیے۔',
    'ipv6' => ':attribute لازمی طور پر ایک درست IPv6 ایڈریس ہونا چاہیے۔',
    'json' => ':attribute لازمی طور پر ایک درست JSON سٹرنگ ہونی چاہیے۔',
    'lt' => [
        'numeric' => ':attribute لازمی طور پر :value سے کم ہونا چاہیے۔',
        'file' => ':attribute لازمی طور پر :value کلوبائٹس سے کم ہونا چاہیے۔',
        'string' => ':attribute لازمی طور پر :value حروف سے کم ہونا چاہیے۔',
        'array' => ':attribute میں لازمی طور پر :value سے کم آئٹمز ہونے چاہئیں۔',
    ],
    'lte' => [
        'numeric' => ':attribute لازمی طور پر :value کے برابر یا اس سے کم ہونا چاہیے۔',
        'file' => ':attribute لازمی طور پر :value کلوبائٹس کے برابر یا اس سے کم ہونا چاہیے۔',
        'string' => ':attribute لازمی طور پر :value حروف کے برابر یا اس سے کم ہونا چاہیے۔',
        'array' => ':attribute میں :value سے زیادہ آئٹمز نہیں ہونے چاہئیں۔',
    ],
    'max' => [
        'numeric' => ':attribute :max سے زیادہ نہیں ہونا چاہیے۔',
        'file' => ':attribute :max کلوبائٹس سے زیادہ نہیں ہونا چاہیے۔',
        'string' => ':attribute :max حروف سے زیادہ نہیں ہونا چاہیے۔',
        'array' => ':attribute میں :max سے زیادہ آئٹمز نہیں ہونے چاہئیں۔',
    ],
    'mimes' => ':attribute لازمی طور پر اس قسم کی فائل ہونی چاہیے: :values۔',
    'mimetypes' => ':attribute لازمی طور پر اس قسم کی فائل ہونی چاہیے: :values۔',
    'min' => [
        'numeric' => ':attribute لازمی طور پر کم از کم :min ہونا چاہیے۔',
        'file' => ':attribute لازمی طور پر کم از کم :min کلوبائٹس ہونا چاہیے۔',
        'string' => ':attribute لازمی طور پر کم از کم :min حروف ہونا چاہیے۔',
        'array' => ':attribute میں کم از کم :min آئٹمز ہونے چاہئیں۔',
    ],
    'multiple_of' => ':attribute لازمی طور پر :value کا مضرب ہونا چاہیے۔',
    'not_in' => 'منتخب کردہ :attribute غلط ہے۔',
    'not_regex' => ':attribute فارمیٹ غلط ہے۔',
    'numeric' => ':attribute لازمی طور پر ایک نمبر ہونا چاہیے۔',
    'password' => 'پاس ورڈ غلط ہے۔',
    'present' => ':attribute فیلڈ لازمی طور پر موجود ہونی چاہیے۔',
    'regex' => ':attribute فارمیٹ غلط ہے۔',
    'required' => ':attribute فیلڈ ضروری ہے۔',
    'required_if' => ':attribute فیلڈ ضروری ہے جب :other کی ویلیو :value ہو۔',
    'required_unless' => ':attribute فیلڈ ضروری ہے جب تک کہ :other میں :values شامل نہ ہو۔',
    'required_with' => ':attribute فیلڈ ضروری ہے جب :values موجود ہو۔',
    'required_with_all' => ':attribute فیلڈ ضروری ہے جب :values موجود ہوں۔',
    'required_without' => ':attribute فیلڈ ضروری ہے جب :values موجود نہ ہو۔',
    'required_without_all' => ':attribute فیلڈ ضروری ہے جب :values میں سے کوئی بھی موجود نہ ہو۔',
    'same' => ':attribute اور :other مماثل ہونے چاہئیں۔',
    'size' => [
        'numeric' => ':attribute لازمی طور پر :size ہونا چاہیے۔',
        'file' => ':attribute لازمی طور پر :size کلوبائٹس ہونا چاہیے۔',
        'string' => ':attribute لازمی طور پر :size حروف ہونا چاہیے۔',
        'array' => ':attribute میں لازمی طور پر :size آئٹمز ہونے چاہئیں۔',
    ],
    'starts_with' => ':attribute لازمی طور پر درج ذیل میں سے کسی ایک سے شروع ہونا چاہیے: :values۔',
    'string' => ':attribute لازمی طور پر ایک سٹرنگ ہونا چاہیے۔',
    'timezone' => ':attribute لازمی طور پر ایک درست زون ہونا چاہیے۔',
    'unique' => ':attribute پہلے سے لیا جا چکا ہے۔',
    'uploaded' => ':attribute اپ لوڈ کرنے میں ناکام رہا۔',
    'url' => ':attribute فارمیٹ غلط ہے۔',
    'uuid' => ':attribute لازمی طور پر ایک درست UUID ہونا چاہیے۔',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'مخصوص پیغام',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
