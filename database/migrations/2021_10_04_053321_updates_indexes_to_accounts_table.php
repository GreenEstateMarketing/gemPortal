<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatesIndexesToAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('re_accounts')) {
            Schema::table('re_accounts', function (Blueprint $table) {
                $table->primary('id');
                $table->unique(['email', 'username']);
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
        Schema::table('re_accounts', function (Blueprint $table) {
            //
        });
    }
}
