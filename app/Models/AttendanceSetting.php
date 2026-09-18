<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    protected $table = 'attendance_settings';

    protected $fillable = [
        'branch_id',
        'session_id',
        'user_id',
        'attendance_type',
        'messaging_services',
        'manual_attendance_messaging_enabled',
        'auto_absent_mark_enabled',
        'auto_absent_mark_time',
        'allow_back_date_attendance',
        'late_grace_minutes',
        'qr_validity_minutes',
        'early_out_grace_minutes',
        'half_day_min_minutes',
        'summer_start_time',
        'summer_end_time',
        'winter_start_time',
        'winter_end_time',
        'summer_lunch_from_time',
        'summer_lunch_to_time',
        'winter_lunch_from_time',
        'winter_lunch_to_time',
        'notes',
    ];
}
