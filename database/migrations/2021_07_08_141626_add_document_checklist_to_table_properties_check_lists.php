<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocumentChecklistToTablePropertiesCheckLists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('table_properties_check_lists', function (Blueprint $table) {
            $table->text('document_checklist');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('table_properties_check_lists', function (Blueprint $table) {
            $table->dropColumn(['document_checklist']);
        });
    }
}
