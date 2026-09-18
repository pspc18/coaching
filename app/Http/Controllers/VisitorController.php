<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\ClassType;
use App\Models\Setting;
use App\Models\FeesMaster;
use App\Models\Enquiry;
use App\Models\Visitor;
use App\Models\Admission;
use App\Models\StudentAction;
use App\Models\Classs;
use App\Models\BillCounter;
use App\Models\SmsSetting;
use App\Models\WhatsappSetting;
use App\Models\Teacher;
use App\Models\State;
use App\Models\Remark;
use App\Models\City;
use App\Models\Sessions;
use App\Models\Master\MessageTemplate;
use App\Models\fees\FeesAssign;
use App\Models\fees\FeesAssignDetail;
use App\Models\Master\SchoolDesk;
use App\Models\Master\MessageType;
use App\Models\Master\Branch;
use Session;
use Hash;
use Helper;
use QrCode;
use Response;
use Str;
use PDF;
use Mail;
use DB;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VisitorController extends Controller

{
    public function studentsDashboard()
    {

        return view('students/studentsDashboard');
    }
 

    public static function clearCache($branchId = null, $sessionId = null)
    {
        $branchId = $branchId ?: Session::get('branch_id');
        $sessionId = $sessionId ?: Session::get('session_id');
        if ($branchId && $sessionId) {
            $verKey = "visitors_ver_{$branchId}_{$sessionId}";
            $cur = (int) \Illuminate\Support\Facades\Cache::get($verKey, 1);
            \Illuminate\Support\Facades\Cache::put($verKey, $cur + 1, 86400 * 30);
        }
    }

    public function visitorAdd(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'visitor_name' => 'required',
                'visitor_mobile' => 'required',
                'date' => 'required',
            ]);

            $addstudents = new Visitor;
            $addstudents->user_id = Session::get('id');
            $addstudents->session_id = Session::get('session_id');
            $addstudents->branch_id = Session::get('branch_id');
            $addstudents->visitor_name = $request->visitor_name;
            $addstudents->visitor_mobile = $request->visitor_mobile;
            $addstudents->stu_name = $request->stu_name;
            $addstudents->id_name  = $request->id_name;
            $addstudents->aadharNo  = $request->aadharNo;
            $addstudents->class_type_id = $request->class_type_id;
            $addstudents->date = $request->date;
            $addstudents->remark = $request->remark;
            $addstudents->save();

            self::clearCache(Session::get('branch_id'), Session::get('session_id'));

            return redirect::to('visitorView')->with('message', 'Visitor added successfully.');
        }

        return view('visitor.add');
    }

    public function visitorView(Request $request)
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $roleId = (int) Session::get('role_id');
        $adminBranchId = Session::get('admin_branch_id');

        if ((int) $request->input('refresh', 0) === 1) {
            self::clearCache($branchId, $sessionId);
            return redirect('visitorView')->with('message', 'Visitor cache refreshed successfully.');
        }

        $verKey = "visitors_ver_{$branchId}_{$sessionId}";
        $ver = \Illuminate\Support\Facades\Cache::get($verKey, 1);

        $countsKey = "visitors_counts_{$branchId}_{$sessionId}_v{$ver}";
        $counts = \Illuminate\Support\Facades\Cache::remember($countsKey, 600, function () use ($sessionId, $branchId, $roleId, $adminBranchId) {
            $baseQuery = Visitor::whereNull('deleted_at');
            if ($roleId > 1) {
                $baseQuery->where('branch_id', $branchId);
            }
            if (!empty($adminBranchId)) {
                $baseQuery->where('branch_id', $adminBranchId);
            }
            if (!empty($sessionId)) {
                $baseQuery->where('session_id', $sessionId);
            }

            $today = date('Y-m-d');
            $startOfMonth = date('Y-m-01');
            $endOfMonth = date('Y-m-t');

            return [
                'all' => (clone $baseQuery)->count(),
                'today' => (clone $baseQuery)->whereDate('date', $today)->count(),
                'month' => (clone $baseQuery)->whereBetween('date', [$startOfMonth, $endOfMonth])->count(),
            ];
        });

        $searchWord = trim((string) $request->input('name', $request->input('search', '')));
        $classTypeId = $request->input('class_type_id', '');
        $date = $request->input('date', '');
        $fromDate = $request->input('from_date', '');
        $toDate = $request->input('to_date', '');
        $idName = trim((string) $request->input('id_name', ''));
        $studentName = trim((string) $request->input('stu_name', ''));
        $remark = trim((string) $request->input('remark', ''));
        $perPage = $request->input('per_page', 25);
        $page = max(1, (int) $request->input('page', 1));

        $query = Visitor::with(['classType', 'user'])->whereNull('deleted_at');

        if ($roleId > 1) {
            $query->where('visitors.branch_id', $branchId);
        }
        if (!empty($adminBranchId)) {
            $query->where('visitors.branch_id', $adminBranchId);
        }
        if (!empty($sessionId)) {
            $query->where('visitors.session_id', $sessionId);
        }

        if (!empty($searchWord)) {
            $query->where(function ($q) use ($searchWord) {
                $q->where('visitors.visitor_name', 'LIKE', "%{$searchWord}%")
                  ->orWhere('visitors.visitor_mobile', 'LIKE', "%{$searchWord}%")
                  ->orWhere('visitors.stu_name', 'LIKE', "%{$searchWord}%")
                  ->orWhere('visitors.id_name', 'LIKE', "%{$searchWord}%")
                  ->orWhere('visitors.aadharNo', 'LIKE', "%{$searchWord}%")
                  ->orWhere('visitors.remark', 'LIKE', "%{$searchWord}%");
            });
        }

        if (!empty($classTypeId)) {
            $query->where('visitors.class_type_id', $classTypeId);
        }

        if (!empty($date)) {
            $query->whereDate('visitors.date', $date);
        }

        if (!empty($fromDate)) {
            $query->whereDate('visitors.date', '>=', $fromDate);
        }

        if (!empty($toDate)) {
            $query->whereDate('visitors.date', '<=', $toDate);
        }

        if (!empty($idName)) {
            $query->where(function ($q) use ($idName) {
                $q->where('visitors.id_name', 'LIKE', "%{$idName}%")
                  ->orWhere('visitors.aadharNo', 'LIKE', "%{$idName}%");
            });
        }

        if (!empty($studentName)) {
            $query->where('visitors.stu_name', 'LIKE', "%{$studentName}%");
        }

        if (!empty($remark)) {
            $query->where('visitors.remark', 'LIKE', "%{$remark}%");
        }

        $totalCount = $query->count();

        if ($perPage === 'all' || (int) $perPage <= 0) {
            $perPageVal = $totalCount > 0 ? $totalCount : 25;
            $lastPage = 1;
            $currentPage = 1;
            $startIndex = 0;
            $visitors = $query->orderBy('visitors.date', 'DESC')->orderBy('visitors.id', 'DESC')->get();
        } else {
            $perPageVal = max(1, (int) $perPage);
            $lastPage = max(1, (int) ceil($totalCount / $perPageVal));
            $currentPage = min($page, $lastPage);
            $startIndex = ($currentPage - 1) * $perPageVal;
            $visitors = $query->orderBy('visitors.date', 'DESC')->orderBy('visitors.id', 'DESC')->skip($startIndex)->take($perPageVal)->get();
        }

        $classType = Helper::classType();

        if ($request->ajax() || (int) $request->input('ajax', 0) === 1) {
            $html = view('visitor.table_rows', [
                'data' => $visitors,
                'visitors' => $visitors,
                'startIndex' => $startIndex,
            ])->render();

            return response()->json([
                'status' => true,
                'html' => $html,
                'current_page' => $currentPage,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total_count' => $totalCount,
                'start_index' => $startIndex,
                'counts' => $counts,
            ]);
        }

        return view('visitor.view', [
            'data' => $visitors,
            'visitors' => $visitors,
            'counts' => $counts,
            'totalCount' => $totalCount,
            'startIndex' => $startIndex,
            'currentPage' => $currentPage,
            'lastPage' => $lastPage,
            'perPage' => $perPage,
            'classType' => $classType,
            'search' => [
                'name' => $searchWord,
                'class_type_id' => $classTypeId,
                'date' => $date,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'id_name' => $idName,
                'stu_name' => $studentName,
                'remark' => $remark,
            ],
        ]);
    }

    public function visitorEdit(Request $request, $id)
    {
        $data = Visitor::find($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'visitor_name' => 'required',
                'visitor_mobile' => 'required',
                'date' => 'required',
            ]);

            $data->user_id = Session::get('id');
            $data->session_id = Session::get('session_id');
            $data->branch_id = Session::get('branch_id');
            $data->visitor_name = $request->visitor_name;
            $data->visitor_mobile = $request->visitor_mobile;
            $data->stu_name = $request->stu_name;
            $data->id_name  = $request->id_name;
            $data->aadharNo  = $request->aadharNo;
            $data->class_type_id = $request->class_type_id;
            $data->date = $request->date;
            $data->remark = $request->remark;
            $data->save();

            self::clearCache(Session::get('branch_id'), Session::get('session_id'));

            return redirect::to('visitorView')->with('message', 'Visitor updated successfully.');
        }

        return view('visitor.edit', ['data' => $data]);
    }

    public function visitorDelete(Request $request)
    {
        if (!empty($request->delete_id)) {
            $data = Visitor::find($request->delete_id);
            if ($data) {
                $data->delete();
                self::clearCache(Session::get('branch_id'), Session::get('session_id'));
            }
        }
        return redirect::to('visitorView')->with('message', 'Visitor deleted successfully!');
    }




    public function student_action_index()
    {
        $allstudent_action =  StudentAction::where('session_id', Session::get('session_id'))
            ->where('branch_id', Session::get('branch_id'))->orderBy('id', 'DESC')->get();
        return view('students.student_action.student_action_index', ['data' => $allstudent_action]);
    }


    public function class_type_search(Request $request)
    {
        if (!empty($request->class_type_id)) {
            $data = array();

            $data = Classs::where('class_id', $request->class_type_id)->get();
            $stateData = '';
            foreach ($data as $class) {
                $stateData .= '
           <option value="' . $class->id . '">' . $class->name . '</option>';
            }
            echo $stateData;
        }
    }


 
 
}
