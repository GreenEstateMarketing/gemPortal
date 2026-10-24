<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAccountsAddCnicColumn extends Migration
{
    public function up()
    {
        Schema::table('re_accounts', function (Blueprint $table) {
            $table->string('cnic', 15)->nullable()->unique()->after('phone');
        });
    }

    public function down()
    {
        Schema::table('re_accounts', function (Blueprint $table) {
            $table->dropColumn('cnic');
        });
    }
}
