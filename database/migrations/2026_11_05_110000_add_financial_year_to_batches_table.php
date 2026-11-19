<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddFinancialYearToBatchesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('batches', 'financial_year')) {
            Schema::table('batches', function (Blueprint $table) {
                $table->string('financial_year', 20)->nullable()->after('session_id')->index();
            });
        }

        // Backfill existing batches from sessions table
        $sessions = DB::table('sessions')->get()->keyBy('id');
        $batches = DB::table('batches')->get();

        foreach ($batches as $b) {
            $fy = null;
            if ($b->session_id && isset($sessions[$b->session_id])) {
                $s = $sessions[$b->session_id];
                $fy = $s->from_year . '-' . $s->to_year;
            } else {
                $fy = '2026-27';
            }

            DB::table('batches')->where('id', $b->id)->update([
                'financial_year' => $fy
            ]);
        }
    }

    public function down()
    {
        if (Schema::hasColumn('batches', 'financial_year')) {
            Schema::table('batches', function (Blueprint $table) {
                $table->dropColumn('financial_year');
            });
        }
    }
}
