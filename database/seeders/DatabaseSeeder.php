<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends BaseSeeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('cms:plugin:activate:all');

        $this->call(CurrencySeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(FacilitySeeder::class);
        $this->call(PackageSeeder::class);
        $this->call(AccountSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(PageSeeder::class);
        $this->call(ThemeOptionSeeder::class);
        $this->call(MenuSeeder::class);

        $this->uploadFiles('banner');
        $this->uploadFiles('cities');
        $this->uploadFiles('logo');
        $this->uploadFiles('projects');
        $this->uploadFiles('properties');
        $this->uploadFiles('users');
    }
}
