<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLatitudeToReProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('re_projects', function (Blueprint $table) {
            $table->decimal('latitude',10,8);
            $table->decimal('longitude',10,8);
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
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
}
