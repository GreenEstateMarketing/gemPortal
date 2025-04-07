<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAccountsAddCityAreaColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('re_accounts', 'city_id')) {
            Schema::table('re_accounts', function (Blueprint $table) {
                $table->integer('city_id');
            });
        }

        if(!Schema::hasColumn('re_accounts', 'city_area_id')) {
            Schema::table('re_accounts', function (Blueprint $table) {
                $table->integer('city_area_id');
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
        if(Schema::hasColumn('re_accounts', 'city_id')) {
            Schema::table('re_accounts', function (Blueprint $table) {
                $table->dropColumn('city_id');
            });
        }

        if(Schema::hasColumn('re_accounts', 'city_area_id')) {
            Schema::table('re_accounts', function (Blueprint $table) {
                $table->dropColumn('city_area_id');
            });
        }
    }
}
