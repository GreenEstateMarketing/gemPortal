<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDiscountFieldsToRePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('re_packages', function (Blueprint $table) {
            $table->double('original_price', 15, 2)->unsigned()->nullable()->after('price');
            $table->boolean('is_recommended')->default(false)->after('percent_save');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('re_packages', function (Blueprint $table) {
            $table->dropColumn(['original_price', 'is_recommended']);
        });
    }
}
