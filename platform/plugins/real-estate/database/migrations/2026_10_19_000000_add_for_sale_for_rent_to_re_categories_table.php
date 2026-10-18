<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddForSaleForRentToReCategoriesTable extends Migration
{
    public function up()
    {
        Schema::table('re_categories', function (Blueprint $table) {
            $table->boolean('for_sale')->default(true)->after('parent_id');
            $table->boolean('for_rent')->default(true)->after('for_sale');
        });

        // Explicit backfill (belt-and-suspenders alongside the column
        // defaults above) so every category that existed before this
        // feature keeps showing up for both Sale and Rent searches, exactly
        // as it did when the two had no tie to each other at all.
        DB::table('re_categories')->update(['for_sale' => true, 'for_rent' => true]);
    }

    public function down()
    {
        Schema::table('re_categories', function (Blueprint $table) {
            $table->dropColumn(['for_sale', 'for_rent']);
        });
    }
}
