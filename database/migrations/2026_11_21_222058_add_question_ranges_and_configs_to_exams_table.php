<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuestionRangesAndConfigsToExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'subject_question_ranges')) {
                $table->json('subject_question_ranges')->nullable()->after('subject_ids');
            }
            if (!Schema::hasColumn('exams', 'question_configs')) {
                $table->json('question_configs')->nullable()->after('answer_key');
            }
        });

        Schema::table('omr_results', function (Blueprint $table) {
            if (!Schema::hasColumn('omr_results', 'subject_breakdown')) {
                $table->json('subject_breakdown')->nullable()->after('extracted_answers');
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
            if (Schema::hasColumn('exams', 'subject_question_ranges')) {
                $table->dropColumn('subject_question_ranges');
            }
            if (Schema::hasColumn('exams', 'question_configs')) {
                $table->dropColumn('question_configs');
            }
        });

        Schema::table('omr_results', function (Blueprint $table) {
            if (Schema::hasColumn('omr_results', 'subject_breakdown')) {
                $table->dropColumn('subject_breakdown');
            }
        });
    }
}
