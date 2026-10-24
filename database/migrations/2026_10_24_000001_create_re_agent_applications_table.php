<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReAgentApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('re_agent_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 60);
            $table->string('phone', 20);
            $table->string('education_level', 60);
            $table->unsignedSmallInteger('years_of_experience')->default(0);
            $table->string('resume_path');
            $table->string('resume_original_name');
            $table->string('status', 60)->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('re_agent_applications');
    }
}
