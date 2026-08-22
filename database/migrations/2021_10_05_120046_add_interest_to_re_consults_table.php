<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInterestToReConsultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('re_consults','interest')) {
            Schema::table('re_consults', function (Blueprint $table) {
                $table->text('interest');
            });
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasColumn('re_consults','interest')) {
            Schema::table('re_consults', function (Blueprint $table) {
                $table->dropColumn('interest');
            });
        }
    }
}
