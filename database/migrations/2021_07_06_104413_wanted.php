<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Wanted extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wanted', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->integer('category_id');
            $table->integer('city_id');
            $table->string('mobile_no');
            $table->text('name');
            $table->text('area');
            $table->text('email');
            $table->text('comments');
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
        Schema::dropIfExists('wanted');
    }
}
