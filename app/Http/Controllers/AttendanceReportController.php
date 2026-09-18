<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admission;
use App\Models\AttendanceMark;
use App\Models\User;
use App\Models\Role;
use Session;
use Helper;

class AttendanceReportController extends Controller
{
    private function normalizeReportMode(?string $mode): string
    {
        $mode = (string) $mode;
        return in_array($mode, ['monthly', 'day_wise'], true) ? $mode : 'day_wise';
    }

    private function statusLabel(string $status): string
    {
        return ucwords(str_replace('_', ' ', $status));
    }

    private function emptyStatusCounts(): array
    {
        return ['in' => 0, 'out' => 0, 'absent' => 0, 'halfday' => 0, 'holiday' => 0, 'total' => 0];
    }

    private function reportStatus(?string $status): ?string
    {
        $status = strtolower(trim((string) $status));
        return match ($status) {
            'in', 'present', 'late' => 'in',
            'out', 'early_out' => 'out',
            'absent', 'leave' => 'absent',
            'halfday', 'half_day' => 'halfday',
            'holiday' => 'holiday',
            default => null,
        };
    }

    private function exportCsv(
        string $activeTab,
        string $reportMode,
        string $dateFrom,
        string $dateTo,
        array $rows,
        array $totals,
        array $dateWiseSummary
    ) {
        $fileName = 'attendance-report-' . $activeTab . '-' . $reportMode . '-' . $dateFrom . '.csv';

        return response()->streamDownload(function () use ($activeTab, $reportMode, $dateFrom, $dateTo, $rows, $totals, $dateWiseSummary) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['Attendance Report']);
            fputcsv($out, ['Entity Type', $activeTab === 'staff' ? 'Staff' : 'Students']);
            fputcsv($out, ['Mode', $reportMode === 'monthly' ? 'Monthly' : 'Day Wise']);
            fputcsv($out, ['From Date', $dateFrom]);
            fputcsv($out, ['To Date', $dateTo]);
            fputcsv($out, []);

            fputcsv($out, ['S.No.', 'Unique ID', 'Name', 'In', 'Out', 'Absent', 'Half Day', 'Holiday', 'Total']);
            foreach ($rows as $index => $row) {
                fputcsv($out, [
                    $index + 1,
                    $row['unique_id'],
                    $row['name'],
                    $row['counts']['in'],
                    $row['counts']['out'],
                    $row['counts']['absent'],
                    $row['counts']['halfday'],
                    $row['counts']['holiday'],
                    $row['counts']['total'],
                ]);
            }

            fputcsv($out, []);
            fputcsv($out, ['Overall Totals', 'Count']);
            foreach ($totals as $status => $count) {
                $label = $status === 'total' ? 'Total' : $this->statusLabel($status);
                fputcsv($out, [$label, $count]);
            }

            fputcsv($out, []);
            fputcsv($out, ['Date-wise Summary']);
            fputcsv($out, ['Date', 'In', 'Out', 'Absent', 'Half Day', 'Holiday', 'Total']);
            foreach ($dateWiseSummary as $day => $counts) {
                fputcsv($out, [
                    $day,
                    $counts['in'],
                    $counts['out'],
                    $counts['absent'],
                    $counts['halfday'],
                    $counts['holiday'],
                    $counts['total'],
                ]);
            }

            fclose($out);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function resolveStudentUniqueId($student)
    {
        $attendanceId = trim((string) ($student->attendance_unique_id ?? ''));
        if ($attendanceId !== '') {
            return $attendanceId;
        }

        $admissionNo = trim((string) ($student->admissionNo ?? ''));
        if ($admissionNo !== '') {
            return $admissionNo;
        }

        return 'STU-' . $student->id;
    }

    private function resolveStaffUniqueId($member)
    {
        $attendanceId = trim((string) ($member->attendance_unique_id ?? ''));
        return $attendanceId !== '' ? $attendanceId : ('USR-' . $member->id);
    }

    private function resolveStaffAliases($member)
    {
        $primary = $this->resolveStaffUniqueId($member);
        $legacy = 'USR-' . $member->id;

        return array_values(array_unique([$primary, $legacy]));
    }

    public function index(Request $request)
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $canAccessStaffAttendance = (int) Session::get('role_id') === 1;

        $activeTab = $canAccessStaffAttendance && $request->tab === 'staff' ? 'staff' : 'students';
        $reportMode = $this->normalizeReportMode($request->report_mode);

        $selectedDate = $request->date ? date('Y-m-d', strtotime((string) $request->date)) : date('Y-m-d');
        $month = (int) ($request->month ?? date('n'));
        $year = (int) ($request->year ?? date('Y'));
        if ($month < 1 || $month > 12) {
            $month = (int) date('n');
        }
        if ($year < 2000 || $year > 2100) {
            $year = (int) date('Y');
        }

        if ($reportMode === 'monthly') {
            $dateFrom = date('Y-m-01', strtotime($year . '-' . $month . '-01'));
            $dateTo = date('Y-m-t', strtotime($dateFrom));
            $selectedDate = $dateFrom;
        } else {
            $dateFrom = $selectedDate;
            $dateTo = $selectedDate;
            $month = (int) date('n', strtotime($selectedDate));
            $year = (int) date('Y', strtotime($selectedDate));
        }

        $roleId = (int) Session::get('role_id');
        $classes = Helper::classType();
        $allowedClassIds = $classes->pluck('id')->map(function ($id) {
            return (int) $id;
        })->all();
        $classFilter = $request->class_type_id ?? '';
        if ($roleId > 1 && !empty($allowedClassIds) && $classFilter !== '' && !in_array((int) $classFilter, $allowedClassIds, true)) {
            abort(403, 'You are not assigned to the selected class.');
        }

        $studentsQuery = Admission::select('id', 'attendance_unique_id', 'admissionNo', 'first_name', 'last_name', 'class_type_id')
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('status', 1);

        if (!empty($allowedClassIds) && $roleId > 1) {
            $studentsQuery->whereIn('class_type_id', $allowedClassIds);
        }

        $students = $studentsQuery->orderBy('first_name')->get();

        $studentMap = [];
        foreach ($students as $stu) {
            $uid = $this->resolveStudentUniqueId($stu);
            $studentMap[$uid] = [
                'name' => trim($stu->first_name . ' ' . $stu->last_name),
                'class_type_id' => $stu->class_type_id,
                'unique_id' => $uid,
                'aliases' => [$uid],
            ];
        }

        $staff = User::select('id', 'attendance_unique_id', 'first_name', 'last_name', 'role_id')
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('status', 1)
            ->where('role_id', '!=', 3)
            ->when(!$canAccessStaffAttendance, function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->orderBy('first_name')
            ->get();

        $staffRoles = Role::whereIn('id', $staff->pluck('role_id')->unique()->values())->orderBy('name')->get();

        $staffMap = [];
        foreach ($staff as $member) {
            $uid = $this->resolveStaffUniqueId($member);
            $staffMap[$uid] = [
                'name' => trim($member->first_name . ' ' . $member->last_name),
                'role_id' => $member->role_id,
                'unique_id' => $uid,
                'aliases' => $this->resolveStaffAliases($member),
            ];
        }

        $query = AttendanceMark::where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->whereBetween('date', [$dateFrom, $dateTo]);

        if ($activeTab === 'staff') {
            $query->where('entity_type', 'staff');
        } else {
            $query->where('entity_type', 'student');
        }

        $marks = $query->get();

        if ($activeTab === 'staff' && $marks->isEmpty()) {
            $marks = AttendanceMark::where('branch_id', $branchId)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->where('entity_type', 'staff')
                ->get();
        }

        $roleFilter = $request->role_id ?? '';

        $rows = [];
        $totals = $this->emptyStatusCounts();

        if ($activeTab === 'staff') {
            $matchedUniqueIds = [];
            $marksByUnique = $marks->groupBy('unique_id');

            foreach ($staffMap as $info) {
                if (!empty($roleFilter) && (string) $info['role_id'] !== (string) $roleFilter) {
                    continue;
                }

                $counts = $this->emptyStatusCounts();

                $entries = $marks->whereIn('unique_id', $info['aliases'])->groupBy('date')->map(function ($group) {
                    return $group->sortByDesc('updated_at')->first();
                });

                foreach ($info['aliases'] as $aliasId) {
                    if (isset($marksByUnique[$aliasId])) {
                        $matchedUniqueIds[$aliasId] = true;
                    }
                }

                foreach ($entries as $entry) {
                    $status = $this->reportStatus($entry->status ?? '');
                    if ($status !== null) {
                        $counts[$status]++;
                        $counts['total']++;
                    }
                }

                $rows[] = [
                    'unique_id' => $info['unique_id'],
                    'name' => $info['name'],
                    'counts' => $counts,
                ];

                foreach ($totals as $k => $v) {
                    $totals[$k] += $counts[$k];
                }
            }

            if (empty($roleFilter)) {
                foreach ($marksByUnique as $uid => $entriesRaw) {
                    if (isset($matchedUniqueIds[$uid])) {
                        continue;
                    }

                    $counts = $this->emptyStatusCounts();

                    $entries = $entriesRaw->groupBy('date')->map(function ($group) {
                        return $group->sortByDesc('updated_at')->first();
                    });

                    foreach ($entries as $entry) {
                        $status = $this->reportStatus($entry->status ?? '');
                        if ($status !== null) {
                            $counts[$status]++;
                            $counts['total']++;
                        }
                    }

                    $legacyUser = User::select('first_name', 'last_name')->where('attendance_unique_id', $uid)->first();
                    $legacyName = $legacyUser ? trim(($legacyUser->first_name ?? '') . ' ' . ($legacyUser->last_name ?? '')) : '';
                    if ($legacyName === '') {
                        $legacyName = 'Legacy Staff (' . $uid . ')';
                    }

                    $rows[] = [
                        'unique_id' => $uid,
                        'name' => $legacyName,
                        'counts' => $counts,
                    ];

                    foreach ($totals as $k => $v) {
                        $totals[$k] += $counts[$k];
                    }
                }
            }
        } else {
            foreach ($studentMap as $info) {
                if (!empty($classFilter) && (string) $info['class_type_id'] !== (string) $classFilter) {
                    continue;
                }

                $counts = $this->emptyStatusCounts();

                $entries = $marks->whereIn('unique_id', $info['aliases'])->groupBy('date')->map(function ($group) {
                    return $group->sortByDesc('updated_at')->first();
                });

                foreach ($entries as $entry) {
                    $status = $this->reportStatus($entry->status ?? '');
                    if ($status !== null) {
                        $counts[$status]++;
                        $counts['total']++;
                    }
                }

                $rows[] = [
                    'unique_id' => $info['unique_id'],
                    'name' => $info['name'],
                    'counts' => $counts,
                ];

                foreach ($totals as $k => $v) {
                    $totals[$k] += $counts[$k];
                }
            }
        }

        $countsTemplate = $this->emptyStatusCounts();
        $dateWiseSummary = [];
        $cursor = strtotime($dateFrom);
        $endTs = strtotime($dateTo);
        while ($cursor <= $endTs) {
            $dateWiseSummary[date('Y-m-d', $cursor)] = $countsTemplate;
            $cursor = strtotime('+1 day', $cursor);
        }

        $summaryMarks = $marks;

        if ($activeTab === 'staff') {
            if (!empty($roleFilter)) {
                $allowed = [];
                foreach ($staffMap as $info) {
                    if ((string) $info['role_id'] !== (string) $roleFilter) {
                        continue;
                    }
                    $allowed = array_merge($allowed, $info['aliases']);
                }
                $allowed = array_values(array_unique($allowed));
                $summaryMarks = !empty($allowed) ? $marks->whereIn('unique_id', $allowed) : collect();
            }
        } else {
            $allowed = [];
            foreach ($studentMap as $info) {
                if (!empty($classFilter) && (string) $info['class_type_id'] !== (string) $classFilter) {
                    continue;
                }
                $allowed = array_merge($allowed, $info['aliases']);
            }
            $allowed = array_values(array_unique($allowed));
            $summaryMarks = !empty($allowed) ? $marks->whereIn('unique_id', $allowed) : collect();
        }

        $expectedEntity = $activeTab === 'staff' ? 'staff' : 'student';
        $summaryMarks = $summaryMarks->filter(function ($mark) use ($expectedEntity) {
            return (string) ($mark->entity_type ?? '') === $expectedEntity;
        });

        $dedupedMarks = $summaryMarks->groupBy(function ($mark) {
            return (string) $mark->unique_id . '|' . (string) $mark->date;
        })->map(function ($group) {
            return $group->sortByDesc('updated_at')->first();
        });

        foreach ($dedupedMarks as $entry) {
            $dateKey = (string) $entry->date;
            if (!isset($dateWiseSummary[$dateKey])) {
                continue;
            }
            $status = $this->reportStatus($entry->status ?? '');
            if ($status !== null && isset($dateWiseSummary[$dateKey][$status])) {
                $dateWiseSummary[$dateKey][$status]++;
                $dateWiseSummary[$dateKey]['total']++;
            }
        }

        if ((int) $request->input('export', 0) === 1) {
            return $this->exportCsv($activeTab, $reportMode, $dateFrom, $dateTo, $rows, $totals, $dateWiseSummary);
        }

        return Helper::view('attendance.report', compact(
            'activeTab',
            'reportMode',
            'dateFrom',
            'dateTo',
            'selectedDate',
            'classes',
            'students',
            'staff',
            'staffRoles',
            'rows',
            'classFilter',
            'roleFilter',
            'month',
            'year',
            'totals',
            'dateWiseSummary',
            'canAccessStaffAttendance'
        ));
    }
}
