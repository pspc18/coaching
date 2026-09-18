<?php

namespace App\Http\Controllers\master;

use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\Role;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class RoleController extends Controller
{
    public function add(Request $request)
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');

        if ($request->isMethod('post')) {
            $request->validate([
                'role' => 'required|string|max:100',
            ]);

            $class = new Role;
            $class->user_id = Session::get('id');
            $class->session_id = $sessionId;
            $class->branch_id = $branchId;
            $class->name = trim($request->role);
            $class->save();

            return response()->json(['status' => 'success', 'message' => 'Role added successfully.']);
        }

        $allRoles = Role::whereNull('deleted_at')->orderBy('id', 'ASC')->get();

        // Calculate assigned staff count per role
        $userCounts = User::whereNull('deleted_at')
            ->select('role_id', DB::raw('count(*) as user_count'))
            ->groupBy('role_id')
            ->pluck('user_count', 'role_id');

        $totalRoles = $allRoles->count();
        $systemRoles = $allRoles->where('id', '<=', 5)->count();
        $customRoles = $allRoles->where('id', '>', 5)->count();
        $totalStaffWithRoles = User::whereNull('deleted_at')->count();

        $kpis = [
            'total_roles' => $totalRoles,
            'system_roles' => $systemRoles,
            'custom_roles' => $customRoles,
            'staff_with_roles' => $totalStaffWithRoles,
        ];

        $search = trim((string) ($request->search ?? ''));
        $searchId = trim((string) ($request->search_id ?? ''));
        $typeFilter = (string) ($request->type ?? 'all');
        $page = max(1, (int) ($request->page ?? 1));
        $perPage = (int) ($request->per_page ?? 25);
        if (!in_array($perPage, [10, 25, 50, 100, -1], true)) {
            $perPage = 25;
        }

        $filteredRows = $allRoles->filter(function ($item) use ($search, $searchId, $typeFilter) {
            if ($searchId !== '' && (string)$item->id !== $searchId) {
                return false;
            }
            if ($search !== '') {
                $nameMatch = stripos($item->name, $search) !== false;
                if (!$nameMatch) return false;
            }
            if ($typeFilter === 'system' && (int)$item->id > 5) {
                return false;
            }
            if ($typeFilter === 'custom' && (int)$item->id <= 5) {
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
                'html' => view('master.role.role_rows', [
                    'rows' => $pageRows,
                    'userCounts' => $userCounts
                ])->render(),
                'pagination' => $pagination,
                'kpis' => $kpis
            ]);
        }

        return view('master.role.add', [
            'rows' => $pageRows,
            'role' => $allRoles,
            'userCounts' => $userCounts,
            'pagination' => $pagination,
            'kpis' => $kpis,
            'search' => $search,
            'searchId' => $searchId,
            'typeFilter' => $typeFilter,
            'perPage' => $perPage,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $add_pr = Role::find($id);
        if ($request->isMethod('post')) {
            $request->validate([
                'role' => 'required',
            ]);

            $add_pr->user_id = Session::get('id');
            $add_pr->session_id = Session::get('session_id');
            $add_pr->branch_id = Session::get('branch_id');
            $add_pr->name = trim($request->role);
            $add_pr->save();

            return response()->json(['status' => 'success', 'message' => 'Role Edited Successfully.', 'redirect' => url('role_add')]);
        }
        return view('master.role.edit', ['add_pr' => $add_pr]);
    }

    public function delete(Request $request)
    {
        $id = $request->delete_id;
        if ((int)$id <= 5) {
            return redirect::to('role_add')->with('error', 'Core system roles cannot be deleted.');
        }
        Role::find($id)->delete();
        return redirect::to('role_add')->with('message', 'Role Deleted Successfully.');
    }

    public function role_permission(Request $request, $role_id)
    {
        $permissionTypes = ['add', 'edit', 'view', 'delete', 'status', 'print'];

        if ($request->isMethod('post')) {
            $modules = $request->modules ?? [];
            $subModules = $request->sub_modules ?? [];

            foreach ($modules as $moduleId => $permTypes) {
                $data = [
                    'sidebar_name' => DB::table('sidebars')->where('id', $moduleId)->value('name'),
                    'updated_at' => now(),
                    'role_id' => $role_id,
                    'sidebar_id' => $moduleId
                ];

                foreach ($permissionTypes as $type) {
                    $data[$type] = in_array($type, $permTypes) ? 1 : 0;
                }

                $subSelected = $subModules[$moduleId] ?? [];
                $data['sub_sidebar_id'] = !empty($subSelected) ? implode(',', $subSelected) : null;

                DB::table('role_permissions')->updateOrInsert(
                    ['role_id' => $role_id, 'sidebar_id' => $moduleId],
                    $data
                );
            }

            return response()->json(['status' => 'success', 'message' => 'Role Permissions saved successfully!']);
        }

        $modules = DB::table('sidebars')->whereNull('deleted_at')->get();
        $subs = DB::table('sidebar_sub')->whereNull('deleted_at')->get()->groupBy('sidebar_id');
        $rolePermissions = DB::table('role_permissions')->where('role_id', $role_id)->get()->keyBy('sidebar_id');

        return view('master.role.permissions', compact('modules', 'subs', 'rolePermissions', 'role_id'));
    }
}