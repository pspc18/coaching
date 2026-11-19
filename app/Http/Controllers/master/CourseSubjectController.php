<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSubject;
use App\Models\Master\AllSubjects;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CourseSubjectController extends Controller
{
    public function assign(Request $request)
    {
        $branchId = Session::get('branch_id') ?? 1;
        $sessionId = Session::get('session_id') ?? 1;
        $userId = Session::get('id') ?? 1;

        if ($request->isMethod('post')) {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
            ]);

            $courseId = $request->course_id;
            $course = Course::findOrFail($courseId);
            $selectedSubjects = (array) ($request->subjects ?? []);
            $customSubject = trim((string)$request->new_subject_name);

            // If user entered a custom new subject, add to list
            if (!empty($customSubject)) {
                $allSub = AllSubjects::where('name', $customSubject)->first();
                if (!$allSub) {
                    $allSub = AllSubjects::create([
                        'branch_id' => $branchId,
                        'user_id' => $userId,
                        'session_id' => $sessionId,
                        'name' => $customSubject,
                        'other_subject' => 0,
                    ]);
                }
                if (!in_array($customSubject, $selectedSubjects, true)) {
                    $selectedSubjects[] = $customSubject;
                }
            }

            // Delete subjects no longer in selected list
            CourseSubject::where('course_id', $courseId)
                ->where('branch_id', $branchId)
                ->where('session_id', $sessionId)
                ->whereNotIn('subject_name', $selectedSubjects)
                ->delete();

            // Insert or update selected subjects
            foreach ($selectedSubjects as $idx => $subName) {
                $cleanName = trim($subName);
                if (empty($cleanName)) continue;

                $allSub = AllSubjects::where('name', $cleanName)->first();
                $allSubId = $allSub ? $allSub->id : null;
                if (!$allSubId) {
                    $allSub = AllSubjects::create([
                        'branch_id' => $branchId,
                        'user_id' => $userId,
                        'session_id' => $sessionId,
                        'name' => $cleanName,
                        'other_subject' => 0,
                    ]);
                    $allSubId = $allSub->id;
                }

                $existing = CourseSubject::where('course_id', $courseId)
                    ->where('subject_name', $cleanName)
                    ->where('branch_id', $branchId)
                    ->where('session_id', $sessionId)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'deleted_at' => null,
                        'sort_by' => $idx + 1,
                        'subject_id' => $allSubId,
                    ]);
                } else {
                    CourseSubject::create([
                        'course_id' => $courseId,
                        'subject_id' => $allSubId,
                        'subject_name' => $cleanName,
                        'sort_by' => $idx + 1,
                        'branch_id' => $branchId,
                        'session_id' => $sessionId,
                        'user_id' => $userId,
                    ]);
                }

                // Bridge sync: also sync legacy `subject` table for course->class_type_id
                if ($course->class_type_id) {
                    $existingClassSub = Subject::where('class_type_id', $course->class_type_id)
                        ->where('name', $cleanName)
                        ->where('branch_id', $branchId)
                        ->where('session_id', $sessionId)
                        ->first();

                    if ($existingClassSub) {
                        $existingClassSub->update([
                            'deleted_at' => null,
                            'sort_by' => $idx + 1,
                        ]);
                    } else {
                        Subject::create([
                            'user_id' => $userId,
                            'session_id' => $sessionId,
                            'branch_id' => $branchId,
                            'name' => $cleanName,
                            'other_subject' => 0,
                            'sort_by' => $idx + 1,
                            'class_type_id' => $course->class_type_id,
                        ]);
                    }
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Subjects assigned to course successfully.',
                ]);
            }

            return redirect('course_subjects?course_id=' . $courseId)->with('message', 'Subjects assigned to course successfully.');
        }

        $allCourses = Course::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->orderBy('orderBy', 'ASC')
            ->orderBy('name', 'ASC')
            ->get();

        $selectedCourseId = $request->course_id ?? ($allCourses->first()->id ?? null);

        $assignedSubjects = [];
        $selectedCourse = null;
        if ($selectedCourseId) {
            $selectedCourse = Course::with('courseSubjects')->find($selectedCourseId);
            if ($selectedCourse) {
                $assignedSubjects = $selectedCourse->courseSubjects->pluck('subject_name')->all();
            }
        }

        $masterSubjects = AllSubjects::orderBy('name', 'ASC')->get()->unique('name');

        return view('master.course_subject.assign', [
            'courses' => $allCourses,
            'selectedCourse' => $selectedCourse,
            'selectedCourseId' => $selectedCourseId,
            'assignedSubjects' => $assignedSubjects,
            'masterSubjects' => $masterSubjects,
        ]);
    }
}
