<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ConvertQuestionsTableToUtf8mb4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = ['questions', 'assign_questions', 'exams', 'assign_exams', 'examination_schedules', 'examination_admit_cards'];
        foreach ($tables as $tb) {
            try {
                DB::statement("ALTER TABLE `{$tb}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            } catch (\Exception $e) {
                // Table might not exist or already converted
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No downgrade needed for utf8mb4
    }
}
