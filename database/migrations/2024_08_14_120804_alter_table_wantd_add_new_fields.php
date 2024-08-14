<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableWantdAddNewFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wanted', function (Blueprint $table) {
            $table->integer('amount')->nullable();
            $table->string('project_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wanted', function (Blueprint $table) {
            $table->dropColumn('amount');
            $table->dropColumn('project_name');
        });
    }
}
