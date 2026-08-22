<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MemberActivityLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->text('user_agent');
            $table->string('reference_url');
            $table->string('reference_name');
            $table->string('ip_address');
            $table->integer('member_id');
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
        Schema::dropIfExists('member_activity_logs');
    }
}
