<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCityAreaAddTimestamps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('city_area', 'created_at') && !Schema::hasColumn('city_area', 'updated_at')) {
            Schema::table('city_area', function (Blueprint $table) {
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('city_area', 'created_at') && Schema::hasColumn('city_area', 'updated_at')) {
            Schema::table('city_area', function (Blueprint $table) {
                $table->dropTimestamps();
            });
        }
    }
}
