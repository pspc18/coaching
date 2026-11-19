<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCoachingMetadataAndBilingualToQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'chapter_name')) {
                $table->string('chapter_name', 255)->nullable()->after('subject_id');
            }
            if (!Schema::hasColumn('questions', 'topic_name')) {
                $table->string('topic_name', 255)->nullable()->after('chapter_name');
            }
            if (!Schema::hasColumn('questions', 'language')) {
                $table->string('language', 20)->default('en')->after('topic_name'); // 'en', 'hi', 'bilingual'
            }
            if (!Schema::hasColumn('questions', 'name_hi')) {
                $table->longText('name_hi')->nullable()->after('name');
            }
            if (!Schema::hasColumn('questions', 'ans1_hi')) {
                $table->longText('ans1_hi')->nullable()->after('ans1');
            }
            if (!Schema::hasColumn('questions', 'ans2_hi')) {
                $table->longText('ans2_hi')->nullable()->after('ans2');
            }
            if (!Schema::hasColumn('questions', 'ans3_hi')) {
                $table->longText('ans3_hi')->nullable()->after('ans3');
            }
            if (!Schema::hasColumn('questions', 'ans4_hi')) {
                $table->longText('ans4_hi')->nullable()->after('ans4');
            }
            if (!Schema::hasColumn('questions', 'ans5_hi')) {
                $table->longText('ans5_hi')->nullable()->after('ans5');
            }
            if (!Schema::hasColumn('questions', 'direction_hi')) {
                $table->longText('direction_hi')->nullable()->after('direction');
            }
            if (!Schema::hasColumn('questions', 'explanation_hi')) {
                $table->longText('explanation_hi')->nullable()->after('explanation');
            }
            if (!Schema::hasColumn('questions', 'is_pyq')) {
                $table->boolean('is_pyq')->default(false)->after('difficulty_level');
            }
            if (!Schema::hasColumn('questions', 'pyq_exam')) {
                $table->string('pyq_exam', 100)->nullable()->after('is_pyq');
            }
            if (!Schema::hasColumn('questions', 'pyq_year')) {
                $table->string('pyq_year', 50)->nullable()->after('pyq_exam');
            }
            if (!Schema::hasColumn('questions', 'marks')) {
                $table->decimal('marks', 8, 2)->default(1.00)->after('pyq_year');
            }
            if (!Schema::hasColumn('questions', 'negative_marks')) {
                $table->decimal('negative_marks', 8, 2)->default(0.00)->after('marks');
            }
            if (!Schema::hasColumn('questions', 'ideal_time')) {
                $table->integer('ideal_time')->default(60)->after('negative_marks');
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
        Schema::table('questions', function (Blueprint $table) {
            $columns = [
                'chapter_name', 'topic_name', 'language',
                'name_hi', 'ans1_hi', 'ans2_hi', 'ans3_hi', 'ans4_hi', 'ans5_hi',
                'direction_hi', 'explanation_hi',
                'is_pyq', 'pyq_exam', 'pyq_year',
                'marks', 'negative_marks', 'ideal_time'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('questions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
