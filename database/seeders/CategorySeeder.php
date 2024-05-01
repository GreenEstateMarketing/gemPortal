<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Language\Models\LanguageMeta;
use Botble\RealEstate\Models\Category;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use SlugHelper;

class CategorySeeder extends BaseSeeder
{
    public function run()
    {
        Category::truncate();

        $categories = [
            [
                'name'       => 'Apartment',
                'is_default' => true,
                'order'      => 0,
            ],
            [
                'name'       => 'Villa',
                'is_default' => false,
                'order'      => 1,
            ],
            [
                'name'       => 'Condo',
                'is_default' => false,
                'order'      => 2,
            ],
            [
                'name'       => 'House',
                'is_default' => false,
                'order'      => 3,
            ],
            [
                'name'       => 'Land',
                'is_default' => false,
                'order'      => 4,
            ],
            [
                'name'       => 'Commercial property',
                'is_default' => false,
                'order'      => 5,
            ],
        ];

        Category::truncate();
        Slug::where('reference_type', Category::class)->delete();
        if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
            LanguageMeta::where('reference_type', Category::class)->delete();
        }

        foreach ($categories as $item) {
            $category = Category::create($item);

            Slug::create([
                'reference_type' => Category::class,
                'reference_id'   => $category->id,
                'key'            => Str::slug($category->name),
                'prefix'         => SlugHelper::getPrefix(Category::class),
            ]);
        }

        if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
            Artisan::call('cms:language:sync', ['class' => Category::class]);
        }

        $properties = Property::get();

        foreach ($properties as $property) {
            $property->category_id = Category::inRandomOrder()->value('id');
            $property->save();
        }

        $projects = Project::get();

        foreach ($projects as $project) {
            $project->category_id = Category::inRandomOrder()->value('id');
            $project->save();
        }
    }
}
