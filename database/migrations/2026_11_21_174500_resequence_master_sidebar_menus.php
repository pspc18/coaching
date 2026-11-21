<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ResequenceMasterSidebarMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Rename row 124 ("Add Subject" -> "Subjects" / "Subject Master")
        DB::table('sidebar_sub')
            ->where('sidebar_id', 9)
            ->where('url', 'create_subject')
            ->update([
                'name'         => 'Subjects',
                'hindi_name'   => 'विषय सूची (मास्टर)',
                'ican'         => 'fa-book',
                'updated_at'   => now(),
            ]);

        // 2. Set clean academic-first sequence for Master (sidebar_id = 9)
        //    1: Courses (courses)
        //    2: Batches (batches)
        //    3: Subjects (create_subject)
        //    4: Course Subjects (course_subjects)
        //    5: Academic Calendar (add_weekend)
        //    6: Sessions (session_add)
        //    7: Role (role_add)
        //    8: Branch (viewBranch)
        $masterSequence = [
            'courses'         => 1,
            'batches'         => 2,
            'create_subject'  => 3,
            'course_subjects' => 4,
            'add_weekend'     => 5,
            'session_add'     => 6,
            'role_add'        => 7,
            'viewBranch'      => 8,
        ];

        foreach ($masterSequence as $url => $order) {
            DB::table('sidebar_sub')
                ->where('sidebar_id', 9)
                ->where('url', $url)
                ->update([
                    'orderBy'    => $order,
                    'updated_at' => now(),
                ]);
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
            ->where('sidebar_id', 9)
            ->where('url', 'create_subject')
            ->update([
                'name' => 'Add Subject',
                'orderBy' => 4,
            ]);
    }
}
