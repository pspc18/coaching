<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('omr_results', function (Blueprint $table) {
            if (!Schema::hasColumn('omr_results', 'stored_file')) {
                $table->string('stored_file', 255)->nullable()->after('source_filename');
            }
            if (!Schema::hasColumn('omr_results', 'annotated_image_url')) {
                $table->text('annotated_image_url')->nullable()->after('stored_file');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('omr_results', function (Blueprint $table) {
            if (Schema::hasColumn('omr_results', 'annotated_image_url')) {
                $table->dropColumn('annotated_image_url');
            }
            if (Schema::hasColumn('omr_results', 'stored_file')) {
                $table->dropColumn('stored_file');
            }
        });
    }
};
