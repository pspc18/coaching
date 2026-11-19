<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('exams')) {
            Schema::table('exams', function (Blueprint $table) {
                if (!Schema::hasColumn('exams', 'course_id')) {
                    $table->unsignedBigInteger('course_id')->nullable()->after('code');
                }
                if (!Schema::hasColumn('exams', 'batch_id')) {
                    $table->unsignedBigInteger('batch_id')->nullable()->after('course_id');
                }
            });
            DB::statement('UPDATE exams SET course_id = class_type_id WHERE course_id IS NULL AND class_type_id IS NOT NULL');
        }

        if (Schema::hasTable('fill_marks')) {
            Schema::table('fill_marks', function (Blueprint $table) {
                if (!Schema::hasColumn('fill_marks', 'course_id')) {
                    $table->unsignedBigInteger('course_id')->nullable()->after('admission_id');
                }
                if (!Schema::hasColumn('fill_marks', 'batch_id')) {
                    $table->unsignedBigInteger('batch_id')->nullable()->after('course_id');
                }
            });
            DB::statement('UPDATE fill_marks SET course_id = class_type_id WHERE course_id IS NULL AND class_type_id IS NOT NULL');
        }

        if (Schema::hasTable('examination_schedules')) {
            Schema::table('examination_schedules', function (Blueprint $table) {
                if (!Schema::hasColumn('examination_schedules', 'course_id')) {
                    $table->unsignedBigInteger('course_id')->nullable()->after('subject_id');
                }
                if (!Schema::hasColumn('examination_schedules', 'batch_id')) {
                    $table->unsignedBigInteger('batch_id')->nullable()->after('course_id');
                }
            });
            DB::statement('UPDATE examination_schedules SET course_id = class_type_id WHERE course_id IS NULL AND class_type_id IS NOT NULL');
        }

        if (Schema::hasTable('exam_result_publications')) {
            Schema::table('exam_result_publications', function (Blueprint $table) {
                if (!Schema::hasColumn('exam_result_publications', 'course_id')) {
                    $table->unsignedBigInteger('course_id')->nullable()->after('exam_id');
                }
                if (!Schema::hasColumn('exam_result_publications', 'batch_id')) {
                    $table->unsignedBigInteger('batch_id')->nullable()->after('course_id');
                }
            });
            DB::statement('UPDATE exam_result_publications SET course_id = class_type_id WHERE course_id IS NULL AND class_type_id IS NOT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('exams')) {
            Schema::table('exams', function (Blueprint $table) {
                if (Schema::hasColumn('exams', 'batch_id')) $table->dropColumn('batch_id');
                if (Schema::hasColumn('exams', 'course_id')) $table->dropColumn('course_id');
            });
        }
        if (Schema::hasTable('fill_marks')) {
            Schema::table('fill_marks', function (Blueprint $table) {
                if (Schema::hasColumn('fill_marks', 'batch_id')) $table->dropColumn('batch_id');
                if (Schema::hasColumn('fill_marks', 'course_id')) $table->dropColumn('course_id');
            });
        }
        if (Schema::hasTable('examination_schedules')) {
            Schema::table('examination_schedules', function (Blueprint $table) {
                if (Schema::hasColumn('examination_schedules', 'batch_id')) $table->dropColumn('batch_id');
                if (Schema::hasColumn('examination_schedules', 'course_id')) $table->dropColumn('course_id');
            });
        }
        if (Schema::hasTable('exam_result_publications')) {
            Schema::table('exam_result_publications', function (Blueprint $table) {
                if (Schema::hasColumn('exam_result_publications', 'batch_id')) $table->dropColumn('batch_id');
                if (Schema::hasColumn('exam_result_publications', 'course_id')) $table->dropColumn('course_id');
            });
        }
    }
};
