<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\RealEstate\Models\Facility;
use Botble\RealEstate\Models\Property;
use Illuminate\Support\Facades\Artisan;

class FacilitySeeder extends BaseSeeder
{
    public function run()
    {
        Facility::truncate();

        $facilities = [
            [
                'name' => 'Hospital',
                'icon' => 'far fa-hospital',
                'google_place_type' => 'hospital',
            ],
            [
                'name' => 'Super Market',
                'icon' => 'fas fa-cart-plus',
                'google_place_type' => 'supermarket',
            ],
            [
                'name' => 'School',
                'icon' => 'fas fa-school',
                'google_place_type' => 'school',
            ],
            [
                'name' => 'Entertainment',
                'icon' => 'fas fa-hotel',
                'google_place_keyword' => 'entertainment',
            ],
            [
                'name' => 'Pharmacy',
                'icon' => 'fas fa-prescription-bottle-alt',
                'google_place_type' => 'pharmacy',
            ],
            [
                'name' => 'Airport',
                'icon' => 'fas fa-plane-departure',
                'google_place_type' => 'airport',
                // Google's "airport" type also matches travel agencies,
                // insurance agents, and meet & greet services - a keyword
                // hint keeps results actually airport-named (paired with the
                // isNoisyPlace() filter in location.blade.php for the rest).
                'google_place_keyword' => 'airport',
                'google_place_radius' => 20000,
            ],
            [
                'name' => 'Railways',
                'icon' => 'fas fa-subway',
                'google_place_type' => 'train_station',
                'google_place_keyword' => 'railway station',
            ],
            [
                'name' => 'Bus Stop',
                'icon' => 'fas fa-bus',
                'google_place_type' => 'bus_station',
            ],
            [
                'name' => 'Beach',
                'icon' => 'fas fa-umbrella-beach',
                'google_place_keyword' => 'beach',
            ],
            [
                'name' => 'Mall',
                'icon' => 'fas fa-cart-plus',
                'google_place_type' => 'shopping_mall',
            ],
            [
                'name' => 'Bank',
                'icon' => 'fas fa-university',
                'google_place_type' => 'bank',
            ],
        ];

        foreach ($facilities as $facility) {
            Facility::create($facility);
        }

        if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
            Artisan::call('cms:language:sync', ['class' => Facility::class]);
        }

        foreach (Property::get() as $property) {
            $property->facilities()->detach();
            for ($i = 1; $i < 12; $i++) {
                $property->facilities()->attach($i, ['distance' => rand(1, 20) . 'km']);
            }
        }
    }
}
