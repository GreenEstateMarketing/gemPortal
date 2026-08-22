<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableTransactionsAddUserTypeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('transactions', 'user_type')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('user_type')->default(null);
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
        if(Schema::hasColumn('transactions', 'user_type')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn('user_type');
            });
        }
    }
}
