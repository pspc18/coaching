<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Batch;
use App\Models\CourseSubject;
use App\Models\ClassType;
use App\Models\Admission;
use App\Models\Subject;
use App\Models\Master\AllSubjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        return $this->add($request);
    }

    public function add(Request $request)
    {
        $branchId = Session::get('branch_id') ?? 1;
        $sessionId = Session::get('session_id') ?? 1;
        $userId = Session::get('id') ?? 1;

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:191',
            ]);

            $courseName = trim($request->name);
            $code = trim((string)$request->code);
            if (empty($code)) {
                $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', substr($courseName, 0, 4))) . '-' . rand(10, 99);
            }

            // Ensure backward-compatibility bridge with class_types
            $classType = ClassType::where('branch_id', $branchId)
                ->where('session_id', $sessionId)
                ->where('name', $courseName)
                ->first();

            if (!$classType) {
                $maxOrder = (int) ClassType::where('branch_id', $branchId)->where('session_id', $sessionId)->max('orderBy');
                $classType = ClassType::create([
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'branch_id' => $branchId,
                    'name' => $courseName,
                    'orderBy' => $maxOrder + 1,
                ]);
            }

            $maxCourseOrder = (int) Course::where('branch_id', $branchId)->where('session_id', $sessionId)->max('orderBy');

            $course = Course::create([
                'branch_id' => $branchId,
                'session_id' => $sessionId,
                'user_id' => $userId,
                'name' => $courseName,
                'code' => $code,
                'description' => $request->description,
                'class_type_id' => $classType->id,
                'orderBy' => $request->orderBy ?? ($maxCourseOrder + 1),
                'status' => $request->status ?? 1,
            ]);

            // Auto create default Morning and Evening batches for instant readiness
            Batch::create([
                'course_id' => $course->id,
                'branch_id' => $branchId,
                'session_id' => $sessionId,
                'user_id' => $userId,
                'name' => 'Batch 1 (Morning)',
                'shift' => 'Morning',
                'start_time' => '08:00 AM',
                'end_time' => '12:00 PM',
                'capacity' => 60,
                'class_type_id' => $classType->id,
                'status' => 1,
            ]);

            Batch::create([
                'course_id' => $course->id,
                'branch_id' => $branchId,
                'session_id' => $sessionId,
                'user_id' => $userId,
                'name' => 'Batch 2 (Evening)',
                'shift' => 'Evening',
                'start_time' => '03:00 PM',
                'end_time' => '07:00 PM',
                'capacity' => 60,
                'class_type_id' => $classType->id,
                'status' => 1,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Course added successfully with default Morning & Evening batches.',
                    'course' => $course
                ]);
            }

            return redirect('courses')->with('message', 'Course added successfully.');
        }

        // Fetch courses list with relationships
        $coursesQuery = Course::with(['batches', 'courseSubjects', 'classType'])
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId);

        $search = trim((string)$request->search);
        if (!empty($search)) {
            $coursesQuery->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $allCourses = $coursesQuery->orderBy('orderBy', 'ASC')->orderBy('id', 'ASC')->get();

        // Optimized student counts per course
        $courseIds = $allCourses->pluck('id')->all();
        $classTypeIds = $allCourses->pluck('class_type_id')->filter()->all();

        $studentsByCourse = empty($courseIds) ? [] : DB::table('admissions')
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereNull('deleted_at')
            ->whereIn('course_id', $courseIds)
            ->select('course_id', DB::raw('count(*) as count'))
            ->groupBy('course_id')
            ->pluck('count', 'course_id')
            ->all();

        // Also check if students are mapped via class_type_id
        $studentsByClass = empty($classTypeIds) ? [] : DB::table('admissions')
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereNull('deleted_at')
            ->whereIn('class_type_id', $classTypeIds)
            ->select('class_type_id', DB::raw('count(*) as count'))
            ->groupBy('class_type_id')
            ->pluck('count', 'class_type_id')
            ->all();

        $totalBatches = Batch::where('branch_id', $branchId)->where('session_id', $sessionId)->count();
        $totalSubjectsAssigned = CourseSubject::where('branch_id', $branchId)->where('session_id', $sessionId)->count();

        $kpis = [
            'total_courses' => $allCourses->count(),
            'total_batches' => $totalBatches,
            'total_students' => array_sum($studentsByCourse) + array_sum($studentsByClass),
            'total_subjects_assigned' => $totalSubjectsAssigned,
        ];

        return view('master.course.index', [
            'courses' => $allCourses,
            'kpis' => $kpis,
            'studentsByCourse' => $studentsByCourse,
            'studentsByClass' => $studentsByClass,
            'search' => $search,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:191',
            ]);

            $course->name = trim($request->name);
            $course->code = trim((string)$request->code);
            $course->description = $request->description;
            $course->orderBy = $request->orderBy ?? $course->orderBy;
            $course->status = $request->status ?? 1;
            $course->save();

            // Sync with class_types bridge
            if ($course->class_type_id) {
                ClassType::where('id', $course->class_type_id)->update([
                    'name' => $course->name,
                    'orderBy' => $course->orderBy,
                ]);
            }

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Course updated successfully.'
                ]);
            }

            return redirect('courses')->with('message', 'Course updated successfully.');
        }

        return response()->json([
            'status' => 'success',
            'course' => $course
        ]);
    }

    public function delete(Request $request)
    {
        $courseId = $request->id ?? $request->delete_id;
        $course = Course::find($courseId);

        if (!$course) {
            return response()->json(['status' => 'error', 'message' => 'Course not found.'], 404);
        }

        // Check if students enrolled
        $enrolledCount = Admission::where('course_id', $course->id)->count();
        if ($enrolledCount > 0) {
            return response()->json([
                'status' => 'error',
                'message' => "Cannot delete course! {$enrolledCount} students are currently enrolled in this course."
            ], 422);
        }

        // Soft delete associated batches and course subjects
        Batch::where('course_id', $course->id)->delete();
        CourseSubject::where('course_id', $course->id)->delete();
        $course->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Course deleted successfully.'
        ]);
    }

    public function getSubjectsByCourse($course_id)
    {
        $course = Course::find($course_id);
        if (!$course) {
            return response()->json([]);
        }

        $subjects = CourseSubject::where('course_id', $course_id)
            ->orderBy('sort_by', 'ASC')
            ->orderBy('id', 'ASC')
            ->get(['id', 'subject_id', 'subject_name as name', 'sort_by']);

        // Fallback: If no course_subjects mapped yet, check subject table for mapped class_type_id
        if ($subjects->isEmpty() && $course->class_type_id) {
            $classSubjects = Subject::where('class_type_id', $course->class_type_id)->get(['id', 'name']);
            return response()->json($classSubjects);
        }

        return response()->json($subjects);
    }
}
