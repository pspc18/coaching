<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupAndDirectionToQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'ans5')) {
                $table->text('ans5')->nullable()->after('ans4');
            }
            if (!Schema::hasColumn('questions', 'group_id')) {
                $table->string('group_id', 100)->nullable()->index()->after('question_type_id');
            }
            if (!Schema::hasColumn('questions', 'direction')) {
                $table->longText('direction')->nullable()->after('group_id');
            }
            if (!Schema::hasColumn('questions', 'direction_table')) {
                $table->longText('direction_table')->nullable()->after('direction');
            }
            if (!Schema::hasColumn('questions', 'sub_question_no')) {
                $table->string('sub_question_no', 50)->nullable()->after('direction_table');
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
            $cols = ['ans5', 'group_id', 'direction', 'direction_table', 'sub_question_no'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('questions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}
