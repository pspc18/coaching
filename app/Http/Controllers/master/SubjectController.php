<?php

namespace App\Http\Controllers\master;

use App\Models\Master\AllSubjects;
use App\Models\CourseSubject;
use Session;
use DB;
use Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
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

        // Calculate assigned course count for each master subject
        $subjectCounts = CourseSubject::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->select('subject_name', DB::raw('count(distinct course_id) as course_count'))
            ->groupBy('subject_name')
            ->pluck('course_count', 'subject_name');

        $totalSubjects = $allData->count();
        $mainSubjects = $allData->where('other_subject', 0)->count();
        $otherSubjects = $allData->where('other_subject', 1)->count();
        $totalAssignments = CourseSubject::where('branch_id', $branchId)->where('session_id', $sessionId)->count();

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
        $sub = AllSubjects::find($id);
        if ($sub) {
            $sub->delete();
        }
        return redirect::to('create_subject')->with('message', 'Subject Deleted Successfully.');
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
