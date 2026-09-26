<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\RealEstate\Models\SpokenLanguage;

class SpokenLanguageSeeder extends BaseSeeder
{
    public function run()
    {
        $languages = [
            'English',
            'Urdu',
            'Punjabi',
            'Pashto',
            'Sindhi',
            'Saraiki',
            'Balochi',
            'Arabic',
            'French',
            'Chinese',
        ];

        foreach ($languages as $index => $name) {
            SpokenLanguage::query()->firstOrCreate(
                ['name' => $name],
                ['order' => $index, 'status' => 'published']
            );
        }
    }
}
