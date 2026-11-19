<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateExamChaptersAndTopicsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('exam_chapters')) {
            Schema::create('exam_chapters', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('session_id')->nullable();
                $table->unsignedBigInteger('class_type_id');
                $table->unsignedBigInteger('subject_id');
                $table->string('name', 255);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['class_type_id', 'subject_id']);
                $table->index('name');
            });
        }

        if (!Schema::hasTable('exam_topics')) {
            Schema::create('exam_topics', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('session_id')->nullable();
                $table->unsignedBigInteger('chapter_id')->nullable();
                $table->unsignedBigInteger('class_type_id');
                $table->unsignedBigInteger('subject_id');
                $table->string('name', 255);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['class_type_id', 'subject_id']);
                $table->index('chapter_id');
                $table->index('name');
            });
        }

        // Populate existing chapters and topics from questions table
        try {
            $existing = DB::table('questions')
                ->whereNotNull('chapter_name')
                ->where('chapter_name', '!=', '')
                ->whereNotNull('class_type_id')
                ->whereNotNull('subject_id')
                ->select('class_type_id', 'subject_id', 'chapter_name', 'topic_name', 'branch_id', 'session_id', 'user_id')
                ->distinct()
                ->get();

            foreach ($existing as $row) {
                $chapName = trim($row->chapter_name);
                if (empty($chapName)) continue;

                $chap = DB::table('exam_chapters')->where([
                    'class_type_id' => $row->class_type_id,
                    'subject_id' => $row->subject_id,
                    'name' => $chapName,
                ])->first();

                if (!$chap) {
                    $chapId = DB::table('exam_chapters')->insertGetId([
                        'user_id' => $row->user_id,
                        'branch_id' => $row->branch_id,
                        'session_id' => $row->session_id,
                        'class_type_id' => $row->class_type_id,
                        'subject_id' => $row->subject_id,
                        'name' => $chapName,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $chapId = $chap->id;
                }

                $topName = trim($row->topic_name ?? '');
                if (!empty($topName)) {
                    $topExists = DB::table('exam_topics')->where([
                        'class_type_id' => $row->class_type_id,
                        'subject_id' => $row->subject_id,
                        'chapter_id' => $chapId,
                        'name' => $topName,
                    ])->exists();

                    if (!$topExists) {
                        DB::table('exam_topics')->insert([
                            'user_id' => $row->user_id,
                            'branch_id' => $row->branch_id,
                            'session_id' => $row->session_id,
                            'chapter_id' => $chapId,
                            'class_type_id' => $row->class_type_id,
                            'subject_id' => $row->subject_id,
                            'name' => $topName,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore seeding errors during initial migration
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exam_topics');
        Schema::dropIfExists('exam_chapters');
    }
}
