<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceMarkingWindow extends Model
{
    protected $table = 'attendance_marking_windows';

    protected $fillable = [
        'branch_id',
        'session_id',
        'user_id',
        'class_type_id',
        'from_time',
        'to_time',
        'is_active',
        'notes',
    ];
}
