<?php

use Botble\RealEstate\Enums\ConsultStatusEnum;

return [
    'name'                => 'استفسارات',
    'property'                => 'جائیداد کا نام',
    'edit'                => 'استفسار دیکھیں',
    'statuses'            => [
        'read'   => 'پڑھا گیا',
        'unread' => 'نہیں پڑھا گیا',
    ],
    'phone'               => 'فون',
    'settings'            => [
        'email' => [
            'title'       => 'استفسار',
            'description' => 'استفسار ای میل کنفیگریشن',
            'templates'   => [
                'notice_title'       => 'منتظم کو نوٹس بھیجیں',
                'notice_description' => 'نئے استفسار موصول ہونے پر منتظم کو نوٹس بھیجنے کے لیے ای میل ٹیمپلیٹ',
            ],
        ],
    ],
    'content'             => 'تفصیلات',
    'consult_information' => 'استفسار کی معلومات',
    'email'               => [
        'header'  => 'ای میل',
        'title'   => 'آپ کی سائٹ سے نیا استفسار',
        'success' => 'استفسار کامیابی سے بھیج دیا گیا!',
        'failed'  => 'اس وقت درخواست نہیں بھیجی جا سکتی، براہ کرم بعد میں دوبارہ کوشش کریں!',
    ],
    'form'                => [
        'name'                 => [
            'required' => 'نام درکار ہے',
        ],
        'email'                => [
            'required' => 'ای میل درکار ہے',
            'email'    => 'ای میل ایڈریس درست نہیں ہے',
        ],
        'content'              => [
            'required' => 'مواد درکار ہے',
        ],
        'g-recaptcha-response' => [
            'required' => 'پیغام بھیجنے سے پہلے براہ کرم تصدیق کریں کہ آپ روبوٹ نہیں ہیں۔',
            'captcha'  => 'ابھی تک آپ روبوٹ نہ ہونے کی تصدیق نہیں ہوئی۔',
        ],
    ],
    'consult_sent_from'   => 'یہ استفسار کی معلومات اس سے بھیجی گئی',
    'time'                => 'وقت',
    'consult_id'          => 'استفسار کی شناخت',
    'form_name'           => 'نام',
    'form_email'          => 'ای میل',
    'form_phone'          => 'فون',
    'mark_as_read'        => 'پڑھا گیا کے طور پر نشان زد کریں',
    'mark_as_unread'      => 'نہیں پڑھا گیا کے طور پر نشان زد کریں',
    'new_consult_notice'  => 'آپ کے پاس <span class="bold">:count</span> نئے استفسارات ہیں',
    'view_all'            => 'سب دیکھیں',
    'project'             => 'منصوبہ',
    'property'            => 'جائیداد',
    'preferred_visit_date' => 'ترجیحی دورے کی تاریخ',
    'preferred_visit_time' => 'ترجیحی دورے کا وقت',
];
