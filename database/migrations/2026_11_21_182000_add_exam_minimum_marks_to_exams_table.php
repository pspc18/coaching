<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExamMinimumMarksToExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('exams') && !Schema::hasColumn('exams', 'exam_minimum_marks')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->decimal('exam_minimum_marks', 10, 2)->nullable()->default(0.00)->after('exam_maximum_marks');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('exams') && Schema::hasColumn('exams', 'exam_minimum_marks')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->dropColumn('exam_minimum_marks');
            });
        }
    }
}
