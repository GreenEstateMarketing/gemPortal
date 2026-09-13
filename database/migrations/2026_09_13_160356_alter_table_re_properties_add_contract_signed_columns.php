<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRePropertiesAddContractSignedColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('re_properties', function (Blueprint $table) {
            $table->boolean('contract_signed_by_member')->default(false);
            $table->boolean('contract_signed_by_agent')->default(false);
            $table->boolean('contract_signed_by_admin')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('re_properties', function (Blueprint $table) {
            $table->dropColumn(['contract_signed_by_member', 'contract_signed_by_agent', 'contract_signed_by_admin']);
        });
    }
}
