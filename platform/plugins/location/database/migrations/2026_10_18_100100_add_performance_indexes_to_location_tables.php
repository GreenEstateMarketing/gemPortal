<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive, read-performance indexes only - no unique constraints, no
 * foreign keys, no column changes. Speeds up the city/state joins already
 * performed by property search without changing any query results.
 */
class AddPerformanceIndexesToLocationTables extends Migration
{
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->index('state_id', 'cities_state_id_index');
            $table->index('country_id', 'cities_country_id_index');
        });

        Schema::table('states', function (Blueprint $table) {
            $table->index('country_id', 'states_country_id_index');
        });
    }

    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropIndex('cities_state_id_index');
            $table->dropIndex('cities_country_id_index');
        });

        Schema::table('states', function (Blueprint $table) {
            $table->dropIndex('states_country_id_index');
        });
    }
}
