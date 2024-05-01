<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Setting\Models\Setting as SettingModel;
use Theme;

class ThemeOptionSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->uploadFiles('general');

        $theme = Theme::getThemeName();

        $data = [
            'en_US' => [
                [
                    'key'   => 'cookie_consent_message',
                    'value' => 'Your experience on this site will be improved by allowing cookies ',
                ],
                [
                    'key'   => 'cookie_consent_learn_more_url',
                    'value' => url('cookie-policy'),
                ],
                [
                    'key'   => 'cookie_consent_learn_more_text',
                    'value' => 'Cookie Policy',
                ],
                [
                    'key'   => 'homepage_id',
                    'value' => '1',
                ],
                [
                    'key'   => 'blog_page_id',
                    'value' => '2',
                ],
            ],

            'vi' => [
                [
                    'key'   => 'cookie_consent_message',
                    'value' => 'Trải nghiệm của bạn trên trang web này sẽ được cải thiện bằng cách cho phép cookie ',
                ],
                [
                    'key'   => 'cookie_consent_learn_more_url',
                    'value' => url('cookie-policy'),
                ],
                [
                    'key'   => 'cookie_consent_learn_more_text',
                    'value' => 'Cookie Policy',
                ],
                [
                    'key'   => 'homepage_id',
                    'value' => '7',
                ],
                [
                    'key'   => 'blog_page_id',
                    'value' => '8',
                ],
            ],
        ];

        foreach ($data as $locale => $options) {
            foreach ($options as $item) {
                $item['key'] = 'theme-' . $theme . '-' . ($locale != 'en_US' ? $locale . '-' : '') . $item['key'];

                SettingModel::where('key', $item['key'])->delete();

                SettingModel::create($item);
            }
        }
    }
}
