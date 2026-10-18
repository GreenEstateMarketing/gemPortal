<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Additive, read-performance indexes only - no unique constraints, no
 * foreign keys, no column changes.
 */
class AddPerformanceIndexesToPaymentTables extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->index('user_id', 'payments_user_id_index');
            $table->index('customer_id', 'payments_customer_id_index');
        });

        // order_id has drifted to a `text` column (see
        // 2025_03_04_091739_alter_table_payments_change_charge_id_type-style
        // migrations) - MySQL can't index a BLOB/TEXT column without an
        // explicit prefix length, and changing its type is out of scope for
        // this pass. A prefix index is still additive/non-breaking and lets
        // lookups by order_id use an index instead of a full table scan.
        if (!$this->indexExists('payments', 'payments_order_id_index')) {
            DB::statement('ALTER TABLE `payments` ADD INDEX `payments_order_id_index` (`order_id`(191))');
        }
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_user_id_index');
            $table->dropIndex('payments_customer_id_index');
        });

        if ($this->indexExists('payments', 'payments_order_id_index')) {
            DB::statement('ALTER TABLE `payments` DROP INDEX `payments_order_id_index`');
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $result = DB::select('SHOW INDEX FROM `' . $table . '` WHERE Key_name = ?', [$indexName]);

        return count($result) > 0;
    }
}
