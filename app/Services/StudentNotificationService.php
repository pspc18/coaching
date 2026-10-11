<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;

class StudentNotificationService
{
    private const ATTENDANCE_TYPES = ['attendance', 'attendance_marked', 'student'];
    private const FEE_TYPES = ['fee_payment', 'fee_revert'];
    private const RESULT_TYPES = ['exam_result'];
    private const NOTICE_TYPES = ['notice'];
    private const COMPLAINT_TYPES = ['complaint_reply', 'complaint_status'];

    private const CATEGORY_LABELS = [
        'all' => 'All',
        'attendance' => 'Attendance',
        'fees' => 'Fees',
        'result' => 'Results',
        'notice' => 'Notices',
        'complaint' => 'Complaints',
    ];

    public function allowedCategories(): array
    {
        return array_keys(self::CATEGORY_LABELS);
    }

    public function categoryLabel(string $category): string
    {
        return self::CATEGORY_LABELS[$category] ?? self::CATEGORY_LABELS['all'];
    }

    public function baseQuery(int $admissionId, bool $includeHidden = false): Builder
    {
        $query = Notification::query()
            ->where('admission_id', $admissionId)
            ->where(function (Builder $query) {
                $query->where('title', 'Attendance Marked')
                    ->orWhereIn('type', array_merge(
                        self::ATTENDANCE_TYPES,
                        self::FEE_TYPES,
                        self::RESULT_TYPES,
                        self::NOTICE_TYPES,
                        self::COMPLAINT_TYPES
                    ));
            });

        if (!$includeHidden) {
            $query->where('show_status', 1);
        }

        return $query;
    }

    public function applyCategory(Builder $query, string $category): Builder
    {
        switch ($category) {
            case 'attendance':
                return $query->where(function (Builder $query) {
                    $query->where('title', 'Attendance Marked')
                        ->orWhereIn('type', self::ATTENDANCE_TYPES);
                });

            case 'fees':
                return $query->whereIn('type', self::FEE_TYPES);

            case 'result':
                return $query->whereIn('type', self::RESULT_TYPES);

            case 'notice':
                return $query->whereIn('type', self::NOTICE_TYPES);

            case 'complaint':
                return $query->whereIn('type', self::COMPLAINT_TYPES);

            default:
                return $query;
        }
    }

    public function counts(int $admissionId, bool $unreadOnly = true): array
    {
        $query = $this->baseQuery($admissionId);
        if ($unreadOnly) {
            $query->where('message_seen', 0);
        }

        $row = $query
            ->selectRaw("
                COUNT(*) as all_count,
                SUM(CASE WHEN (title = 'Attendance Marked' OR type IN ('attendance', 'attendance_marked', 'student')) THEN 1 ELSE 0 END) as attendance_count,
                SUM(CASE WHEN type IN ('fee_payment', 'fee_revert') THEN 1 ELSE 0 END) as fees_count,
                SUM(CASE WHEN type = 'exam_result' THEN 1 ELSE 0 END) as result_count,
                SUM(CASE WHEN type = 'notice' THEN 1 ELSE 0 END) as notice_count,
                SUM(CASE WHEN type IN ('complaint_reply', 'complaint_status') THEN 1 ELSE 0 END) as complaint_count
            ")
            ->first();

        return [
            'all' => (int) ($row->all_count ?? 0),
            'attendance' => (int) ($row->attendance_count ?? 0),
            'fees' => (int) ($row->fees_count ?? 0),
            'result' => (int) ($row->result_count ?? 0),
            'notice' => (int) ($row->notice_count ?? 0),
            'complaint' => (int) ($row->complaint_count ?? 0),
        ];
    }

    public function normalizeCategoryFromNotification(?string $type, ?string $title = null): string
    {
        $type = strtolower(trim((string) $type));
        $title = strtolower(trim((string) $title));

        if ($title === strtolower('Attendance Marked') || in_array($type, self::ATTENDANCE_TYPES, true)) {
            return 'attendance';
        }

        if (in_array($type, self::FEE_TYPES, true)) {
            return 'fees';
        }

        if (in_array($type, self::RESULT_TYPES, true)) {
            return 'result';
        }

        if (in_array($type, self::NOTICE_TYPES, true)) {
            return 'notice';
        }

        if (in_array($type, self::COMPLAINT_TYPES, true)) {
            return 'complaint';
        }

        return 'all';
    }
}
