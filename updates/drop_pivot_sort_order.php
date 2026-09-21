<?php namespace Pensoft\Results\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * The pivot table was created with a sort_order column that is never written to.
 * Both pensoft_results_categories and pensoft_results_results also have one, so
 * the unqualified order clause on the relation was ambiguous on PostgreSQL:
 *
 *   SQLSTATE[42702]: Ambiguous column: column reference "sort_order" is ambiguous
 *
 * The relations now order by a qualified column; this drops the unused column so
 * the ambiguity cannot come back.
 */
class DropPivotSortOrder extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('pensoft_results_result_category', 'sort_order')) {
            Schema::table('pensoft_results_result_category', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }

    public function down()
    {
        if (!Schema::hasColumn('pensoft_results_result_category', 'sort_order')) {
            Schema::table('pensoft_results_result_category', function (Blueprint $table) {
                $table->integer('sort_order')->nullable();
            });
        }
    }
}
