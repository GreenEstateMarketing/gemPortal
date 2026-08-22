<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePropertiesCheckLists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_properties_check_lists', function (Blueprint $table) {
            $table->id();
            $table->integer('property_id');
            $table->integer('completion_document')->default(0);
            $table->integer('allotment_document')->default(0);;
            $table->integer('possession_document')->default(0);;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_properties_check_lists');
    }
}
