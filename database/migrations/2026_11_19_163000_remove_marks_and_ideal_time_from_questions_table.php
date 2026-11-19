<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveMarksAndIdealTimeFromQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $colsToDrop = [];
            if (Schema::hasColumn('questions', 'marks')) {
                $colsToDrop[] = 'marks';
            }
            if (Schema::hasColumn('questions', 'negative_marks')) {
                $colsToDrop[] = 'negative_marks';
            }
            if (Schema::hasColumn('questions', 'ideal_time')) {
                $colsToDrop[] = 'ideal_time';
            }

            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
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
            if (!Schema::hasColumn('questions', 'marks')) {
                $table->decimal('marks', 8, 2)->default(1.00)->nullable();
            }
            if (!Schema::hasColumn('questions', 'negative_marks')) {
                $table->decimal('negative_marks', 8, 2)->default(0.00)->nullable();
            }
            if (!Schema::hasColumn('questions', 'ideal_time')) {
                $table->integer('ideal_time')->default(60)->nullable();
            }
        });
    }
}
