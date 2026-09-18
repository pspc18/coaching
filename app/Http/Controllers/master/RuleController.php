<?php

namespace App\Http\Controllers\master;

use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\Rule;
use App\Models\Master\Role;
use App\Models\Master\SchoolDesk;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    public function add(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:200',
                'role_id' => 'required',
            ]);

            $holiday = new Rule;
            $holiday->name = trim($request->name);
            $holiday->role_id = $request->role_id;
            $holiday->description = $request->description;
            $holiday->save();

            if ($request->ajax()) {
                return response()->json(['status' => 'success', 'message' => 'Rule added successfully.']);
            }
            return redirect('rules_add')->with('message', 'Rule added successfully.');
        }

        $allRules = Rule::select('rules.*', 'role.name as role_name')
            ->leftJoin('role', 'role.id', '=', 'rules.role_id')
            ->orderBy('rules.id', 'DESC')
            ->get();

        $allRoles = Role::whereNull('deleted_at')->orderBy('id', 'ASC')->get();

        $totalRules = $allRules->count();
        $distinctRoles = $allRules->pluck('role_id')->unique()->filter()->count();
        $totalRolesCount = $allRoles->count();

        $kpis = [
            'total_rules' => $totalRules,
            'roles_covered' => $distinctRoles,
            'total_roles' => $totalRolesCount,
            'active_guidelines' => $totalRules,
        ];

        $search = trim((string) ($request->search ?? ''));
        $searchId = trim((string) ($request->search_id ?? ''));
        $searchName = trim((string) ($request->search_name ?? ''));
        $roleFilter = (string) ($request->role_id ?? 'all');
        $searchDesc = trim((string) ($request->search_desc ?? ''));
        $page = max(1, (int) ($request->page ?? 1));
        $perPage = (int) ($request->per_page ?? 25);
        if (!in_array($perPage, [10, 25, 50, 100, -1], true)) {
            $perPage = 25;
        }

        $filteredRows = $allRules->filter(function ($item) use ($search, $searchId, $searchName, $roleFilter, $searchDesc) {
            if ($searchId !== '' && stripos((string)$item->id, $searchId) === false) {
                return false;
            }
            if ($searchName !== '' && stripos((string)$item->name, $searchName) === false) {
                return false;
            }
            if ($searchDesc !== '' && stripos(strip_tags((string)$item->description), $searchDesc) === false) {
                return false;
            }
            if ($search !== '') {
                $nameMatch = stripos($item->name, $search) !== false;
                $roleMatch = stripos((string)$item->role_name, $search) !== false;
                $descMatch = stripos(strip_tags((string)$item->description), $search) !== false;
                $idMatch = stripos((string)$item->id, $search) !== false;
                if (!$nameMatch && !$roleMatch && !$descMatch && !$idMatch) return false;
            }
            if ($roleFilter !== 'all' && (string)$item->role_id !== $roleFilter) {
                return false;
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
                'html' => view('master.Rule.rule_rows', ['rows' => $pageRows])->render(),
                'pagination' => $pagination,
                'kpis' => $kpis
            ]);
        }

        return view('master.Rule.add', [
            'rows' => $pageRows,
            'data' => $allRules,
            'allRoles' => $allRoles,
            'pagination' => $pagination,
            'kpis' => $kpis,
            'search' => $search,
            'searchId' => $searchId,
            'searchName' => $searchName,
            'searchDesc' => $searchDesc,
            'perPage' => $perPage,
            'roleFilter' => $roleFilter,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $data = Rule::find($id);
        if (!$data) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Rule not found.'], 404);
            }
            return redirect('rules_add')->with('error', 'Rule not found.');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:200',
                'role_id' => 'required',
            ]);
            $data->name = trim($request->name);
            $data->role_id = $request->role_id;
            $data->description = $request->description;
            $data->save();

            if ($request->ajax()) {
                return response()->json(['status' => 'success', 'message' => 'Rule updated successfully.']);
            }
            return redirect('rules_add')->with('message', 'Rule updated successfully.');
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $data->id,
                    'name' => $data->name,
                    'role_id' => $data->role_id,
                    'description' => $data->description,
                ]
            ]);
        }

        return view('master.Rule.edit', ['data' => $data]);
    }

    public function delete(Request $request)
    {
        $id = $request->delete_id;
        $rule = Rule::find($id);
        if ($rule) {
            $rule->delete();
        }

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Rule deleted successfully.']);
        }

        return redirect::to('rules_add')->with('message', 'Rule Deleted Successfully.');
    }

    public function schoolDeskEdit(Request $request)
    {
        $old = SchoolDesk::first();
        if ($request->isMethod('post')) {
            $request->validate([
                'description' => 'required',
            ]);
            if (empty($old)) {
                $data = new SchoolDesk;
            } else {
                $data = SchoolDesk::find(1);
            }
            $data->user_id = Session::get('id');
            $data->session_id = Session::get('session_id');
            $data->branch_id = Session::get('branch_id');
            $data->description = $request->description;
            $data->save();
            return response()->json(['status' => 'success', 'message' => 'School Desk updated Successfully.', 'redirect' => url('school_desk')]);
        }
        return view('master.schoolDesk.school_desk', ['data' => $old]);
    }

    public function schoolDeskView(Request $request)
    {
        $data = SchoolDesk::where('id', 1)->first();
        return view('master.schoolDesk.school_desk_view', ['data' => $data]);
    }
}