<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRePropertiesTableAddBuildInColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('re_properties', 'built_in')) {
            Schema::table('re_properties', function (Blueprint $table) {
                $table->string('built_in')->default(null);
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
        if(Schema::hasColumn('re_properties', 'built_in')) {
            Schema::table('re_properties', function (Blueprint $table) {
                $table->dropColumn('built_in');
            });
        }
    }
}
