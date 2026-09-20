<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRePropertiesAddContractFinalizedAtColumn extends Migration
{
    public function up()
    {
        Schema::table('re_properties', function (Blueprint $table) {
            $table->timestamp('contract_finalized_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('re_properties', function (Blueprint $table) {
            $table->dropColumn('contract_finalized_at');
        });
    }
}
