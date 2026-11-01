<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReAgentApplicationCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('re_agent_application_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_application_id');
            $table->unsignedInteger('category_id');

            $table->foreign('agent_application_id')->references('id')->on('re_agent_applications')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('re_categories')->onDelete('cascade');
            $table->unique(['agent_application_id', 'category_id'], 're_agent_application_categories_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('re_agent_application_categories');
    }
}
