<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReSpokenLanguagesTable extends Migration
{
    public function up()
    {
        Schema::create('re_spoken_languages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->integer('order')->default(0);
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('re_spoken_languages');
    }
}
