<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportLocations extends Command
{
    protected $signature = 'locations:import';

    protected $description = 'Import Countries, States and Cities';

    public function handle()
    {
        $this->importCountries();
        $this->importStates();
        $this->importCities();

        $this->info('Finished.');
    }

    private function importCountries()
    {
$path = storage_path('app/location-data/countries.json');

$countries = json_decode(file_get_contents($path), true);

foreach ($countries as $country) {

    DB::table('countries')->updateOrInsert(

        ['name' => $country['name']],

        [

            'nationality' => $country['nationality'],

            'status' => 'published',

            'order' => 0,

            'is_default' => 0,

            'created_at' => now(),

            'updated_at' => now(),

        ]

    );

}

$this->info('Countries Imported');
    }

    private function importStates()
    {
        $path = storage_path('app/location-data/states.json');

$states = json_decode(file_get_contents($path), true);

foreach ($states as $state) {

    $country = DB::table('countries')

        ->where('name', $state['country_name'])

        ->first();

    if (!$country) {

        continue;

    }

    DB::table('states')->updateOrInsert(

        [

            'name' => $state['name'],

            'country_id' => $country->id,

        ],

        [

            'status' => 'published',

            'order' => 0,

            'created_at' => now(),

            'updated_at' => now(),

        ]

    );

}

$this->info('States Imported');

    }

    private function importCities()
    {
        $path = storage_path('app/location-data/countries+states+cities.json');

$data = json_decode(file_get_contents($path), true);

foreach ($data as $countryData) {

    $country = DB::table('countries')

        ->where('name', $countryData['name'])

        ->first();

    if (!$country) {

        continue;

    }

    foreach ($countryData['states'] as $stateData) {

        $state = DB::table('states')

            ->where('country_id', $country->id)

            ->where('name', $stateData['name'])

            ->first();

        if (!$state) {

            continue;

        }

        foreach ($stateData['cities'] as $city) {

            DB::table('cities')->updateOrInsert(

                [

                    'name' => $city['name'],

                    'state_id' => $state->id,

                ],

                [

                    'country_id' => $country->id,

                    'status' => 'published',

                    'order' => 0,

                    'is_default' => 0,

                    'is_featured' => 0,

                    'created_at' => now(),

                    'updated_at' => now(),

                ]

            );

        }

    }

}

$this->info('Cities Imported');

    }
}