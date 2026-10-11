<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddIsVerifiedToReProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('re_projects', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('re_projects', function (Blueprint $table) {
            $table->dropColumn('is_verified');
        });
    }
}
