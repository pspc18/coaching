<?php

namespace App\Http\Controllers;

use App\Models\AttendanceMarkingWindow;
use App\Models\User;
use App\Models\UserAttendanceMarkingWindow;
use Helper;
use Illuminate\Http\Request;
use Session;

class AttendanceMarkingWindowController extends Controller
{
    public function index(Request $request)
    {
        $branchId = (int) Session::get('branch_id');
        $sessionId = (int) Session::get('session_id');
        $classes = Helper::ClassType();
        $activeTab = $request->input('tab') === 'users' || $request->input('form_type') === 'users' ? 'users' : 'classes';
        $windows = AttendanceMarkingWindow::where('branch_id', $branchId)
            ->where('session_id', $sessionId)
            ->get()
            ->keyBy('class_type_id');
        $users = User::with('roleName')->where('branch_id', $branchId)->where('session_id', $sessionId)
            ->where('status', 1)->whereNotIn('role_id', [1, 3])->orderBy('first_name')->orderBy('id')->get();
        $userWindows = UserAttendanceMarkingWindow::where('branch_id', $branchId)
            ->where('session_id', $sessionId)->get()->keyBy('user_id');

        if ($request->isMethod('post')) {
            if ($request->input('form_type') === 'users') {
                $request->validate([
                    'user_windows' => ['nullable', 'array'],
                    'user_windows.*.user_id' => ['required', 'integer'],
                    'user_windows.*.from_time' => ['nullable', 'date_format:H:i'],
                    'user_windows.*.to_time' => ['nullable', 'date_format:H:i'],
                    'user_windows.*.notes' => ['nullable', 'string', 'max:1000'],
                    'user_windows.*.is_active' => ['nullable', 'in:0,1'],
                ]);
                $allowedUserIds = $users->pluck('id')->map(fn ($id) => (int) $id)->all();
                \Illuminate\Support\Facades\DB::transaction(function () use ($request, $allowedUserIds, $branchId, $sessionId) {
                    foreach ((array) $request->input('user_windows', []) as $windowData) {
                        $targetUserId = (int) ($windowData['user_id'] ?? 0);
                        $fromTime = trim((string) ($windowData['from_time'] ?? ''));
                        $toTime = trim((string) ($windowData['to_time'] ?? ''));
                        if (!in_array($targetUserId, $allowedUserIds, true) || $fromTime === '' || $toTime === '') continue;
                        UserAttendanceMarkingWindow::updateOrCreate(
                            ['branch_id' => $branchId, 'session_id' => $sessionId, 'user_id' => $targetUserId],
                            [
                                'configured_by' => Session::get('id'),
                                'from_time' => $fromTime,
                                'to_time' => $toTime,
                                'is_active' => !empty($windowData['is_active']) ? 1 : 0,
                                'notes' => $windowData['notes'] ?? null,
                            ]
                        );
                    }
                });
                return redirect()->to($request->url().'?tab=users')->with('message', 'User marking windows saved successfully.');
            }

            $request->validate([
                'windows' => ['nullable', 'array'],
                'windows.*.class_type_id' => ['required', 'integer'],
                'windows.*.from_time' => ['nullable', 'date_format:H:i'],
                'windows.*.to_time' => ['nullable', 'date_format:H:i'],
                'windows.*.notes' => ['nullable', 'string', 'max:1000'],
                'windows.*.is_active' => ['nullable', 'in:0,1'],
            ]);

            \Illuminate\Support\Facades\DB::transaction(function () use ($request, $branchId, $sessionId) {
                foreach ((array) $request->input('windows', []) as $windowData) {
                    $classTypeId = (int) ($windowData['class_type_id'] ?? 0);
                    $fromTime = trim((string) ($windowData['from_time'] ?? ''));
                    $toTime = trim((string) ($windowData['to_time'] ?? ''));
                    if ($classTypeId <= 0 || $fromTime === '' || $toTime === '') {
                        continue;
                    }

                    AttendanceMarkingWindow::updateOrCreate(
                        [
                            'branch_id' => $branchId,
                            'session_id' => $sessionId,
                            'class_type_id' => $classTypeId,
                        ],
                        [
                            'user_id' => Session::get('id'),
                            'from_time' => $fromTime,
                            'to_time' => $toTime,
                            'is_active' => !empty($windowData['is_active']) ? 1 : 0,
                            'notes' => $windowData['notes'] ?? null,
                        ]
                    );
                }
            });

            return redirect()
                ->to($request->url().'?tab=classes')
                ->with('message', 'Attendance marking windows saved successfully.');
        }

        $classConfigured = $windows->count();
        $classActiveCount = $windows->where('is_active', 1)->count();
        $userConfigured = $userWindows->count();
        $userActiveCount = $userWindows->where('is_active', 1)->count();

        return Helper::view('attendance.marking_window', compact(
            'classes', 'windows', 'users', 'userWindows', 'activeTab',
            'classConfigured', 'classActiveCount', 'userConfigured', 'userActiveCount'
        ));
    }
}
