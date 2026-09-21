<?php namespace Pensoft\Results\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateResultsTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('pensoft_results_categories')) {
            Schema::create('pensoft_results_categories', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->string('name')->nullable();
                $table->string('slug')->nullable()->index();
                $table->boolean('is_visible')->default(true);
                $table->integer('sort_order')->nullable();
            });
        }

        if (!Schema::hasTable('pensoft_results_phases')) {
            Schema::create('pensoft_results_phases', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->string('name')->nullable();
                $table->string('slug')->nullable()->index();
                $table->decimal('position_x', 5, 2)->default(50);
                $table->integer('sort_order')->nullable();
            });
        }

        if (!Schema::hasTable('pensoft_results_results')) {
            Schema::create('pensoft_results_results', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->string('title')->nullable();
                $table->string('slug')->nullable()->index();
                $table->string('month_label')->nullable();
                $table->text('description')->nullable();
                $table->text('relevant_for')->nullable();
                $table->text('how_used')->nullable();
                $table->text('materials')->nullable();
                $table->string('icon_class')->nullable();
                $table->string('position', 10)->default('above');
                $table->decimal('position_x', 5, 2)->default(50);
                $table->integer('phase_id')->unsigned()->nullable()->index();
                $table->integer('sort_order')->nullable();
                $table->boolean('is_published')->default(true);
            });
        }

        if (!Schema::hasTable('pensoft_results_result_category')) {
            // No sort_order here on purpose: both sides of the relation carry one
            // and an unqualified order clause would be ambiguous.
            Schema::create('pensoft_results_result_category', function (Blueprint $table) {
                $table->integer('result_id')->unsigned();
                $table->integer('category_id')->unsigned();
                $table->primary(['result_id', 'category_id'], 'result_category');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('pensoft_results_result_category');
        Schema::dropIfExists('pensoft_results_results');
        Schema::dropIfExists('pensoft_results_phases');
        Schema::dropIfExists('pensoft_results_categories');
    }
}
