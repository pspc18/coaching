<?php

namespace App\Http\Controllers\master;

use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\TeacherCategory;
use App\Models\Subject;
use App\Models\Master\TimePeriods;
use App\Models\Master\AllSubjects;
use App\Models\ClassType;
use Session;
use Hash;
use Str;
use DB;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function add(Request $request)
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $classTypeId = $request->class_type_id ?? '';

        if ($request->action === 'get_class_subjects') {
            $assignedSubjects = Subject::where('session_id', $sessionId)
                ->where('branch_id', $branchId)
                ->where('class_type_id', $classTypeId)
                ->pluck('name')
                ->all();

            return response()->json([
                'status' => 'success',
                'assigned' => $assignedSubjects,
            ]);
        }

        if ($request->isMethod('post') && ($request->has('add_subject') || $request->has('class_type_id'))) {
            $request->validate([
                'class_type_id' => 'required',
            ]);

            $addSubjects = (array) ($request->add_subject ?? []);

            Subject::whereNotIn('name', $addSubjects)
                ->where('class_type_id', $request->class_type_id)
                ->where('session_id', $sessionId)
                ->where('branch_id', $branchId)
                ->delete();

            if (!empty($addSubjects)) {
                foreach ($addSubjects as $key => $item) {
                    $string = trim($item);
                    $ucfirstString = ucwords(strtolower($string), '\',. ');
                    $other_subject = AllSubjects::where('name', $ucfirstString)->first();
                    $otherVal = $other_subject ? ($other_subject->other_subject ?? 0) : 0;

                    $existing = Subject::where('name', $item)
                        ->where('class_type_id', $request->class_type_id)
                        ->where('session_id', $sessionId)
                        ->where('branch_id', $branchId)
                        ->first();

                    if (!empty($existing)) {
                        $existing->update([
                            'deleted_at' => null,
                            'other_subject' => $otherVal,
                            'sort_by' => $key + 1
                        ]);
                    } else {
                        Subject::create([
                            'user_id' => Session::get('id'),
                            'session_id' => $sessionId,
                            'branch_id' => $branchId,
                            'name' => $item,
                            'other_subject' => $otherVal,
                            'sort_by' => $key + 1,
                            'class_type_id' => $request->class_type_id,
                        ]);
                    }
                }
            }
            if ($request->ajax()) {
                return response()->json(['status' => 'success', 'message' => 'Subjects assigned to class successfully.']);
            }
            return redirect('add_subject')->with('message', 'Subjects assigned to class successfully.');
        }

        // Fetch assigned subjects query
        $assignedQuery = Subject::where('session_id', $sessionId)
            ->where('branch_id', $branchId);

        if (!empty($classTypeId) && $classTypeId !== 'all') {
            $assignedQuery->where('class_type_id', $classTypeId);
        }

        $allAssigned = $assignedQuery->orderBy('sort_by', 'ASC')->orderBy('id', 'ASC')->get();
        $classTypes = ClassType::where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->orderBy('orderBy', 'ASC')
            ->get();

        $allMasterSubjects = AllSubjects::where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->whereNull('deleted_at')
            ->orderBy('name', 'ASC')
            ->get();

        // Calculate KPI summary
        $totalAssignedCount = $allAssigned->count();
        $mainAssignedCount = $allAssigned->where('other_subject', 0)->count();
        $otherAssignedCount = $allAssigned->where('other_subject', 1)->count();
        $distinctClassesAssigned = Subject::where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->distinct('class_type_id')
            ->count('class_type_id');

        $kpis = [
            'total_assigned' => $totalAssignedCount,
            'main_assigned' => $mainAssignedCount,
            'other_assigned' => $otherAssignedCount,
            'classes_covered' => $distinctClassesAssigned,
        ];

        $search = trim((string) ($request->search ?? ''));
        $searchId = trim((string) ($request->search_id ?? ''));
        $searchName = trim((string) ($request->search_name ?? ''));
        $categoryFilter = (string) ($request->category ?? 'all');
        $page = max(1, (int) ($request->page ?? 1));
        $perPage = (int) ($request->per_page ?? 25);
        if (!in_array($perPage, [10, 25, 50, 100, -1], true)) {
            $perPage = 25;
        }

        $filteredRows = $allAssigned->filter(function ($item) use ($search, $searchId, $searchName, $categoryFilter) {
            if ($searchId !== '' && stripos((string)$item->id, $searchId) === false) {
                return false;
            }
            if ($searchName !== '' && stripos((string)$item->name, $searchName) === false) {
                return false;
            }
            if ($search !== '') {
                $nameMatch = stripos($item->name, $search) !== false;
                $idMatch = stripos((string)$item->id, $search) !== false;
                if (!$nameMatch && !$idMatch) return false;
            }
            if ($categoryFilter === 'main' && (int)$item->other_subject !== 0) return false;
            if ($categoryFilter === 'other' && (int)$item->other_subject !== 1) return false;
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
                'html' => view('master.subject.add_subject_rows', [
                    'rows' => $pageRows,
                    'classTypes' => $classTypes->keyBy('id')
                ])->render(),
                'pagination' => $pagination,
                'kpis' => $kpis
            ]);
        }

        $searchArr = [
            'class_type_id' => $classTypeId,
            'class_type_filter' => ''
        ];

        return view('master.subject.add_subject', [
            'rows' => $pageRows,
            'section' => $allAssigned,
            'search' => $searchArr,
            'classTypes' => $classTypes,
            'allMasterSubjects' => $allMasterSubjects,
            'pagination' => $pagination,
            'kpis' => $kpis,
            'searchId' => $searchId,
            'searchName' => $searchName,
            'classTypeId' => $classTypeId,
            'perPage' => $perPage,
            'categoryFilter' => $categoryFilter,
        ]);
    }

    public function selectClass(Request $request)
    {
        return $this->add($request);
    }

    public function edit(Request $request, $id)
    {
        $data = Subject::find($id);
        if ($request->isMethod('post')) {
            $request->validate([
                'subject' => 'required',
                'class_type_id' => 'required',
            ]);
            $check = Subject::where('id', '<>', $id)
                ->where('class_type_id', $request->class_type_id)
                ->where('name', $request->subject)
                ->where('branch_id', Session::get('branch_id'))
                ->where('session_id', Session::get('session_id'))
                ->get();

            if (count($check) == 0) {
                $data->session_id = Session::get('session_id');
                $data->branch_id = Session::get('branch_id');
                $data->class_type_id = $request->class_type_id;
                $data->name = $request->subject;
                $data->save();
            } else {
                return redirect::to('add_subject')->with('error', 'This subject already added for this class.');
            }
            return redirect::to('add_subject')->with('message', 'Subject Edited Successfully.');
        }
        return view('master.subject.edit_subject', ['data' => $data]);
    }

    public function createSubjects(Request $request)
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');

        if ($request->isMethod('post')) {
            $request->validate([
                'add_subject' => 'required',
            ]);
            $string = $request->add_subject;
            $ucfirstString = ucwords(strtolower(trim($string)), '\',. ');
            $olddata = AllSubjects::where('name', $ucfirstString)
                ->where('session_id', $sessionId)
                ->where('branch_id', $branchId)
                ->first();

            if ($olddata) {
                return response()->json(['error' => 'errors', 'message' => 'Subject already exists in master.'], 400);
            } else {
                $subject = new AllSubjects;
                $subject->user_id = Session::get('id');
                $subject->session_id = $sessionId;
                $subject->branch_id = $branchId;
                $subject->name = $ucfirstString;
                $subject->other_subject = $request->other_subject ?? 0;
                $subject->save();
                return response()->json(['status' => 'success', 'message' => 'Subject Created Successfully.', 'print_url' => url('/create_subject')]);
            }
        }

        $allData = AllSubjects::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->whereNull('deleted_at')
            ->orderBy('name', 'ASC')
            ->get();

        // Calculate assigned class count for each master subject
        $subjectCounts = Subject::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->select('name', DB::raw('count(distinct class_type_id) as class_count'))
            ->groupBy('name')
            ->pluck('class_count', 'name');

        $totalSubjects = $allData->count();
        $mainSubjects = $allData->where('other_subject', 0)->count();
        $otherSubjects = $allData->where('other_subject', 1)->count();
        $totalAssignments = Subject::where('branch_id', $branchId)->where('session_id', $sessionId)->count();

        $kpis = [
            'total_subjects' => $totalSubjects,
            'main_subjects' => $mainSubjects,
            'other_subjects' => $otherSubjects,
            'total_assignments' => $totalAssignments,
        ];

        $search = trim((string) ($request->search ?? ''));
        $searchId = trim((string) ($request->search_id ?? ''));
        $categoryFilter = (string) ($request->category ?? 'all');
        $page = max(1, (int) ($request->page ?? 1));
        $perPage = (int) ($request->per_page ?? 25);
        if (!in_array($perPage, [10, 25, 50, 100, -1], true)) {
            $perPage = 25;
        }

        $filteredRows = $allData->filter(function ($item) use ($search, $searchId, $categoryFilter) {
            if ($searchId !== '' && (string)$item->id !== $searchId) {
                return false;
            }
            if ($search !== '') {
                $nameMatch = stripos($item->name, $search) !== false;
                if (!$nameMatch) return false;
            }
            if ($categoryFilter === 'main' && (int)$item->other_subject !== 0) return false;
            if ($categoryFilter === 'other' && (int)$item->other_subject !== 1) return false;
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
                'html' => view('master.subject.create_subject_rows', [
                    'rows' => $pageRows,
                    'subjectCounts' => $subjectCounts
                ])->render(),
                'pagination' => $pagination,
                'kpis' => $kpis
            ]);
        }

        return view('master.subject.create_subject', [
            'rows' => $pageRows,
            'data' => $allData,
            'subjectCounts' => $subjectCounts,
            'pagination' => $pagination,
            'kpis' => $kpis,
            'search' => $search,
            'perPage' => $perPage,
            'categoryFilter' => $categoryFilter,
        ]);
    }

    public function deleteCreateSubject(Request $request)
    {
        $id = $request->delete_id;
        AllSubjects::find($id)->delete();
        return redirect::to('create_subject')->with('message', 'Subject Deleted Successfully.');
    }

    public function delete(Request $request)
    {
        $id = $request->delete_id;
        $subj = Subject::find($id);
        if ($subj) {
            $subj->delete();
        }

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Subject removed from class successfully.']);
        }
        return redirect::to('add_subject')->with('message', 'Subject Deleted Successfully.');
    }

    public function deletePeriods(Request $request)
    {
        $id = $request->delete_id;
        TimePeriods::find($id)->delete();
        return redirect::to('time_periods')->with('message', 'Period Deleted Successfully.');
    }

    public function timePeriods(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'from_time' => 'required',
                'to_time' => 'required',
                'period_name' => 'required',
            ]);
            $data = new TimePeriods;
            $data->user_id = Session::get('id');
            $data->session_id = Session::get('session_id');
            $data->branch_id = Session::get('branch_id');
            $data->from_time = $request->from_time;
            $data->to_time = $request->to_time;
            $data->period_name = $request->period_name;
            $data->save();

            return response()->json(['status' => 'success', 'message' => 'Period added Successfully.']);
        }
        $data = TimePeriods::where('branch_id', Session::get('branch_id'))->where('session_id', Session::get('session_id'))->orderBy('id', 'ASC')->get();
        return view('master.time_table.add', ['data' => $data]);
    }

    public function editTimePeriods(Request $request, $id)
    {
        $data = TimePeriods::find($id);
        if ($request->isMethod('post')) {
            $request->validate([
                'from_time' => 'required',
                'to_time' => 'required',
                'period_name' => 'required',
            ]);
            $data->user_id = Session::get('id');
            $data->session_id = Session::get('session_id');
            $data->branch_id = Session::get('branch_id');
            $data->from_time = $request->from_time;
            $data->to_time = $request->to_time;
            $data->period_name = $request->period_name;
            $data->save();
            return response()->json(['status' => 'success', 'message' => 'Period Updated Successfully.', 'redirect' => url('time_periods')]);
        }
        return view('master.time_table.edit', ['data' => $data]);
    }

    public function subjectOrderBy(Request $request)
    {
        if ($request->isMethod('post')) {
            if (!empty($request->subject_id)) {
                foreach ($request->subject_id as $key => $item) {
                    $data = Subject::find($request->subject_id[$key]);
                    if ($data) {
                        $data->sort_by = $request->sort_by[$key];
                        $data->save();
                    }
                }
            }
            return response()->json(['status' => 'success', 'message' => 'Order Updated Successfully.']);
        }
    }

    public function multiEditSubjects(Request $request)
    {
        if ($request->isMethod('post')) {
            if (!empty($request->add_subject)) {
                foreach ($request->id as $key => $item) {
                    $string = $request->add_subject[$key];
                    $ucfirstString = ucwords(strtolower(trim($string)), '\',. ');
                    $string2 = 'other_subject_' . $item;
                    $data = AllSubjects::find($item);
                    if ($data) {
                        $data->name = $ucfirstString;
                        $data->other_subject = $request->$string2 ?? 0;
                        $data->save();
                    }
                }
                return response()->json(['status' => 'success', 'message' => 'Subjects Updated Successfully.']);
            } else {
                return redirect::to('create_subject')->with('error', 'Something went wrong');
            }
        }
    }
}