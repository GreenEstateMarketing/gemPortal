<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WidenLatitudeLongitudeOnRePropertiesAndReProjects extends Migration
{
    /**
     * decimal(10,8) only leaves 2 digits before the decimal point, so it
     * silently overflows ("Numeric value out of range") for any longitude
     * with a 3-digit integer part - i.e. anywhere outside roughly -99..99,
     * which covers most of the world (all of the Americas outside a strip
     * of South America, all of Asia/Australia, etc). Only went unnoticed
     * before because all prior data was in Pakistan, whose longitudes stay
     * under 100. decimal(11,8) covers the full -180..180 range.
     *
     * @return void
     */
    public function up()
    {
        foreach (['re_properties', 're_projects'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->decimal('latitude', 11, 8)->nullable()->change();
                $table->decimal('longitude', 11, 8)->nullable()->change();
            });
        }
    }

    /**
     * @return void
     */
    public function down()
    {
        foreach (['re_properties', 're_projects'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->decimal('latitude', 10, 8)->nullable()->change();
                $table->decimal('longitude', 10, 8)->nullable()->change();
            });
        }
    }
}
