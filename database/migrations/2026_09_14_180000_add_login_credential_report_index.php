<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddLoginCredentialReportIndex extends Migration
{
    private $indexName = 'idx_admissions_login_credential_report';

    public function up()
    {
        if (!Schema::hasTable('admissions')) {
            return;
        }

        $existing = DB::select("SHOW INDEX FROM `admissions` WHERE Key_name = ?", [$this->indexName]);
        if (empty($existing)) {
            Schema::table('admissions', function (Blueprint $table) {
                $table->index(
                    ['branch_id', 'session_id', 'class_type_id', 'status'],
                    $this->indexName
                );
            });
        }
    }

    public function down()
    {
        if (!Schema::hasTable('admissions')) {
            return;
        }

        $existing = DB::select("SHOW INDEX FROM `admissions` WHERE Key_name = ?", [$this->indexName]);
        if (!empty($existing)) {
            Schema::table('admissions', function (Blueprint $table) {
                $table->dropIndex($this->indexName);
            });
        }
    }
}
