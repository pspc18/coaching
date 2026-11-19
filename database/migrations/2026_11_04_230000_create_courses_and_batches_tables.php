<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCoursesAndBatchesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Create courses table
        if (!Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->integer('branch_id')->default(1);
                $table->integer('session_id')->default(1);
                $table->integer('user_id')->default(1);
                $table->string('name', 191);
                $table->string('code', 50)->nullable();
                $table->text('description')->nullable();
                $table->unsignedBigInteger('class_type_id')->nullable()->index();
                $table->integer('orderBy')->default(0);
                $table->tinyInteger('status')->default(1)->comment('1: Active, 0: Inactive');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. Create batches table
        if (!Schema::hasTable('batches')) {
            Schema::create('batches', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('course_id')->index();
                $table->integer('branch_id')->default(1);
                $table->integer('session_id')->default(1);
                $table->integer('user_id')->default(1);
                $table->string('name', 191);
                $table->string('shift', 50)->default('Morning')->comment('Morning, Evening, Afternoon, Special');
                $table->string('start_time', 50)->nullable()->comment('e.g. 08:00 AM');
                $table->string('end_time', 50)->nullable()->comment('e.g. 12:00 PM');
                $table->integer('capacity')->default(50)->nullable();
                $table->string('room_no', 50)->nullable();
                $table->unsignedBigInteger('class_type_id')->nullable()->index();
                $table->integer('orderBy')->default(0);
                $table->tinyInteger('status')->default(1)->comment('1: Active, 0: Inactive');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 3. Create course_subjects table
        if (!Schema::hasTable('course_subjects')) {
            Schema::create('course_subjects', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('course_id')->index();
                $table->unsignedBigInteger('subject_id')->nullable()->index();
                $table->string('subject_name', 191);
                $table->integer('sort_by')->default(0);
                $table->integer('branch_id')->default(1);
                $table->integer('session_id')->default(1);
                $table->integer('user_id')->default(1);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 4. Add course_id and batch_id to admissions table if missing
        if (Schema::hasTable('admissions')) {
            Schema::table('admissions', function (Blueprint $table) {
                if (!Schema::hasColumn('admissions', 'course_id')) {
                    $table->unsignedBigInteger('course_id')->nullable()->after('class_type_id')->index();
                }
                if (!Schema::hasColumn('admissions', 'batch_id')) {
                    $table->unsignedBigInteger('batch_id')->nullable()->after('course_id')->index();
                }
            });
        }

        // 5. Seed Coaching Courses from User Request
        $initialCourses = [
            'SSC',
            'Raj Level',
            'Airforce X',
            'Airforce Y',
            'Central Level',
            'X-Y Group',
            'STAFF',
            'Ldc',
            'Army',
            '11 foundation',
            '12 foundation',
            'RAS Foundation'
        ];

        $now = date('Y-m-d H:i:s');
        $branchId = 1;
        $sessionId = 1;
        $userId = 1;

        foreach ($initialCourses as $idx => $courseName) {
            // Check if class_type already exists or create one for bridge
            $existingClass = DB::table('class_types')
                ->where('branch_id', $branchId)
                ->where('session_id', $sessionId)
                ->where('name', $courseName)
                ->first();

            $classTypeId = $existingClass ? $existingClass->id : null;
            if (!$classTypeId) {
                $classTypeId = DB::table('class_types')->insertGetId([
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'branch_id' => $branchId,
                    'name' => $courseName,
                    'orderBy' => $idx + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Create course
            $existingCourse = DB::table('courses')
                ->where('branch_id', $branchId)
                ->where('session_id', $sessionId)
                ->where('name', $courseName)
                ->first();

            $courseId = $existingCourse ? $existingCourse->id : null;
            if (!$courseId) {
                $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', substr($courseName, 0, 4))) . '-' . str_pad($idx + 1, 2, '0', STR_PAD_LEFT);
                $courseId = DB::table('courses')->insertGetId([
                    'branch_id' => $branchId,
                    'session_id' => $sessionId,
                    'user_id' => $userId,
                    'name' => $courseName,
                    'code' => $code,
                    'description' => $courseName . ' coaching course',
                    'class_type_id' => $classTypeId,
                    'orderBy' => $idx + 1,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Seed 2 default Batches (Morning and Evening) for each course
            $batchesToCreate = [
                [
                    'name' => 'Batch 1 (Morning)',
                    'shift' => 'Morning',
                    'start_time' => '08:00 AM',
                    'end_time' => '12:00 PM',
                    'capacity' => 60,
                    'room_no' => 'Room-101',
                ],
                [
                    'name' => 'Batch 2 (Evening)',
                    'shift' => 'Evening',
                    'start_time' => '03:00 PM',
                    'end_time' => '07:00 PM',
                    'capacity' => 60,
                    'room_no' => 'Room-102',
                ]
            ];

            foreach ($batchesToCreate as $bIdx => $bInfo) {
                $hasBatch = DB::table('batches')
                    ->where('course_id', $courseId)
                    ->where('name', $bInfo['name'])
                    ->exists();

                if (!$hasBatch) {
                    DB::table('batches')->insert([
                        'course_id' => $courseId,
                        'branch_id' => $branchId,
                        'session_id' => $sessionId,
                        'user_id' => $userId,
                        'name' => $bInfo['name'],
                        'shift' => $bInfo['shift'],
                        'start_time' => $bInfo['start_time'],
                        'end_time' => $bInfo['end_time'],
                        'capacity' => $bInfo['capacity'],
                        'room_no' => $bInfo['room_no'],
                        'class_type_id' => $classTypeId,
                        'orderBy' => $bIdx + 1,
                        'status' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }

            // Seed subjects based on course
            $defaultCourseSubjects = [];
            if (stripos($courseName, '11') !== false || stripos($courseName, '12') !== false) {
                $defaultCourseSubjects = ['Physics', 'Chemistry', 'Maths', 'Biology', 'Hindi', 'English'];
            } elseif (stripos($courseName, 'Airforce X') !== false) {
                $defaultCourseSubjects = ['Physics', 'Maths', 'English'];
            } elseif (stripos($courseName, 'Airforce Y') !== false) {
                $defaultCourseSubjects = ['English', 'Reasoning', 'General Awareness'];
            } elseif (stripos($courseName, 'SSC') !== false || stripos($courseName, 'Ldc') !== false || stripos($courseName, 'Central') !== false || stripos($courseName, 'Raj') !== false) {
                $defaultCourseSubjects = ['Maths', 'Reasoning', 'English', 'Hindi', 'General Knowledge'];
            } elseif (stripos($courseName, 'RAS') !== false) {
                $defaultCourseSubjects = ['General Studies', 'Rajasthan GK', 'Hindi', 'English'];
            } elseif (stripos($courseName, 'Army') !== false) {
                $defaultCourseSubjects = ['General Knowledge', 'General Science', 'Maths'];
            } else {
                $defaultCourseSubjects = ['General Studies', 'English', 'Maths'];
            }

            foreach ($defaultCourseSubjects as $sIdx => $subName) {
                // Ensure in all_subjects
                $allSub = DB::table('all_subjects')->where('name', $subName)->first();
                $allSubId = $allSub ? $allSub->id : null;
                if (!$allSubId) {
                    $allSubId = DB::table('all_subjects')->insertGetId([
                        'branch_id' => $branchId,
                        'user_id' => $userId,
                        'session_id' => $sessionId,
                        'name' => $subName,
                        'other_subject' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                // Add to course_subjects
                $hasCourseSubject = DB::table('course_subjects')
                    ->where('course_id', $courseId)
                    ->where('subject_name', $subName)
                    ->exists();

                if (!$hasCourseSubject) {
                    DB::table('course_subjects')->insert([
                        'course_id' => $courseId,
                        'subject_id' => $allSubId,
                        'subject_name' => $subName,
                        'sort_by' => $sIdx + 1,
                        'branch_id' => $branchId,
                        'session_id' => $sessionId,
                        'user_id' => $userId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                // Sync subject table for class_type_id bridge
                if ($classTypeId) {
                    $hasClassSub = DB::table('subject')
                        ->where('class_type_id', $classTypeId)
                        ->where('name', $subName)
                        ->where('session_id', $sessionId)
                        ->where('branch_id', $branchId)
                        ->exists();

                    if (!$hasClassSub) {
                        DB::table('subject')->insert([
                            'user_id' => $userId,
                            'session_id' => $sessionId,
                            'branch_id' => $branchId,
                            'name' => $subName,
                            'other_subject' => 0,
                            'sort_by' => $sIdx + 1,
                            'class_type_id' => $classTypeId,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }
        }

        // 6. Update or insert sidebar_sub for Courses and Batches under Master (sidebar_id = 9)
        $coursesSubId = DB::table('sidebar_sub')->where('url', 'courses')->value('id');
        if (!$coursesSubId) {
            $coursesSubId = DB::table('sidebar_sub')->insertGetId([
                'user_id' => $userId,
                'branch_id' => $branchId,
                'session_id' => $sessionId,
                'sidebar_id' => 9,
                'sidebar_name' => 'Master Panel',
                'name' => 'Courses',
                'bg_color' => 'primary',
                'url' => 'courses',
                'hindi_name' => 'कोर्स',
                'ican' => 'fa-graduation-cap',
                'orderBy' => 2,
                'sub_sidebar' => 'yes',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $batchesSubId = DB::table('sidebar_sub')->where('url', 'batches')->value('id');
        if (!$batchesSubId) {
            $batchesSubId = DB::table('sidebar_sub')->insertGetId([
                'user_id' => $userId,
                'branch_id' => $branchId,
                'session_id' => $sessionId,
                'sidebar_id' => 9,
                'sidebar_name' => 'Master Panel',
                'name' => 'Batches',
                'bg_color' => 'info',
                'url' => 'batches',
                'hindi_name' => 'बैच',
                'ican' => 'fa-users',
                'orderBy' => 3,
                'sub_sidebar' => 'yes',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $courseSubjectsSubId = DB::table('sidebar_sub')->where('url', 'course_subjects')->value('id');
        if (!$courseSubjectsSubId) {
            $courseSubjectsSubId = DB::table('sidebar_sub')->insertGetId([
                'user_id' => $userId,
                'branch_id' => $branchId,
                'session_id' => $sessionId,
                'sidebar_id' => 9,
                'sidebar_name' => 'Master Panel',
                'name' => 'Course Subjects',
                'bg_color' => 'success',
                'url' => 'course_subjects',
                'hindi_name' => 'कोर्स विषय',
                'ican' => 'fa-book',
                'orderBy' => 4,
                'sub_sidebar' => 'yes',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Adjust Class (id 28) order to 15 so Courses & Batches are at the top of Master
        DB::table('sidebar_sub')->where('url', 'add_class')->update(['orderBy' => 15]);

        // Grant permission to user_permission records for Master (sidebar_id = 9)
        $permRecords = DB::table('user_permission')->where('sidebar_id', 9)->get();
        foreach ($permRecords as $perm) {
            $subIds = array_filter(explode(',', (string)$perm->sub_sidebar_id));
            $newIds = array_unique(array_merge($subIds, [$coursesSubId, $batchesSubId, $courseSubjectsSubId]));
            DB::table('user_permission')->where('id', $perm->id)->update([
                'sub_sidebar_id' => implode(',', $newIds),
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
        Schema::dropIfExists('course_subjects');
        Schema::dropIfExists('batches');
        Schema::dropIfExists('courses');

        if (Schema::hasTable('admissions')) {
            Schema::table('admissions', function (Blueprint $table) {
                if (Schema::hasColumn('admissions', 'course_id')) {
                    $table->dropColumn('course_id');
                }
                if (Schema::hasColumn('admissions', 'batch_id')) {
                    $table->dropColumn('batch_id');
                }
            });
        }
    }
}
