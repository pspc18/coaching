<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAiAndAdvancedFieldsToQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'image')) {
                $table->string('image')->nullable()->after('ans4');
            }
            if (!Schema::hasColumn('questions', 'explanation')) {
                $table->text('explanation')->nullable()->after('image');
            }
            if (!Schema::hasColumn('questions', 'difficulty_level')) {
                $table->string('difficulty_level', 50)->nullable()->default('Medium')->after('explanation');
            }
            if (!Schema::hasColumn('questions', 'source_file')) {
                $table->string('source_file')->nullable()->after('difficulty_level');
            }
            if (!Schema::hasColumn('questions', 'ai_extracted')) {
                $table->boolean('ai_extracted')->default(false)->after('source_file');
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
            $cols = ['image', 'explanation', 'difficulty_level', 'source_file', 'ai_extracted'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('questions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}
