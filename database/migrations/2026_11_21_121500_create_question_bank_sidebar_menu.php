<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateQuestionBankSidebarMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Create or retrieve "Question Bank" in sidebars table
        $qbSidebar = DB::table('sidebars')->where('name', 'Question Bank')->first();

        if (!$qbSidebar) {
            // Find Examination Panel order_by (default 5)
            $examSidebar = DB::table('sidebars')->where('id', 8)->orWhere('name', 'Examination Panel')->first();
            $examOrder = $examSidebar ? (int) $examSidebar->order_by : 5;
            $newOrder = $examOrder + 1; // 6

            // Shift other sidebars with order_by >= $newOrder
            DB::table('sidebars')
                ->where('order_by', '>=', $newOrder)
                ->increment('order_by');

            $qbSidebarId = DB::table('sidebars')->insertGetId([
                'user_id'    => 1,
                'branch_id'  => 1,
                'session_id' => 1,
                'name'       => 'Question Bank',
                'hindi_name' => 'प्रश्न बैंक',
                'url'        => 'view/question',
                'apk_url'    => 'view/question',
                'ican'       => 'fa fa-question-circle',
                'apk_icon'   => 'help-circle',
                'order_by'   => $newOrder,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $qbSidebarId = $qbSidebar->id;
            DB::table('sidebars')->where('id', $qbSidebarId)->update([
                'url'        => 'view/question',
                'ican'       => 'fa fa-question-circle',
                'updated_at' => now(),
            ]);
        }

        // 2. Configure Submenus for Question Bank:
        //    (a) Questions Add (orderBy: 1)
        //    (b) Questions List (orderBy: 2)

        // Submenu: Questions Add
        $addSub = DB::table('sidebar_sub')
            ->where('sidebar_id', $qbSidebarId)
            ->where('url', 'add/question')
            ->first();

        if (!$addSub) {
            $addQSubId = DB::table('sidebar_sub')->insertGetId([
                'user_id'      => 1,
                'branch_id'    => 1,
                'session_id'   => 1,
                'sidebar_id'   => $qbSidebarId,
                'sidebar_name' => 'Question Bank',
                'name'         => 'Questions Add',
                'hindi_name'   => 'प्रश्न जोड़ें',
                'bg_color'     => 'primary',
                'url'          => 'add/question',
                'ican'         => 'fa-plus-circle',
                'orderBy'      => 1,
                'sub_sidebar'  => 'yes',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        } else {
            $addQSubId = $addSub->id;
            DB::table('sidebar_sub')->where('id', $addQSubId)->update([
                'name'         => 'Questions Add',
                'hindi_name'   => 'प्रश्न जोड़ें',
                'sidebar_name' => 'Question Bank',
                'sidebar_id'   => $qbSidebarId,
                'orderBy'      => 1,
                'sub_sidebar'  => 'yes',
                'updated_at'   => now(),
            ]);
        }

        // Submenu: Questions List (Migrate row 104 if present, else create/update)
        $listSub = DB::table('sidebar_sub')->where('id', 104)->first();
        if ($listSub) {
            $listQSubId = 104;
            DB::table('sidebar_sub')->where('id', 104)->update([
                'sidebar_id'   => $qbSidebarId,
                'sidebar_name' => 'Question Bank',
                'name'         => 'Questions List',
                'hindi_name'   => 'प्रश्नावली सूची',
                'bg_color'     => 'success',
                'url'          => 'view/question',
                'ican'         => 'fa-list-ul',
                'orderBy'      => 2,
                'sub_sidebar'  => 'yes',
                'updated_at'   => now(),
            ]);
        } else {
            $existingList = DB::table('sidebar_sub')
                ->where('sidebar_id', $qbSidebarId)
                ->where('url', 'view/question')
                ->first();

            if (!$existingList) {
                $listQSubId = DB::table('sidebar_sub')->insertGetId([
                    'user_id'      => 1,
                    'branch_id'    => 1,
                    'session_id'   => 1,
                    'sidebar_id'   => $qbSidebarId,
                    'sidebar_name' => 'Question Bank',
                    'name'         => 'Questions List',
                    'hindi_name'   => 'प्रश्नावली सूची',
                    'bg_color'     => 'success',
                    'url'          => 'view/question',
                    'ican'         => 'fa-list-ul',
                    'orderBy'      => 2,
                    'sub_sidebar'  => 'yes',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            } else {
                $listQSubId = $existingList->id;
                DB::table('sidebar_sub')->where('id', $listQSubId)->update([
                    'name'         => 'Questions List',
                    'hindi_name'   => 'प्रश्नावली सूची',
                    'sidebar_name' => 'Question Bank',
                    'sidebar_id'   => $qbSidebarId,
                    'orderBy'      => 2,
                    'sub_sidebar'  => 'yes',
                    'updated_at'   => now(),
                ]);
            }
        }

        // 3. Reorder Examination Panel (sidebar_id = 8) submenus into a clean, logical sequence:
        //    1: Exams (view/exam)
        //    2: Examination Schedule (add/examination_schedule)
        //    3: Admit Card (download_admit_card)
        //    4: Fill Marks (fill_marks)
        //    5: Fill Marks By Excel (fill-marks-by-excel)
        //    6: Exam Wise Report (exam_wise_report)
        //    7: Subject Wise Report (subject_wise_report)
        //    8: Green Sheet Report (green_sheet_report)
        $examSequence = [
            'view/exam'                => ['order' => 1, 'name' => 'Exams'],
            'add/examination_schedule' => ['order' => 2, 'name' => 'Examination Schedule'],
            'download_admit_card'      => ['order' => 3, 'name' => 'Admit Card'],
            'fill_marks'               => ['order' => 4, 'name' => 'Fill Marks'],
            'fill-marks-by-excel'      => ['order' => 5, 'name' => 'Fill Marks By Excel'],
            'exam_wise_report'         => ['order' => 6, 'name' => 'Exam Wise Report'],
            'subject_wise_report'      => ['order' => 7, 'name' => 'Subject Wise Report'],
            'green_sheet_report'       => ['order' => 8, 'name' => 'Green Sheet Report'],
        ];

        foreach ($examSequence as $url => $config) {
            DB::table('sidebar_sub')
                ->where('sidebar_id', 8)
                ->where('url', $url)
                ->update([
                    'orderBy'    => $config['order'],
                    'updated_at' => now(),
                ]);
        }

        // 4. Update branch table: ensure Question Bank sidebar is added to all branches' branch_sidebar_id
        if (Schema::hasTable('branch')) {
            $branches = DB::table('branch')->get();
            foreach ($branches as $branch) {
                $sidebarIds = array_filter(array_map('trim', explode(',', (string) ($branch->branch_sidebar_id ?? ''))));
                if (!in_array((string) $qbSidebarId, $sidebarIds, true)) {
                    $sidebarIds[] = (string) $qbSidebarId;
                    DB::table('branch')->where('id', $branch->id)->update([
                        'branch_sidebar_id' => implode(',', array_unique($sidebarIds)),
                    ]);
                }
            }
        }

        // 5. Update role_permissions & user_permission for Question Bank
        $qbSubIds = implode(',', array_unique([(string) $addQSubId, (string) $listQSubId]));

        if (Schema::hasTable('role_permissions')) {
            // Find all roles
            $roleIds = DB::table('role_permissions')
                ->where('sidebar_id', 8)
                ->pluck('role_id')
                ->unique()
                ->toArray();

            // Always ensure Admin role (1) is included
            if (!in_array(1, $roleIds)) {
                $roleIds[] = 1;
            }

            foreach ($roleIds as $rId) {
                // Remove 104 from Examination Panel (sidebar_id = 8)
                $examPerm = DB::table('role_permissions')
                    ->where('role_id', $rId)
                    ->where('sidebar_id', 8)
                    ->first();

                if ($examPerm && !empty($examPerm->sub_sidebar_id)) {
                    $cleanedSubIds = array_filter(
                        explode(',', $examPerm->sub_sidebar_id),
                        fn($id) => !in_array(trim($id), [(string) $listQSubId, (string) $addQSubId, '104'], true)
                    );
                    DB::table('role_permissions')
                        ->where('role_id', $rId)
                        ->where('sidebar_id', 8)
                        ->update([
                            'sub_sidebar_id' => !empty($cleanedSubIds) ? implode(',', $cleanedSubIds) : null,
                        ]);
                }

                // Grant full permissions for Question Bank
                DB::table('role_permissions')->updateOrInsert(
                    [
                        'role_id'    => $rId,
                        'sidebar_id' => $qbSidebarId,
                    ],
                    [
                        'sidebar_name'   => 'Question Bank',
                        'sub_sidebar_id' => $qbSubIds,
                        'add'            => 1,
                        'edit'           => 1,
                        'delete'         => 1,
                        'view'           => 1,
                        'print'          => 1,
                    ]
                );
            }
        }

        // 6. Clean up user_permission table
        if (Schema::hasTable('user_permission')) {
            $userPerms = DB::table('user_permission')->where('sidebar_id', 8)->get();
            foreach ($userPerms as $up) {
                if (!empty($up->sub_sidebar_id)) {
                    $cleaned = array_filter(
                        explode(',', $up->sub_sidebar_id),
                        fn($id) => !in_array(trim($id), [(string) $listQSubId, (string) $addQSubId, '104'], true)
                    );
                    DB::table('user_permission')->where('id', $up->id)->update([
                        'sub_sidebar_id' => !empty($cleaned) ? implode(',', $cleaned) : null,
                    ]);
                }

                // Give this user permission to Question Bank
                $existingUserQb = DB::table('user_permission')
                    ->where('user_id', $up->user_id)
                    ->where('sidebar_id', $qbSidebarId)
                    ->first();

                if (!$existingUserQb) {
                    DB::table('user_permission')->insert([
                        'user_id'        => $up->user_id,
                        'sidebar_id'     => $qbSidebarId,
                        'sidebar_name'   => 'Question Bank',
                        'sub_sidebar_id' => $qbSubIds,
                        'add'            => $up->add ?? 1,
                        'edit'           => $up->edit ?? 1,
                        'delete'         => $up->delete ?? 1,
                        'view'           => $up->view ?? 1,
                        'print'          => $up->print ?? 1,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
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
        $qbSidebar = DB::table('sidebars')->where('name', 'Question Bank')->first();
        if ($qbSidebar) {
            $qbSidebarId = $qbSidebar->id;

            // Restore row 104 to Examination Panel (8)
            DB::table('sidebar_sub')->where('id', 104)->update([
                'sidebar_id'   => 8,
                'sidebar_name' => 'Examination Panel',
                'name'         => 'Question Bank',
                'url'          => 'view/question',
                'orderBy'      => 1,
            ]);

            // Delete Questions Add
            DB::table('sidebar_sub')
                ->where('sidebar_id', $qbSidebarId)
                ->where('url', 'add/question')
                ->delete();

            // Delete role permissions & user permissions
            DB::table('role_permissions')->where('sidebar_id', $qbSidebarId)->delete();
            DB::table('user_permission')->where('sidebar_id', $qbSidebarId)->delete();

            // Delete sidebar
            DB::table('sidebars')->where('id', $qbSidebarId)->delete();
        }
    }
}
