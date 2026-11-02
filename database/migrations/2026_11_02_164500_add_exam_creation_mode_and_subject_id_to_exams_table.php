<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExamCreationModeAndSubjectIdToExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'exam_creation_mode')) {
                $table->string('exam_creation_mode', 50)->nullable()->default('normal')->after('is_online');
            }
            if (!Schema::hasColumn('exams', 'duration')) {
                $table->integer('duration')->nullable()->default(60)->after('exam_maximum_marks');
            }
            if (!Schema::hasColumn('exams', 'subject_id')) {
                $table->unsignedBigInteger('subject_id')->nullable()->after('class_type_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'exam_creation_mode')) {
                $table->dropColumn('exam_creation_mode');
            }
            if (Schema::hasColumn('exams', 'duration')) {
                $table->dropColumn('duration');
            }
            if (Schema::hasColumn('exams', 'subject_id')) {
                $table->dropColumn('subject_id');
            }
        });
    }
}
