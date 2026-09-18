<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollSetting extends Model
{
    protected $table = 'payroll_settings';

    protected $fillable = [
        'branch_id',
        'session_id',
        'created_by',
        'paid_leave_limit',
        'present_weight',
        'late_weight',
        'late_frequency',
        'early_out_weight',
        'early_out_frequency',
        'halfday_weight',
        'leave_weight',
        'holiday_weight',
        'absent_weight',
    ];
}
