<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTableAllColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('audit_histories', function (Blueprint $table) {
            if ($this->indexExists('audit_histories', 'audit_histories_module_index')) {
                $table->dropIndex('audit_histories_module_index');
            }
        });

        Schema::table('audit_histories', function (Blueprint $table) {
            $table->longText('user_agent')->change();
            $table->longText('ip_address')->change();
            $table->longText('module')->change();
            $table->longText('action')->change();
            $table->longText('reference_user')->change();
            $table->longText('reference_id')->change();
            $table->longText('reference_name')->change();
            $table->longText('type')->change();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audit_histories', function (Blueprint $table) {
            $table->longText('user_agent')->change();
            $table->longText('ip_address')->change();
            $table->longText('module')->change();
            $table->longText('action')->change();
            $table->longText('reference_user')->change();
            $table->longText('reference_id')->change();
            $table->longText('reference_name')->change();
            $table->longText('type')->change();
        });
    }

    private function indexExists($table, $index)
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}`");

        foreach ($indexes as $idx) {
            if ($idx->Key_name === $index) {
                return true;
            }
        }

        return false;
    }
}
