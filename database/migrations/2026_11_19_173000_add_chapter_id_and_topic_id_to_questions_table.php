<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddChapterIdAndTopicIdToQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'chapter_id')) {
                $table->unsignedBigInteger('chapter_id')->nullable()->after('subject_id');
                $table->index('chapter_id');
            }
            if (!Schema::hasColumn('questions', 'topic_id')) {
                $table->unsignedBigInteger('topic_id')->nullable()->after('chapter_id');
                $table->index('topic_id');
            }
        });

        // Backfill chapter_id and topic_id for existing questions
        try {
            $questions = DB::table('questions')
                ->whereNotNull('chapter_name')
                ->where('chapter_name', '!=', '')
                ->whereNull('chapter_id')
                ->select('id', 'class_type_id', 'subject_id', 'chapter_name', 'topic_name')
                ->get();

            foreach ($questions as $q) {
                $chapId = DB::table('exam_chapters')->where([
                    'class_type_id' => $q->class_type_id,
                    'subject_id' => $q->subject_id,
                    'name' => trim($q->chapter_name),
                ])->value('id');

                $topId = null;
                if (!empty($q->topic_name)) {
                    $topId = DB::table('exam_topics')->where([
                        'class_type_id' => $q->class_type_id,
                        'subject_id' => $q->subject_id,
                        'name' => trim($q->topic_name),
                    ])->value('id');
                }

                if ($chapId || $topId) {
                    DB::table('questions')->where('id', $q->id)->update([
                        'chapter_id' => $chapId,
                        'topic_id' => $topId,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Ignore backfill error on empty tables
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'topic_id')) {
                $table->dropIndex(['topic_id']);
                $table->dropColumn('topic_id');
            }
            if (Schema::hasColumn('questions', 'chapter_id')) {
                $table->dropIndex(['chapter_id']);
                $table->dropColumn('chapter_id');
            }
        });
    }
}
