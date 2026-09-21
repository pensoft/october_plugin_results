<?php namespace Pensoft\Results\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Nodes are staggered vertically in the approved design so neighbouring titles
 * do not crowd each other, so the distance from the axis has to be per result
 * rather than a single value in the stylesheet.
 */
class AddResultOffsetY extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('pensoft_results_results', 'offset_y')) {
            Schema::table('pensoft_results_results', function (Blueprint $table) {
                $table->integer('offset_y')->default(86);
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('pensoft_results_results', 'offset_y')) {
            Schema::table('pensoft_results_results', function (Blueprint $table) {
                $table->dropColumn('offset_y');
            });
        }
    }
}
