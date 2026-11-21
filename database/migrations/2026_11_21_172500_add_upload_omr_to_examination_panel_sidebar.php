<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUploadOmrToExaminationPanelSidebar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Find or create "Upload OMR Sheet" in sidebar_sub under sidebar_id = 8
        $omrSub = DB::table('sidebar_sub')
            ->where('sidebar_id', 8)
            ->where('url', 'upload_omr')
            ->first();

        if (!$omrSub) {
            $omrSubId = DB::table('sidebar_sub')->insertGetId([
                'user_id'      => 1,
                'branch_id'    => 1,
                'session_id'   => 1,
                'sidebar_id'   => 8,
                'sidebar_name' => 'Examination Panel',
                'name'         => 'Upload OMR Sheet',
                'hindi_name'   => 'ओएमआर शीट अपलोड करें',
                'bg_color'     => 'info',
                'url'          => 'upload_omr',
                'ican'         => 'fa-qrcode',
                'orderBy'      => 6,
                'sub_sidebar'  => 'yes',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        } else {
            $omrSubId = $omrSub->id;
            DB::table('sidebar_sub')->where('id', $omrSubId)->update([
                'sidebar_id'   => 8,
                'sidebar_name' => 'Examination Panel',
                'name'         => 'Upload OMR Sheet',
                'hindi_name'   => 'ओएमआर शीट अपलोड करें',
                'bg_color'     => 'info',
                'url'          => 'upload_omr',
                'ican'         => 'fa-qrcode',
                'orderBy'      => 6,
                'sub_sidebar'  => 'yes',
                'deleted_at'   => null,
                'updated_at'   => now(),
            ]);
        }

        // 2. Set the complete 9-item Examination Panel sequence
        $sequence = [
            'view/exam'                => 1, // Exams
            'add/examination_schedule' => 2, // Examination Schedule
            'download_admit_card'      => 3, // Admit Card
            'fill_marks'               => 4, // Fill Marks (Manual)
            'fill-marks-by-excel'      => 5, // Fill Marks By Excel
            'upload_omr'               => 6, // Upload OMR Sheet
            'exam_wise_report'         => 7, // Exam Wise Report
            'subject_wise_report'      => 8, // Subject Wise Report
            'green_sheet_report'       => 9, // Green Sheet Report
        ];

        foreach ($sequence as $url => $order) {
            DB::table('sidebar_sub')
                ->where('sidebar_id', 8)
                ->where('url', $url)
                ->update([
                    'orderBy'    => $order,
                    'updated_at' => now(),
                ]);
        }

        // 3. Grant permission to user_id = 1 for sidebar 8
        $adminPerm = DB::table('user_permission')
            ->where('user_id', 1)
            ->where('sidebar_id', 8)
            ->first();

        if ($adminPerm) {
            $existingSubIds = array_filter(array_map('trim', explode(',', (string)($adminPerm->sub_sidebar_id ?? ''))));
            if (!in_array((string)$omrSubId, $existingSubIds, true)) {
                $existingSubIds[] = (string)$omrSubId;
                DB::table('user_permission')
                    ->where('id', $adminPerm->id)
                    ->update([
                        'sub_sidebar_id' => implode(',', array_unique($existingSubIds)),
                        'updated_at'     => now(),
                    ]);
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
        DB::table('sidebar_sub')
            ->where('sidebar_id', 8)
            ->where('url', 'upload_omr')
            ->delete();
    }
}
