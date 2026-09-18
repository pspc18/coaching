<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceMark extends Model
{
    protected $table = 'attendance_marks';

    protected $fillable = [
        'unique_id',
        'entity_type',
        'date',
        'in_time',
        'out_time',
        'status',
        'branch_id',
        'session_id',
        'created_by',
    ];

    protected static function booted()
    {
        static::saved(function ($mark) {
            \App\Helpers\helper::clearAttendanceCache($mark->branch_id ?? null, $mark->session_id ?? null);
            \App\Helpers\helper::clearDashboardCache($mark->branch_id ?? null, $mark->session_id ?? null);
        });
        static::deleted(function ($mark) {
            \App\Helpers\helper::clearAttendanceCache($mark->branch_id ?? null, $mark->session_id ?? null);
            \App\Helpers\helper::clearDashboardCache($mark->branch_id ?? null, $mark->session_id ?? null);
        });
    }
}
