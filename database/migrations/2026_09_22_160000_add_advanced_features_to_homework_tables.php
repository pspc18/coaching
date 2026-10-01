<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('homeworks')) {
            Schema::table('homeworks', function (Blueprint $table) {
                if (!Schema::hasColumn('homeworks', 'max_marks')) {
                    $table->integer('max_marks')->nullable()->after('title');
                }
                if (!Schema::hasColumn('homeworks', 'allow_late_submission')) {
                    $table->tinyInteger('allow_late_submission')->default(1)->after('submission_date');
                }
            });
        }

        if (Schema::hasTable('upload_homeworks')) {
            Schema::table('upload_homeworks', function (Blueprint $table) {
                if (!Schema::hasColumn('upload_homeworks', 'marks')) {
                    $table->decimal('marks', 5, 2)->nullable()->after('hw_review_id');
                }
            });
        }

        if (Schema::hasTable('homework_documents')) {
            Schema::table('homework_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('homework_documents', 'marks')) {
                    $table->decimal('marks', 5, 2)->nullable()->after('hw_review');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('homeworks')) {
            Schema::table('homeworks', function (Blueprint $table) {
                if (Schema::hasColumn('homeworks', 'max_marks')) {
                    $table->dropColumn('max_marks');
                }
                if (Schema::hasColumn('homeworks', 'allow_late_submission')) {
                    $table->dropColumn('allow_late_submission');
                }
            });
        }

        if (Schema::hasTable('upload_homeworks')) {
            Schema::table('upload_homeworks', function (Blueprint $table) {
                if (Schema::hasColumn('upload_homeworks', 'marks')) {
                    $table->dropColumn('marks');
                }
            });
        }

        if (Schema::hasTable('homework_documents')) {
            Schema::table('homework_documents', function (Blueprint $table) {
                if (Schema::hasColumn('homework_documents', 'marks')) {
                    $table->dropColumn('marks');
                }
            });
        }
    }
};
