<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTableAccountsAddSignatureColumns extends Migration
{
    public function up()
    {
        Schema::table('re_accounts', function (Blueprint $table) {
            $table->binary('signature')->nullable();
            $table->string('signature_source')->nullable();
        });

        // Blueprint::binary() only emits MySQL's plain BLOB (64KB cap) - too
        // small for a drawn signature PNG at higher device pixel ratios.
        // There's no Blueprint helper for LONGBLOB in this Laravel version.
        DB::statement('ALTER TABLE `re_accounts` MODIFY `signature` LONGBLOB NULL');
    }

    public function down()
    {
        Schema::table('re_accounts', function (Blueprint $table) {
            $table->dropColumn(['signature', 'signature_source']);
        });
    }
}
