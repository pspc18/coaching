<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAttendanceMarkingWindow extends Model
{
    protected $table = 'user_attendance_marking_windows';

    protected $fillable = [
        'branch_id', 'session_id', 'user_id', 'configured_by',
        'from_time', 'to_time', 'is_active', 'notes',
    ];
}
