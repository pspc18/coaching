<?php

namespace App\Http\Controllers\master;

use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Admin;
use App\Models\ClassType;
use App\Models\Subject;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ClassController extends Controller
{
    public function add(Request $request)
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required',
            ]);

            $orderBy = $this->getClassOrder($request->name);

            $class = new ClassType;
            $class->user_id = Session::get('id');
            $class->session_id = $sessionId;
            $class->branch_id = $branchId;
            $class->name = trim($request->name);
            $class->orderBy = $orderBy;
            $class->save();

            if ($request->ajax()) {
                return response()->json(['status' => 'success', 'message' => 'Class added successfully.']);
            }
            return redirect('add_class')->with('message', 'Class added successfully.');
        }

        $allClasses = ClassType::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->orderBy('orderBy', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        // Optimized batch queries for admissions, fees, and subjects counts to avoid N+1
        $classIds = $allClasses->pluck('id')->all();

        $admissionsCounts = empty($classIds) ? [] : DB::table('admissions')
            ->whereIn('class_type_id', $classIds)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereNull('deleted_at')
            ->select('class_type_id', DB::raw('count(*) as count'))
            ->groupBy('class_type_id')
            ->pluck('count', 'class_type_id')
            ->all();

        $feesCounts = empty($classIds) ? [] : DB::table('fees_master')
            ->whereIn('class_type_id', $classIds)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereNull('deleted_at')
            ->select('class_type_id', DB::raw('count(*) as count'))
            ->groupBy('class_type_id')
            ->pluck('count', 'class_type_id')
            ->all();

        $subjectCounts = empty($classIds) ? [] : Subject::whereIn('class_type_id', $classIds)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereNull('deleted_at')
            ->select('class_type_id', DB::raw('count(*) as count'))
            ->groupBy('class_type_id')
            ->pluck('count', 'class_type_id')
            ->all();

        $totalClasses = $allClasses->count();
        $totalStudents = array_sum($admissionsCounts);
        $totalFeesLinked = count(array_filter($feesCounts, fn($c) => $c > 0));
        $totalSubjectsAssigned = array_sum($subjectCounts);

        $kpis = [
            'total_classes' => $totalClasses,
            'total_students' => $totalStudents,
            'fees_linked' => $totalFeesLinked,
            'subjects_assigned' => $totalSubjectsAssigned,
        ];

        $search = trim((string) ($request->search ?? ''));
        $searchId = trim((string) ($request->search_id ?? ''));
        $searchName = trim((string) ($request->search_name ?? ''));
        $searchOrder = trim((string) ($request->search_order ?? ''));
        $page = max(1, (int) ($request->page ?? 1));
        $perPage = (int) ($request->per_page ?? 25);
        if (!in_array($perPage, [10, 25, 50, 100, -1], true)) {
            $perPage = 25;
        }

        $filteredRows = $allClasses->filter(function ($item) use ($search, $searchId, $searchName, $searchOrder) {
            if ($searchId !== '' && stripos((string)$item->id, $searchId) === false) {
                return false;
            }
            if ($searchName !== '' && stripos((string)$item->name, $searchName) === false) {
                return false;
            }
            if ($searchOrder !== '' && stripos((string)$item->orderBy, $searchOrder) === false) {
                return false;
            }
            if ($search !== '') {
                $nameMatch = stripos($item->name, $search) !== false;
                $orderMatch = stripos((string)$item->orderBy, $search) !== false;
                $idMatch = stripos((string)$item->id, $search) !== false;
                if (!$nameMatch && !$orderMatch && !$idMatch) return false;
            }
            return true;
        })->values();

        $totalFiltered = $filteredRows->count();
        $totalPages = ($perPage === -1 || $totalFiltered === 0) ? 1 : (int) ceil($totalFiltered / $perPage);
        $page = min($page, max(1, $totalPages));
        $pageRows = ($perPage === -1) ? $filteredRows : $filteredRows->slice(($page - 1) * $perPage, $perPage)->values();
        $fromRecord = $totalFiltered > 0 ? (($page - 1) * ($perPage === -1 ? $totalFiltered : $perPage) + 1) : 0;
        $toRecord = $totalFiltered > 0 ? min($page * ($perPage === -1 ? $totalFiltered : $perPage), $totalFiltered) : 0;

        $pagination = [
            'current_page' => $page,
            'per_page' => $perPage,
            'total_records' => $totalFiltered,
            'total_pages' => $totalPages,
            'from' => $fromRecord,
            'to' => $toRecord,
        ];

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'html' => view('master.class.class_rows', [
                    'rows' => $pageRows,
                    'admissionsCounts' => $admissionsCounts,
                    'feesCounts' => $feesCounts,
                    'subjectCounts' => $subjectCounts,
                ])->render(),
                'pagination' => $pagination,
                'kpis' => $kpis
            ]);
        }

        return view('master.class.add', [
            'rows' => $pageRows,
            'data' => $allClasses,
            'admissionsCounts' => $admissionsCounts,
            'feesCounts' => $feesCounts,
            'subjectCounts' => $subjectCounts,
            'pagination' => $pagination,
            'kpis' => $kpis,
            'search' => $search,
            'searchId' => $searchId,
            'searchName' => $searchName,
            'searchOrder' => $searchOrder,
            'perPage' => $perPage,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $data = ClassType::find($id);
        if (!$data) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Class not found.'], 404);
            }
            return redirect('add_class')->with('error', 'Class not found.');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required',
            ]);
            $data->session_id = Session::get('session_id');
            $data->branch_id = Session::get('branch_id');
            $data->name = trim($request->name);
            $data->orderBy = $this->getClassOrder($request->name);
            $data->save();

            if ($request->ajax()) {
                return response()->json(['status' => 'success', 'message' => 'Class Updated Successfully.', 'redirect' => url('add_class')]);
            }
            return redirect('add_class')->with('message', 'Class Updated Successfully.');
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $data->id,
                    'name' => $data->name,
                    'orderBy' => $data->orderBy,
                ]
            ]);
        }

        return view('master.class.edit', ['data' => $data]);
    }

    public function delete(Request $request)
    {
        $id = $request->delete_id;
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');

        $hasAdmissions = DB::table('admissions')
            ->where('class_type_id', $id)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereNull('deleted_at')
            ->exists();

        $hasFees = DB::table('fees_master')
            ->where('class_type_id', $id)
            ->where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereNull('deleted_at')
            ->exists();

        if ($hasAdmissions || $hasFees) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Cannot delete class with existing student admissions or fees structures.']);
            }
            return redirect::to('add_class')->with('error', 'Cannot delete class with existing student admissions or fees structures.');
        }

        $class = ClassType::find($id);
        if ($class) {
            $class->delete();
        }

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Class Deleted Successfully.']);
        }

        return redirect::to('add_class')->with('message', 'Class Deleted Successfully.');
    }

    public function saveSelectedClasses(Request $request)
    {
        $class_ids = $request->input('class_id', []);
        $class_names = $request->input('class', []);

        if (!empty($class_ids)) {
            foreach ($class_ids as $class_id) {
                $class_name = trim($class_names[$class_id] ?? '');
                if ($class_name !== '') {
                    $orderBy = $this->getClassOrder($class_name);

                    $class = new ClassType;
                    $class->user_id = Session::get('id');
                    $class->session_id = Session::get('session_id');
                    $class->branch_id = Session::get('branch_id');
                    $class->name = $class_name;
                    $class->orderBy = $orderBy;
                    $class->save();
                }
            }
        }

        return redirect::to('add_class')->with('message', 'Selected classes added successfully.');
    }

    private function getClassOrder(string $className): int
    {
        $orderBy = 0;
        $arrayOfStrings = [
            'play', 'kg', 'nursery', 'lkg', 'ukg', 'prep',
            'first', '1',
            'second', '2',
            'third', '3',
            'fourth', '4',
            'fifth', '5',
            'sixth', '6',
            'seventh', '7',
            'eighth', '8',
            'ninth', '9',
            'tenth', '10',
            'eleventh', '11',
            'twelfth', '12',
        ];
        $number = [
            0, 0, 0, 0, 0, 0,
            1, 1, 2, 2, 3, 3,
            4, 4, 5, 5, 6, 6, 7, 7, 8, 8, 9, 9, 10, 10, 11, 11, 12, 12
        ];
        $stringToCheck = strtolower($className);
        foreach ($arrayOfStrings as $key => $string) {
            if (strpos($stringToCheck, $string) !== false) {
                $orderBy = (int) $number[$key];
            }
        }
        return $orderBy;
    }
}