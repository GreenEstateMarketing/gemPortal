<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReAccountCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('re_account_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('category_id');

            $table->foreign('account_id')->references('id')->on('re_accounts')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('re_categories')->onDelete('cascade');
            $table->unique(['account_id', 'category_id'], 're_account_categories_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('re_account_categories');
    }
}
