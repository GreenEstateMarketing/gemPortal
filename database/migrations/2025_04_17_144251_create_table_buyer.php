<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBuyer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('buyer')) {
            Schema::create('buyer', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone');
                $table->string('email')->nullable();
                $table->unsignedBigInteger('property_id')->nullable();
                $table->unsignedBigInteger('seller_id')->nullable();
                $table->unsignedBigInteger('agent_id')->nullable();
                $table->decimal('amount', 15, 2)->nullable();
                $table->string('transaction_type',)->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('buyer');
    }
}
