<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddGooglePlaceTypeToReFacilities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('re_facilities', function (Blueprint $table) {
            $table->string('google_place_type', 60)->nullable()->after('icon');
        });

        // Preserve auto-detection for installs already relying on the old
        // hardcoded name -> Google Places type map (previously baked into
        // the wizard's JS) by seeding it into the new column for any
        // facility whose name matches exactly.
        $legacyTypeMap = [
            'Hospital' => 'hospital',
            'Super Market' => 'supermarket',
            'School' => 'school',
            'Pharmacy' => 'pharmacy',
            'Airport' => 'airport',
            'Railways' => 'train_station',
            'Bus Stop' => 'bus_station',
            'Mall' => 'shopping_mall',
            'Bank' => 'bank',
        ];

        foreach ($legacyTypeMap as $name => $type) {
            DB::table('re_facilities')->where('name', $name)->update(['google_place_type' => $type]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('re_facilities', function (Blueprint $table) {
            $table->dropColumn('google_place_type');
        });
    }
}
