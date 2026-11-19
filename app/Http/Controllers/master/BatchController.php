<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Admission;
use App\Models\Sessions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        return $this->add($request);
    }

    public function add(Request $request)
    {
        $branchId = Session::get('branch_id') ?? 1;
        $currentSessionId = Session::get('session_id') ?? 3;
        $userId = Session::get('id') ?? 1;

        if ($request->isMethod('post')) {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'name' => 'required|string|max:191',
                'shift' => 'required|string|max:50',
            ]);

            $course = Course::findOrFail($request->course_id);

            // Determine Financial Year & Session ID
            $targetSessionId = $request->session_id ? (int)$request->session_id : $currentSessionId;
            $sessionObj = Sessions::find($targetSessionId);
            $financialYear = $sessionObj ? ($sessionObj->from_year . '-' . $sessionObj->to_year) : ($request->financial_year ?? '2026-27');

            $batch = Batch::create([
                'course_id' => $course->id,
                'branch_id' => $branchId,
                'session_id' => $targetSessionId,
                'financial_year' => $financialYear,
                'user_id' => $userId,
                'name' => trim($request->name),
                'shift' => trim($request->shift),
                'start_time' => trim((string)$request->start_time),
                'end_time' => trim((string)$request->end_time),
                'capacity' => $request->capacity ? (int)$request->capacity : 60,
                'room_no' => trim((string)$request->room_no),
                'class_type_id' => $course->class_type_id,
                'orderBy' => $request->orderBy ?? 0,
                'status' => $request->status ?? 1,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Batch created successfully for Financial Year ' . $financialYear . '.',
                    'batch' => $batch
                ]);
            }

            return redirect('batches?session_id=' . $targetSessionId)->with('message', 'Batch created successfully for Financial Year ' . $financialYear . '.');
        }

        // Active Sessions (Financial Years)
        $allSessions = Sessions::whereNull('deleted_at')->orderBy('from_year', 'DESC')->get();

        // Financial Year / Session Filter
        $selectedSessionId = $request->has('session_id') ? $request->session_id : (string)$currentSessionId;
        $selectedFinancialYear = $request->financial_year ?? '';

        $batchesQuery = Batch::with(['course', 'session'])
            ->where('branch_id', $branchId);

        if ($selectedSessionId !== 'all' && !empty($selectedSessionId)) {
            $batchesQuery->where('session_id', $selectedSessionId);
        }

        if (!empty($selectedFinancialYear) && $selectedFinancialYear !== 'all') {
            $batchesQuery->where('financial_year', $selectedFinancialYear);
        }

        $selectedCourseId = $request->course_id ?? '';
        if (!empty($selectedCourseId)) {
            $batchesQuery->where('course_id', $selectedCourseId);
        }

        $selectedShift = $request->shift ?? '';
        if (!empty($selectedShift)) {
            $batchesQuery->where('shift', $selectedShift);
        }

        $search = trim((string)$request->search);
        if (!empty($search)) {
            $batchesQuery->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('shift', 'LIKE', "%{$search}%")
                  ->orWhere('room_no', 'LIKE', "%{$search}%")
                  ->orWhere('financial_year', 'LIKE', "%{$search}%")
                  ->orWhereHas('course', function ($cq) use ($search) {
                      $cq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $allBatches = $batchesQuery->orderBy('financial_year', 'DESC')
            ->orderBy('course_id', 'ASC')
            ->orderBy('shift', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        // Courses for dropdown
        $coursesQuery = Course::where('branch_id', $branchId);
        if ($selectedSessionId !== 'all' && !empty($selectedSessionId)) {
            $coursesQuery->where('session_id', $selectedSessionId);
        }
        $allCourses = $coursesQuery->orderBy('name', 'ASC')->get();
        if ($allCourses->isEmpty()) {
            $allCourses = Course::where('branch_id', $branchId)->orderBy('name', 'ASC')->get()->unique('name');
        }

        // Enrolled count per batch
        $batchIds = $allBatches->pluck('id')->all();
        $studentsByBatch = empty($batchIds) ? [] : DB::table('admissions')
            ->where('branch_id', $branchId)
            ->whereNull('deleted_at')
            ->whereIn('batch_id', $batchIds)
            ->select('batch_id', DB::raw('count(*) as count'))
            ->groupBy('batch_id')
            ->pluck('count', 'batch_id')
            ->all();

        $kpis = [
            'total_batches' => $allBatches->count(),
            'morning_batches' => $allBatches->where('shift', 'Morning')->count(),
            'evening_batches' => $allBatches->where('shift', 'Evening')->count(),
            'afternoon_batches' => $allBatches->where('shift', 'Afternoon')->count(),
            'total_students' => array_sum($studentsByBatch),
        ];

        return view('master.batch.index', [
            'batches' => $allBatches,
            'courses' => $allCourses,
            'sessions' => $allSessions,
            'kpis' => $kpis,
            'studentsByBatch' => $studentsByBatch,
            'selectedSessionId' => $selectedSessionId,
            'selectedFinancialYear' => $selectedFinancialYear,
            'selectedCourseId' => $selectedCourseId,
            'selectedShift' => $selectedShift,
            'search' => $search,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $batch = Batch::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'name' => 'required|string|max:191',
                'shift' => 'required|string|max:50',
            ]);

            $course = Course::findOrFail($request->course_id);

            // Update session / financial year if provided
            if ($request->filled('session_id')) {
                $targetSessionId = (int)$request->session_id;
                $sessionObj = Sessions::find($targetSessionId);
                $financialYear = $sessionObj ? ($sessionObj->from_year . '-' . $sessionObj->to_year) : ($request->financial_year ?? $batch->financial_year);
                $batch->session_id = $targetSessionId;
                $batch->financial_year = $financialYear;
            } elseif ($request->filled('financial_year')) {
                $batch->financial_year = trim($request->financial_year);
            }

            $batch->course_id = $course->id;
            $batch->name = trim($request->name);
            $batch->shift = trim($request->shift);
            $batch->start_time = trim((string)$request->start_time);
            $batch->end_time = trim((string)$request->end_time);
            $batch->capacity = $request->capacity ? (int)$request->capacity : 60;
            $batch->room_no = trim((string)$request->room_no);
            $batch->class_type_id = $course->class_type_id;
            $batch->status = $request->status ?? 1;
            $batch->save();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Batch updated successfully.'
                ]);
            }

            return redirect('batches')->with('message', 'Batch updated successfully.');
        }

        return response()->json([
            'status' => 'success',
            'batch' => $batch
        ]);
    }

    public function delete(Request $request)
    {
        $batchId = $request->id ?? $request->delete_id;
        $batch = Batch::find($batchId);

        if (!$batch) {
            return response()->json(['status' => 'error', 'message' => 'Batch not found.'], 404);
        }

        $enrolledCount = Admission::where('batch_id', $batch->id)->count();
        if ($enrolledCount > 0) {
            return response()->json([
                'status' => 'error',
                'message' => "Cannot delete batch! {$enrolledCount} students are currently enrolled in this batch."
            ], 422);
        }

        $batch->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Batch deleted successfully.'
        ]);
    }

    public function getBatchesByCourse(Request $request, $course_id)
    {
        $course = Course::find($course_id);
        if (!$course) {
            return response()->json([]);
        }

        $query = Batch::where('course_id', $course_id)->where('status', 1);

        if ($request->filled('session_id') && $request->session_id !== 'all') {
            $query->where('session_id', $request->session_id);
        } elseif ($request->filled('financial_year') && $request->financial_year !== 'all') {
            $query->where('financial_year', $request->financial_year);
        }

        $batches = $query->orderBy('shift', 'ASC')
            ->orderBy('orderBy', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        $result = $batches->map(function ($b) {
            return [
                'id' => $b->id,
                'name' => $b->name,
                'shift' => $b->shift,
                'start_time' => $b->start_time,
                'end_time' => $b->end_time,
                'capacity' => $b->capacity,
                'room_no' => $b->room_no,
                'session_id' => $b->session_id,
                'financial_year' => $b->financial_year_label,
                'class_type_id' => $b->class_type_id,
                'formatted_title' => $b->formatted_title,
            ];
        });

        return response()->json($result);
    }
}
