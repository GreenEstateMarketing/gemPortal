<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGooglePlacesMappingToReFacilitiesTable extends Migration
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
            $table->string('google_place_keyword', 120)->nullable()->after('google_place_type');
            $table->unsignedInteger('google_place_radius')->nullable()->after('google_place_keyword');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('re_facilities', function (Blueprint $table) {
            $table->dropColumn(['google_place_type', 'google_place_keyword', 'google_place_radius']);
        });
    }
}
