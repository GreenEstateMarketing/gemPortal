<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * country_id, state_id and price_unit have existed on the live re_properties
 * table (and been read/written by the app) for a long time, but no migration
 * in the repo ever created them - a fresh install/migrate:fresh would be
 * missing these columns. This migration formally captures them; the
 * hasColumn guards make it a no-op on databases (like this one) where the
 * columns already exist.
 */
class FormalizeDriftedColumnsOnRePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('re_properties', function (Blueprint $table) {
            if (! Schema::hasColumn('re_properties', 'country_id')) {
                $table->integer('country_id')->unsigned()->nullable()->after('currency_id');
            }

            if (! Schema::hasColumn('re_properties', 'state_id')) {
                $table->integer('state_id')->unsigned()->nullable()->after('country_id');
            }

            if (! Schema::hasColumn('re_properties', 'price_unit')) {
                $table->string('price_unit', 120)->nullable()->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Intentionally left blank: these columns predate this migration and
        // are relied on elsewhere; do not drop them on rollback.
    }
}
