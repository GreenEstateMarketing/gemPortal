<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableMembersAddSignatureCreatedAtColumn extends Migration
{
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->timestamp('signature_created_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('signature_created_at');
        });
    }
}
