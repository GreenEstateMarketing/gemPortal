<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAccountsAddYearsOfExperienceColumn extends Migration
{
    public function up()
    {
        Schema::table('re_accounts', function (Blueprint $table) {
            $table->unsignedTinyInteger('years_of_experience')->nullable();
        });
    }

    public function down()
    {
        Schema::table('re_accounts', function (Blueprint $table) {
            $table->dropColumn('years_of_experience');
        });
    }
}
