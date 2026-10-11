<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddModulePerformanceIndexes extends Migration
{
    /**
     * Helper to safely add an index if it does not already exist.
     */
    protected function addIndexIfNotExists(string $table, array $columns, string $indexName): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $exists = DB::select("
            SELECT COUNT(1) as cnt 
            FROM information_schema.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
              AND TABLE_NAME = ? 
              AND INDEX_NAME = ?
        ", [$table, $indexName]);

        if (!empty($exists) && $exists[0]->cnt > 0) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $indexName) {
            $blueprint->index($columns, $indexName);
        });
    }

    /**
     * Helper to safely drop an index if it exists.
     */
    protected function dropIndexIfExists(string $table, string $indexName): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $exists = DB::select("
            SELECT COUNT(1) as cnt 
            FROM information_schema.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
              AND TABLE_NAME = ? 
              AND INDEX_NAME = ?
        ", [$table, $indexName]);

        if (empty($exists) || $exists[0]->cnt == 0) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($indexName) {
            $blueprint->dropIndex($indexName);
        });
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Convert any legacy TEXT columns that need indexing to VARCHAR safely
        try {
            DB::statement("ALTER TABLE fees_master MODIFY COLUMN fees_group_id VARCHAR(50) NULL, MODIFY COLUMN class_type_id VARCHAR(50) NULL");
        } catch (\Exception $e) {}

        try {
            DB::statement("ALTER TABLE admissions MODIFY COLUMN mobile VARCHAR(50) NULL");
        } catch (\Exception $e) {}

        try {
            DB::statement("ALTER TABLE fees_detail MODIFY COLUMN status VARCHAR(20) NULL");
        } catch (\Exception $e) {}

        // 1. Fees Management
        $this->addIndexIfNotExists('fees_details_invoices', ['branch_id', 'session_id', 'status'], 'idx_fdi_branch_session_status');
        $this->addIndexIfNotExists('fees_details_invoices', ['admission_id'], 'idx_fdi_admission_id');
        $this->addIndexIfNotExists('fees_details_invoices', ['payment_date'], 'idx_fdi_payment_date');
        $this->addIndexIfNotExists('fees_details_invoices', ['invoice_no'], 'idx_fdi_invoice_no');

        $this->addIndexIfNotExists('fees_assigns', ['admission_id'], 'idx_fees_assigns_admission_id');
        $this->addIndexIfNotExists('fees_assigns', ['branch_id', 'session_id'], 'idx_fees_assigns_branch_session');

        $this->addIndexIfNotExists('fees_assign_details', ['admission_id'], 'idx_fad_admission_id');
        $this->addIndexIfNotExists('fees_assign_details', ['fees_group_id'], 'idx_fad_fees_group_id');
        $this->addIndexIfNotExists('fees_assign_details', ['fees_master_id'], 'idx_fad_fees_master_id');

        $this->addIndexIfNotExists('fees_collect', ['admission_id'], 'idx_fees_collect_admission_id');
        $this->addIndexIfNotExists('fees_collect', ['branch_id', 'session_id'], 'idx_fees_collect_branch_session');

        $this->addIndexIfNotExists('fees_master', ['fees_group_id', 'class_type_id'], 'idx_fees_master_group_class');
        $this->addIndexIfNotExists('fees_master', ['branch_id', 'session_id'], 'idx_fees_master_branch_session');

        $this->addIndexIfNotExists('fees_group', ['branch_id', 'session_id'], 'idx_fees_group_branch_session');

        $this->addIndexIfNotExists('fees_detail', ['admission_id', 'session_id', 'status', 'deleted_at'], 'idx_fees_detail_perf');

        // 2. Student Panel
        $this->addIndexIfNotExists('admissions', ['admissionNo'], 'idx_admissions_admission_no');
        $this->addIndexIfNotExists('admissions', ['mobile'], 'idx_admissions_mobile');
        $this->addIndexIfNotExists('admissions', ['branch_id', 'session_id', 'class_type_id', 'status'], 'idx_admissions_branch_session_class');
        $this->addIndexIfNotExists('student_documents', ['admission_id'], 'idx_student_docs_admission_id');

        // 3. Attendance Management
        $this->addIndexIfNotExists('student_attendance', ['branch_id', 'session_id', 'class_type_id', 'date'], 'idx_student_att_class_date');

        // 4. Examination Panel
        $this->addIndexIfNotExists('exams', ['branch_id', 'session_id', 'class_type_id'], 'idx_exams_branch_session_class');
        $this->addIndexIfNotExists('assign_exams', ['exam_id', 'class_type_id'], 'idx_assign_exams_exam_class');
        $this->addIndexIfNotExists('examination_schedules', ['exam_id', 'class_type_id'], 'idx_exam_sched_exam_class');
        $this->addIndexIfNotExists('examination_admit_cards', ['exam_id', 'admission_id'], 'idx_exam_admit_exam_admission');
        $this->addIndexIfNotExists('fill_min_max_marks', ['exam_id', 'class_type_id', 'subject_id'], 'idx_fill_min_max_exam_class_subject');
        $this->addIndexIfNotExists('fill_marks', ['exam_id', 'admission_id'], 'idx_fill_marks_exam_student');

        // 5. Homework
        $this->addIndexIfNotExists('homeworks', ['branch_id', 'session_id', 'class_type_id'], 'idx_homeworks_lookup');
        $this->addIndexIfNotExists('homework_documents', ['upload_hw_id'], 'idx_hw_docs_upload_hw_id');
        $this->addIndexIfNotExists('homework_documents', ['admission_id'], 'idx_hw_docs_admission_id');
        $this->addIndexIfNotExists('upload_homeworks', ['homework_id', 'admission_id'], 'idx_upload_hw_homework_admission');

        // 6. User Panel & Master
        $this->addIndexIfNotExists('users', ['role_id', 'status'], 'idx_users_role_status');
        $this->addIndexIfNotExists('users', ['userName'], 'idx_users_username');
        $this->addIndexIfNotExists('users', ['mobile'], 'idx_users_mobile');
        $this->addIndexIfNotExists('users', ['branch_id', 'session_id'], 'idx_users_branch_session');
        $this->addIndexIfNotExists('role_permissions', ['role_id', 'sidebar_id'], 'idx_role_perm_role_sidebar');
        $this->addIndexIfNotExists('teacher_subjects', ['teacher_id', 'class_type_id', 'subject_id'], 'idx_teacher_subj_lookup');

        // 7. Reception & Visitors
        $this->addIndexIfNotExists('enquirys', ['branch_id', 'session_id', 'enquiry_status'], 'idx_enquirys_branch_session');
        $this->addIndexIfNotExists('enquirys', ['mobile'], 'idx_enquirys_mobile');
        $this->addIndexIfNotExists('visitors', ['branch_id', 'session_id'], 'idx_visitors_branch_session');

        // 8. Download Center
        $this->addIndexIfNotExists('download_center', ['branch_id', 'session_id', 'class_type_id'], 'idx_download_center_branch_session');

        // 9. Settings & Logs
        $this->addIndexIfNotExists('login_logs', ['user_id', 'branch_id', 'session_id', 'created_at'], 'idx_login_logs_user_branch_session');

        // 10. Message Terminal
        $this->addIndexIfNotExists('message_queues', ['message_status', 'receiver_number'], 'idx_msg_queue_status_receiver');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropIndexIfExists('fees_details_invoices', 'idx_fdi_branch_session_status');
        $this->dropIndexIfExists('fees_details_invoices', 'idx_fdi_admission_id');
        $this->dropIndexIfExists('fees_details_invoices', 'idx_fdi_payment_date');
        $this->dropIndexIfExists('fees_details_invoices', 'idx_fdi_invoice_no');

        $this->dropIndexIfExists('fees_assigns', 'idx_fees_assigns_admission_id');
        $this->dropIndexIfExists('fees_assigns', 'idx_fees_assigns_branch_session');

        $this->dropIndexIfExists('fees_assign_details', 'idx_fad_admission_id');
        $this->dropIndexIfExists('fees_assign_details', 'idx_fad_fees_group_id');
        $this->dropIndexIfExists('fees_assign_details', 'idx_fad_fees_master_id');

        $this->dropIndexIfExists('fees_collect', 'idx_fees_collect_admission_id');
        $this->dropIndexIfExists('fees_collect', 'idx_fees_collect_branch_session');

        $this->dropIndexIfExists('fees_master', 'idx_fees_master_group_class');
        $this->dropIndexIfExists('fees_master', 'idx_fees_master_branch_session');

        $this->dropIndexIfExists('fees_group', 'idx_fees_group_branch_session');

        $this->dropIndexIfExists('fees_detail', 'idx_fees_detail_perf');

        $this->dropIndexIfExists('admissions', 'idx_admissions_admission_no');
        $this->dropIndexIfExists('admissions', 'idx_admissions_mobile');
        $this->dropIndexIfExists('admissions', 'idx_admissions_branch_session_class');
        $this->dropIndexIfExists('student_documents', 'idx_student_docs_admission_id');

        $this->dropIndexIfExists('student_attendance', 'idx_student_att_class_date');

        $this->dropIndexIfExists('exams', 'idx_exams_branch_session_class');
        $this->dropIndexIfExists('assign_exams', 'idx_assign_exams_exam_class');
        $this->dropIndexIfExists('examination_schedules', 'idx_exam_sched_exam_class');
        $this->dropIndexIfExists('examination_admit_cards', 'idx_exam_admit_exam_admission');
        $this->dropIndexIfExists('fill_min_max_marks', 'idx_fill_min_max_exam_class_subject');
        $this->dropIndexIfExists('fill_marks', 'idx_fill_marks_exam_student');

        $this->dropIndexIfExists('homeworks', 'idx_homeworks_lookup');
        $this->dropIndexIfExists('homework_documents', 'idx_hw_docs_upload_hw_id');
        $this->dropIndexIfExists('homework_documents', 'idx_hw_docs_admission_id');
        $this->dropIndexIfExists('upload_homeworks', 'idx_upload_hw_homework_admission');

        $this->dropIndexIfExists('users', 'idx_users_role_status');
        $this->dropIndexIfExists('users', 'idx_users_username');
        $this->dropIndexIfExists('users', 'idx_users_mobile');
        $this->dropIndexIfExists('users', 'idx_users_branch_session');
        $this->dropIndexIfExists('role_permissions', 'idx_role_perm_role_sidebar');
        $this->dropIndexIfExists('teacher_subjects', 'idx_teacher_subj_lookup');

        $this->dropIndexIfExists('enquirys', 'idx_enquirys_branch_session');
        $this->dropIndexIfExists('enquirys', 'idx_enquirys_mobile');
        $this->dropIndexIfExists('visitors', 'idx_visitors_branch_session');

        $this->dropIndexIfExists('download_center', 'idx_download_center_branch_session');

        $this->dropIndexIfExists('login_logs', 'idx_login_logs_user_branch_session');

        $this->dropIndexIfExists('message_queues', 'idx_msg_queue_status_receiver');
    }
}
