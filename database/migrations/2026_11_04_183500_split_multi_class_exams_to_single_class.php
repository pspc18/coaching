<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\exam\Exam;
use App\Models\exam\AssignExam;
use App\Models\exam\AssignQuestion;

class SplitMultiClassExamsToSingleClass extends Migration
{
    /**
     * Run the migrations.
     * Enforces strict 1 Exam = 1 Class architecture.
     * Splits historical exams that were assigned to multiple classes into
     * independent exam records with their own scanner codes, questions, and marks.
     */
    public function up()
    {
        DB::beginTransaction();

        try {
            $exams = Exam::all();

            foreach ($exams as $exam) {
                $assigned = AssignExam::where('exam_id', $exam->id)
                    ->whereNull('deleted_at')
                    ->orderBy('id', 'asc')
                    ->get()
                    ->unique('class_type_id')
                    ->values();

                if ($assigned->isEmpty()) {
                    if (!empty($exam->class_type_id)) {
                        $ae = new AssignExam();
                        $ae->user_id = $exam->user_id ?: 1;
                        $ae->session_id = $exam->session_id;
                        $ae->branch_id = $exam->branch_id;
                        $ae->exam_id = $exam->id;
                        $ae->class_type_id = $exam->class_type_id;
                        $ae->exam_date = $exam->exam_date;
                        $ae->save();
                    }
                    continue;
                }

                // 1. Primary class gets the original exam
                $firstAssign = $assigned[0];
                if ($exam->class_type_id != $firstAssign->class_type_id) {
                    $exam->class_type_id = $firstAssign->class_type_id;
                    $exam->save();
                }

                // 2. Additional classes get cloned into independent exams
                for ($i = 1; $i < $assigned->count(); $i++) {
                    $extraAssign = $assigned[$i];
                    $extraClassId = $extraAssign->class_type_id;

                    // Generate fresh unique code
                    do {
                        $newCode = 'EXAM-' . strtoupper(Str::random(6));
                    } while (Exam::where('code', $newCode)->orWhere('exam_code', $newCode)->exists());

                    $clone = $exam->replicate();
                    $clone->code = $newCode;
                    $clone->exam_code = $newCode;
                    $clone->class_type_id = $extraClassId;
                    $clone->save();

                    // Clone questions for the new exam
                    $questions = AssignQuestion::where('exam_id', $exam->id)->get();
                    foreach ($questions as $q) {
                        $qClone = $q->replicate();
                        $qClone->exam_id = $clone->id;
                        $qClone->class_type_id = $extraClassId;
                        $qClone->save();
                    }

                    // Move AssignExam to point to the cloned exam
                    DB::table('assign_exams')
                        ->where('id', $extraAssign->id)
                        ->update(['exam_id' => $clone->id]);

                    // Move marks
                    if (Schema::hasTable('fill_marks')) {
                        DB::table('fill_marks')
                            ->where('exam_id', $exam->id)
                            ->where('class_type_id', $extraClassId)
                            ->update(['exam_id' => $clone->id]);
                    }

                    if (Schema::hasTable('fill_min_max_marks')) {
                        DB::table('fill_min_max_marks')
                            ->where('exam_id', $exam->id)
                            ->where('class_type_id', $extraClassId)
                            ->update(['exam_id' => $clone->id]);
                    }

                    if (Schema::hasTable('fill_marks_by_excel')) {
                        DB::table('fill_marks_by_excel')
                            ->where('exam_id', $exam->id)
                            ->where('class_type_id', $extraClassId)
                            ->update(['exam_id' => $clone->id]);
                    }

                    if (Schema::hasTable('exam_result_publications')) {
                        DB::table('exam_result_publications')
                            ->where('exam_id', $exam->id)
                            ->where('class_type_id', $extraClassId)
                            ->update(['exam_id' => $clone->id]);
                    }

                    if (Schema::hasTable('examination_schedules')) {
                        DB::table('examination_schedules')
                            ->where('exam_id', $exam->id)
                            ->where('class_type_id', $extraClassId)
                            ->update(['exam_id' => $clone->id]);
                    }

                    if (Schema::hasTable('examination_admit_cards')) {
                        DB::table('examination_admit_cards')
                            ->where('exam_id', $exam->id)
                            ->where('class_type_id', $extraClassId)
                            ->update(['exam_id' => $clone->id]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Reversible not applicable as records are separated into independent exams.
    }
}
