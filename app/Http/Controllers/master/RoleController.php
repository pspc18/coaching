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
            $syncToUsers = $request->input('sync_to_users', 1);

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

            // Sync to existing users assigned to this role based on sync_mode
            $syncMode = $request->input('sync_mode', $request->input('sync_to_users') ? 'default_only' : 'none');
            $syncedCount = 0;
            $skippedCount = 0;

            if ($syncMode !== 'none') {
                $allRoleUserIds = DB::table('users')->where('role_id', $role_id)->whereNull('deleted_at')->pluck('id')->toArray();

                $customUserIds = DB::table('user_permission')
                    ->whereIn('user_id', $allRoleUserIds)
                    ->pluck('user_id')
                    ->unique()
                    ->toArray();

                if ($syncMode === 'default_only') {
                    $targetUserIds = array_values(array_diff($allRoleUserIds, $customUserIds));
                    $skippedCount = count($customUserIds);
                } else {
                    $targetUserIds = $allRoleUserIds;
                }

                $syncedCount = count($targetUserIds);

                foreach ($targetUserIds as $uid) {
                    foreach ($modules as $moduleId => $permTypes) {
                        $userRow = [
                            'sidebar_name'   => DB::table('sidebars')->where('id', $moduleId)->value('name') ?? '',
                            'updated_at'     => now(),
                            'deleted_at'     => null,
                            'sub_sidebar_id' => !empty($subModules[$moduleId] ?? []) ? implode(',', $subModules[$moduleId]) : null
                        ];

                        foreach ($permissionTypes as $type) {
                            $userRow[$type] = in_array($type, $permTypes) ? 1 : 0;
                        }

                        DB::table('user_permission')->updateOrInsert(
                            ['user_id' => $uid, 'sidebar_id' => $moduleId],
                            $userRow
                        );
                    }
                }
            }

            $successMsg = 'Role permissions saved successfully!';
            if ($syncMode === 'default_only' && $syncedCount > 0) {
                $successMsg .= " Synced to {$syncedCount} default staff" . ($skippedCount > 0 ? " (skipped {$skippedCount} staff with custom permissions)." : ".");
            } elseif ($syncMode === 'all' && $syncedCount > 0) {
                $successMsg .= " Synced to all {$syncedCount} staff members.";
            }

            if ($request->ajax()) {
                return response()->json(['status' => 'success', 'message' => $successMsg]);
            }

            return redirect()->back()->with('message', $successMsg);
        }

        $role = DB::table('role')->where('id', $role_id)->first();
        $users = DB::table('users')
            ->where('role_id', $role_id)
            ->whereNull('deleted_at')
            ->select('id', 'first_name', 'userName', 'mobile')
            ->orderBy('first_name', 'ASC')
            ->get();

        $customUserIds = DB::table('user_permission')
            ->whereIn('user_id', $users->pluck('id'))
            ->pluck('user_id')
            ->unique()
            ->toArray();

        $modules = DB::table('sidebars')->whereNull('deleted_at')->get();
        $subs = DB::table('sidebar_sub')->whereNull('deleted_at')->get()->groupBy('sidebar_id');
        $rolePermissions = DB::table('role_permissions')->where('role_id', $role_id)->get()->keyBy('sidebar_id');

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'role' => $role,
                'html' => view('master.role.permissions_partial', compact('role', 'users', 'modules', 'subs', 'rolePermissions', 'role_id', 'permissionTypes', 'customUserIds'))->render()
            ]);
        }

        return view('master.role.permissions', compact('role', 'users', 'modules', 'subs', 'rolePermissions', 'role_id', 'permissionTypes', 'customUserIds'));
    }

    public function user_permission_data(Request $request, $user_id)
    {
        $user = DB::table('users')->where('id', $user_id)->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        $permissions = DB::table('user_permission')->where('user_id', $user_id)->get()->keyBy('sidebar_id');
        $rolePermissions = DB::table('role_permissions')->where('role_id', $user->role_id)->get()->keyBy('sidebar_id');

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'permissions' => $permissions,
            'role_permissions' => $rolePermissions
        ]);
    }

    public function user_permission_save(Request $request, $user_id)
    {
        $permissionTypes = ['add', 'edit', 'view', 'delete', 'status', 'print'];
        $modules = $request->modules ?? [];
        $subModules = $request->sub_modules ?? [];

        foreach ($modules as $moduleId => $permTypes) {
            $data = [
                'sidebar_name' => DB::table('sidebars')->where('id', $moduleId)->value('name') ?? '',
                'updated_at' => now(),
                'user_id' => $user_id,
                'sidebar_id' => $moduleId,
                'deleted_at' => null
            ];

            foreach ($permissionTypes as $type) {
                $data[$type] = in_array($type, $permTypes) ? 1 : 0;
            }

            $subSelected = $subModules[$moduleId] ?? [];
            $data['sub_sidebar_id'] = !empty($subSelected) ? implode(',', $subSelected) : null;

            DB::table('user_permission')->updateOrInsert(
                ['user_id' => $user_id, 'sidebar_id' => $moduleId],
                $data
            );
        }

        return response()->json(['status' => 'success', 'message' => 'User-specific permissions saved successfully!']);
    }
}