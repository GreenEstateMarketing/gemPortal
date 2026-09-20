<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRePropertyStatusLogsTable extends Migration
{
    public function up()
    {
        Schema::create('re_property_status_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('property_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('old_status', 60)->nullable();
            $table->string('new_status', 60);
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('re_properties')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('re_property_status_logs');
    }
}
