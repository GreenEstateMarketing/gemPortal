<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRePropertyContractsTable extends Migration
{
    public function up()
    {
        Schema::create('re_property_contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('property_id');
            $table->string('copy_type', 20); // 'member' | 'agent'
            $table->string('file_path');
            $table->text('masked_key');
            $table->timestamps();

            $table->unique(['property_id', 'copy_type']);
            $table->foreign('property_id')->references('id')->on('re_properties')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('re_property_contracts');
    }
}
