<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerformanceOptimizationIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. fill_marks table indexes
        Schema::table('fill_marks', function (Blueprint $table) {
            $table->index(['exam_id', 'class_type_id', 'branch_id', 'session_id'], 'idx_fill_marks_lookup');
            $table->index(['admission_id', 'subject_id'], 'idx_fill_marks_student_subject');
        });

        // 2. fees_detail table indexes
        Schema::table('fees_detail', function (Blueprint $table) {
            $table->index(['branch_id', 'session_id', 'date'], 'idx_fees_detail_branch_session_date');
        });

        // 3. notifications table indexes
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'branch_id', 'session_id', 'show_status', 'message_seen'], 'idx_notifications_user_lookup');
        });

        // 4. sidebar_sub table indexes
        Schema::table('sidebar_sub', function (Blueprint $table) {
            $table->index(['sidebar_id', 'sub_sidebar'], 'idx_sidebar_sub_lookup');
        });

        // 5. user_permission table indexes
        Schema::table('user_permission', function (Blueprint $table) {
            $table->index(['user_id', 'sidebar_id'], 'idx_user_permission_lookup');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fill_marks', function (Blueprint $table) {
            $table->dropIndex('idx_fill_marks_lookup');
            $table->dropIndex('idx_fill_marks_student_subject');
        });

        Schema::table('fees_detail', function (Blueprint $table) {
            $table->dropIndex('idx_fees_detail_branch_session_date');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('idx_notifications_user_lookup');
        });

        Schema::table('sidebar_sub', function (Blueprint $table) {
            $table->dropIndex('idx_sidebar_sub_lookup');
        });

        Schema::table('user_permission', function (Blueprint $table) {
            $table->dropIndex('idx_user_permission_lookup');
        });
    }
}
