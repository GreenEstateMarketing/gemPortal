<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReAccountSpokenLanguagesTable extends Migration
{
    public function up()
    {
        Schema::create('re_account_spoken_languages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('account_id');
            $table->unsignedBigInteger('spoken_language_id');

            $table->foreign('account_id')->references('id')->on('re_accounts')->onDelete('cascade');
            $table->foreign('spoken_language_id')->references('id')->on('re_spoken_languages')->onDelete('cascade');
            $table->unique(['account_id', 'spoken_language_id'], 're_account_spoken_languages_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('re_account_spoken_languages');
    }
}
