<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsVerifyToTablePropertiesCheckLists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('table_properties_check_lists', function (Blueprint $table) {
            $table->boolean('is_verify');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('table_properties_check_lists', function (Blueprint $table) {
            $table->dropColumn(['is_verify']);
        });
    }
}
