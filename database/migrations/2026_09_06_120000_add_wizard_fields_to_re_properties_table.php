<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddWizardFieldsToRePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('re_properties', function (Blueprint $table) {
            if (! Schema::hasColumn('re_properties', 'wizard_step')) {
                $table->unsignedTinyInteger('wizard_step')->default(0)->after('is_deleted');
            }

            if (! Schema::hasColumn('re_properties', 'submission_status')) {
                $table->string('submission_status', 20)->default('draft')->after('wizard_step');
            }

            if (! Schema::hasColumn('re_properties', 'wizard_role')) {
                $table->string('wizard_role', 20)->nullable()->after('submission_status');
            }

            if (! Schema::hasColumn('re_properties', 'last_wizard_activity_at')) {
                $table->timestamp('last_wizard_activity_at')->nullable()->after('wizard_role');
            }
        });

        Schema::table('re_properties', function (Blueprint $table) {
            $table->integer('city_area_id')->nullable()->change();
            $table->integer('category_id')->unsigned()->nullable()->change();
            $table->decimal('latitude', 10, 8)->nullable()->change();
            $table->decimal('longitude', 10, 8)->nullable()->change();
            $table->text('documents')->nullable()->change();
        });

        // Every pre-existing row was created through the old, non-wizard forms and is a complete record.
        DB::table('re_properties')->update([
            'submission_status' => 'submitted',
            'wizard_step' => 5,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('re_properties', function (Blueprint $table) {
            $table->dropColumn(['wizard_step', 'submission_status', 'wizard_role', 'last_wizard_activity_at']);
        });
    }
}
