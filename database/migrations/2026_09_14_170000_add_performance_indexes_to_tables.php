<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddPerformanceIndexesToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Safe helper to add index if not exists
        $addIndexSafely = function ($tableName, $columns, $indexName) {
            if (!Schema::hasTable($tableName)) {
                return;
            }

            // Check if all columns exist
            foreach ($columns as $column) {
                if (!Schema::hasColumn($tableName, $column)) {
                    return;
                }
            }

            try {
                $indexes = DB::select("SHOW INDEX FROM `{$tableName}` WHERE Key_name = '{$indexName}'");
                if (empty($indexes)) {
                    Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName) {
                        $table->index($columns, $indexName);
                    });
                }
            } catch (\Throwable $e) {
                // Ignore if index already exists or database doesn't support SHOW INDEX
            }
        };

        // 1. Admissions table indexes
        $addIndexSafely('admissions', ['branch_id', 'session_id', 'status', 'school'], 'idx_admissions_branch_session_status');
        $addIndexSafely('admissions', ['class_type_id'], 'idx_admissions_class_type');
        $addIndexSafely('admissions', ['attendance_unique_id'], 'idx_admissions_attendance_uid');
        $addIndexSafely('admissions', ['dob'], 'idx_admissions_dob');

        // 2. Attendance marks table indexes
        $addIndexSafely('attendance_marks', ['date', 'branch_id', 'session_id', 'entity_type'], 'idx_att_marks_date_branch_session');
        $addIndexSafely('attendance_marks', ['unique_id'], 'idx_att_marks_unique_id');

        // 3. Teacher attendance table indexes
        $addIndexSafely('teacher_attendance', ['date', 'branch_id', 'session_id'], 'idx_teacher_att_date_branch_session');
        $addIndexSafely('teacher_attendance', ['staff_id'], 'idx_teacher_att_staff_id');

        // 4. Student attendance table indexes
        $addIndexSafely('student_attendance', ['date', 'branch_id', 'session_id'], 'idx_student_att_date_branch_session');
        $addIndexSafely('student_attendance', ['admission_id'], 'idx_student_att_admission_id');

        // 5. Fees details table indexes
        $addIndexSafely('fees_detail', ['session_id', 'branch_id', 'status', 'date'], 'idx_fees_detail_session_branch_status');
        $addIndexSafely('fees_detail', ['admission_id'], 'idx_fees_detail_admission_id');

        // 6. Expenses table indexes
        $addIndexSafely('expenses', ['session_id', 'branch_id', 'date'], 'idx_expenses_session_branch_date');

        // 7. Managed notices table indexes
        $addIndexSafely('managed_notices', ['branch_id', 'session_id', 'status'], 'idx_notices_branch_session_status');

        // 8. Support complaints table indexes
        $addIndexSafely('support_complaints', ['branch_id', 'session_id', 'status'], 'idx_complaints_branch_session_status');

        // 9. Settings table index
        $addIndexSafely('settings', ['branch_id'], 'idx_settings_branch_id');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $dropIndexSafely = function ($tableName, $indexName) {
            if (!Schema::hasTable($tableName)) {
                return;
            }
            try {
                $indexes = DB::select("SHOW INDEX FROM `{$tableName}` WHERE Key_name = '{$indexName}'");
                if (!empty($indexes)) {
                    Schema::table($tableName, function (Blueprint $table) use ($indexName) {
                        $table->dropIndex($indexName);
                    });
                }
            } catch (\Throwable $e) {
                // Ignore
            }
        };

        $dropIndexSafely('admissions', 'idx_admissions_branch_session_status');
        $dropIndexSafely('admissions', 'idx_admissions_class_type');
        $dropIndexSafely('admissions', 'idx_admissions_attendance_uid');
        $dropIndexSafely('admissions', 'idx_admissions_dob');
        $dropIndexSafely('attendance_marks', 'idx_att_marks_date_branch_session');
        $dropIndexSafely('attendance_marks', 'idx_att_marks_unique_id');
        $dropIndexSafely('teacher_attendance', 'idx_teacher_att_date_branch_session');
        $dropIndexSafely('teacher_attendance', 'idx_teacher_att_staff_id');
        $dropIndexSafely('student_attendance', 'idx_student_att_date_branch_session');
        $dropIndexSafely('student_attendance', 'idx_student_att_admission_id');
        $dropIndexSafely('fees_detail', 'idx_fees_detail_session_branch_status');
        $dropIndexSafely('fees_detail', 'idx_fees_detail_admission_id');
        $dropIndexSafely('expenses', 'idx_expenses_session_branch_date');
        $dropIndexSafely('managed_notices', 'idx_notices_branch_session_status');
        $dropIndexSafely('support_complaints', 'idx_complaints_branch_session_status');
        $dropIndexSafely('settings', 'idx_settings_branch_id');
    }
}
