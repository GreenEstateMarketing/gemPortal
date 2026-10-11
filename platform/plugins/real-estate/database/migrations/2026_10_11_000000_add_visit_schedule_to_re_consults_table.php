<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddVisitScheduleToReConsultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('re_consults', function (Blueprint $table) {
            $table->date('preferred_visit_date')->nullable();
            $table->string('preferred_visit_time', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('re_consults', function (Blueprint $table) {
            $table->dropColumn(['preferred_visit_date', 'preferred_visit_time']);
        });
    }
}
