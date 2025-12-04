<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('course_units', function (Blueprint $table) {
            if (!Schema::hasColumn('course_units', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('course_id');
                $table->foreign('parent_id')->references('id')->on('course_units')->onDelete('cascade');
                $table->index(['course_id', 'parent_id']);
            }
            if (!Schema::hasColumn('course_units', 'category')) {
                $table->string('category', 32)->nullable()->after('title');
                $table->index(['course_id', 'category']);
            }
            if (!Schema::hasColumn('course_units', 'position')) {
                $table->unsignedInteger('position')->default(0)->after('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('course_units', function (Blueprint $table) {
            if (Schema::hasColumn('course_units', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropIndex(['course_id', 'parent_id']);
            }
            if (Schema::hasColumn('course_units', 'category')) {
                $table->dropIndex(['course_id', 'category']);
            }
            $table->dropColumn(array_values(array_filter(['parent_id','category','position'], fn($c) => Schema::hasColumn('course_units', $c))));
        });
    }
};
