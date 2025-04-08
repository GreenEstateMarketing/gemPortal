<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAccountsChangeColumnTypes extends Migration
{
    public function up()
    {
        Schema::table('re_accounts', function (Blueprint $table) {
            $table->string('city_id', 256)->change();
            $table->string('city_area_id', 256)->change();
        });
    }

    public function down()
    {
        Schema::table('re_accounts', function (Blueprint $table) {
            $table->integer('city_id')->change();
            $table->integer('city_area_id')->change();
        });
    }
}

